<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AdoptionStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateAdoptionStatusRequest;
use App\Http\Resources\AdminAdoptionRequestResource;
use App\Models\AdoptionRequest;
use App\Models\Cat;
use App\Support\Paginated;
use App\Support\QueryText;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

/**
 * The admin side of adoption requests: the tabbed list, one request with its valid IDs, and
 * the decisions (approve, reject, release, reopen).
 */
class AdoptionRequestController extends Controller
{
    private const TABS = ['pending', 'approved', 'rejected', 'released', 'all'];

    public function index(Request $request): Response
    {
        $tab = in_array($request->query('status'), self::TABS, true) ? $request->query('status') : 'pending';
        $search = QueryText::get($request, 'q');
        // Pending requests are worked first come, first served; the other tabs show the latest first.
        $sort = in_array($request->query('sort'), ['newest', 'oldest'], true)
            ? $request->query('sort')
            : ($tab === 'pending' ? 'oldest' : 'newest');

        $requests = AdoptionRequest::query()
            ->with(['cat' => fn ($cat) => $cat->withPendingRequestCount()->withExists([
                'adoptionRequests as is_reserved' => fn (Builder $requests) => $requests->whereIn('status', AdoptionStatus::reservingCat()),
            ])])
            ->when($tab !== 'all', fn (Builder $query) => $query->where('status', AdoptionStatus::from(ucfirst($tab))))
            ->when($search !== '', function (Builder $query) use ($search) {
                $term = QueryText::like($search);
                $query->where(fn (Builder $match) => $match
                    ->whereRaw("LOWER(name) LIKE ? ESCAPE '!'", [$term])
                    ->orWhereRaw("LOWER(email) LIKE ? ESCAPE '!'", [$term])
                    ->orWhereRaw("LOWER(name_of_cat) LIKE ? ESCAPE '!'", [$term]));
            })
            ->orderBy('created_at', $sort === 'oldest' ? 'asc' : 'desc')
            ->orderBy('id', $sort === 'oldest' ? 'asc' : 'desc')
            ->paginate(15);

        $counts = AdoptionRequest::query()->selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status');

        return Inertia::render('Admin/Requests/Index', [
            'requests' => Paginated::make($requests, AdminAdoptionRequestResource::class),
            'filters' => ['status' => $tab, 'q' => $search, 'sort' => $sort],
            'counts' => [
                ...collect(AdoptionStatus::cases())->mapWithKeys(fn (AdoptionStatus $status) => [
                    strtolower($status->value) => (int) ($counts[$status->value] ?? 0),
                ]),
                'all' => (int) $counts->sum(),
            ],
        ]);
    }

    public function show(AdoptionRequest $adoptionRequest): Response
    {
        $adoptionRequest->load(['user', 'cat' => fn ($cat) => $cat->withPendingRequestCount()->withExists([
            'adoptionRequests as is_reserved' => fn (Builder $requests) => $requests->whereIn('status', AdoptionStatus::reservingCat()),
        ])]);

        $others = $adoptionRequest->cat_id === null ? collect() : AdoptionRequest::where('cat_id', $adoptionRequest->cat_id)
            ->whereKeyNot($adoptionRequest->getKey())
            ->latest('id')
            ->get();

        $user = $adoptionRequest->user;
        $status = $adoptionRequest->status ?? AdoptionStatus::Pending;

        return Inertia::render('Admin/Requests/Show', [
            'request' => (new AdminAdoptionRequestResource($adoptionRequest))->resolve(),
            'validIds' => collect($adoptionRequest->valid_id ?: [])->values()->map(fn (string $file, int $index) => [
                'label' => 'ID '.($index + 1),
                'url' => route('validIdFile', ['filename' => basename($file)]),
            ]),
            'account' => $user ? [
                'memberSince' => $user->created_at?->format('M Y'),
                'otherRequests' => $user->adoptionRequests()->whereKeyNot($adoptionRequest->getKey())->count(),
            ] : null,
            'otherRequests' => $others->map(fn (AdoptionRequest $other) => [
                'id' => $other->id,
                'name' => $other->name,
                'sentAt' => $other->created_at?->format('M j, Y'),
                'pickupDate' => $other->date_of_adoption?->format('M j, Y'),
                'statusKey' => strtolower(($other->status ?? AdoptionStatus::Pending)->value),
                'url' => route('admin.requests.show', $other),
            ]),
            // Each move the admin could make from here, and why it's blocked if it is.
            'actions' => collect($status->next())->map(fn (AdoptionStatus $next) => [
                'status' => $next->value,
                'refusal' => $adoptionRequest->statusChangeRefusal($next),
            ]),
            'otherPendingCount' => $others->where('status', AdoptionStatus::Pending)->count(),
            'statusUrl' => route('adoption-request.status', $adoptionRequest),
            'pdfUrl' => route('adoption-request.pdf', $adoptionRequest),
            'listUrl' => route('admin.requests.index', ['status' => strtolower($status->value)]),
        ]);
    }

    /**
     * Approve, reject, release or reopen a request. Only the status changes; the applicant's
     * details stay as they sent them. React pages get a redirect back with a message, JSON
     * callers get the same message as JSON.
     */
    public function updateStatus(UpdateAdoptionStatusRequest $request, AdoptionRequest $adoptionRequest): JsonResponse|RedirectResponse
    {
        $status = $request->enum('status', AdoptionStatus::class);

        $autoRejected = DB::transaction(function () use ($adoptionRequest, $status) {
            // Lock the cat so two admins can't approve two requests for it at the same moment.
            if ($adoptionRequest->cat_id !== null) {
                Cat::whereKey($adoptionRequest->cat_id)->lockForUpdate()->first();
            }
            $adoptionRequest->refresh()->load('cat');

            if (($refusal = $adoptionRequest->statusChangeRefusal($status)) !== null) {
                throw ValidationException::withMessages(['status' => $refusal]);
            }

            $adoptionRequest->status = $status;
            match ($status) {
                AdoptionStatus::Approved => $adoptionRequest->approval_date = now(),
                AdoptionStatus::Released => $adoptionRequest->Release_date = now(),
                // Reopened: it waits for a fresh decision.
                AdoptionStatus::Pending => $adoptionRequest->approval_date = null,
                default => null,
            };
            $adoptionRequest->save();

            // Once a cat goes home, the other applicants still waiting for it get their answer.
            if ($status !== AdoptionStatus::Released || $adoptionRequest->cat_id === null) {
                return 0;
            }

            return AdoptionRequest::where('cat_id', $adoptionRequest->cat_id)
                ->whereKeyNot($adoptionRequest->getKey())
                ->where('status', AdoptionStatus::Pending)
                ->update(['status' => AdoptionStatus::Rejected]);
        });

        $message = $this->statusMessage($adoptionRequest, $status, $autoRejected);

        if ($request->expectsJson() && ! $request->header('X-Inertia')) {
            return response()->json(['success' => true, 'message' => $message, 'auto_rejected' => $autoRejected]);
        }

        return back()->with('success', $message);
    }

    // Serve one uploaded ID to an admin
    public function showValidIdFile(string $filename)
    {
        $filename = basename($filename);

        $disk = Storage::disk(config('filesystems.private_uploads_disk'));
        abort_unless($disk->exists('valid-ids/'.$filename), 404);

        return $disk->response('valid-ids/'.$filename);
    }

    // The adoption contract for one request, as a PDF download
    public function generatePDF(AdoptionRequest $adoptionRequest)
    {
        $pdf = Pdf::loadView('adoptionRequestPDF', ['request' => $adoptionRequest]);

        return $pdf->download('adoption-request-'.$adoptionRequest->id.'-'.Str::slug((string) $adoptionRequest->name_of_cat ?: 'cat').'.pdf');
    }

    // Old URLs, kept so bookmarks still work.
    public function legacyList(): RedirectResponse
    {
        return redirect()->route('admin.requests.index');
    }

    public function legacyReleased(): RedirectResponse
    {
        return redirect()->route('admin.requests.index', ['status' => 'released']);
    }

    public function legacyValidIds(AdoptionRequest $adoptionRequest): RedirectResponse
    {
        return redirect()->route('admin.requests.show', $adoptionRequest);
    }

    private function statusMessage(AdoptionRequest $adoptionRequest, AdoptionStatus $status, int $autoRejected): string
    {
        $who = $adoptionRequest->name ?: 'the applicant';
        $cat = $adoptionRequest->cat?->cat_name ?? $adoptionRequest->name_of_cat ?? 'the cat';

        $message = match ($status) {
            AdoptionStatus::Approved => "Approved {$who} to adopt {$cat}.",
            AdoptionStatus::Rejected => "Rejected {$who}'s request for {$cat}.",
            AdoptionStatus::Released => "{$cat} went home with {$who}.",
            AdoptionStatus::Pending => "Reopened {$who}'s request for {$cat}. It's pending again.",
        };

        if ($autoRejected > 0) {
            $message .= ' '.trans_choice(
                '{1} The other pending request for this cat was rejected automatically.|[2,*] The other :count pending requests for this cat were rejected automatically.',
                $autoRejected
            );
        }

        return $message;
    }
}
