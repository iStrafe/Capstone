<?php

namespace App\Http\Controllers;

use App\Enums\AdoptionStatus;
use App\Http\Resources\CatResource;
use App\Http\Resources\NewsEventResource;
use App\Models\Cat;
use App\Models\NewsEvent;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Public cat pages. Admin inventory management lives in Admin\CatController.
 */
class CatController extends Controller
{
    public function home(): Response
    {
        return Inertia::render('Home', [
            'cats' => CatResource::collection(Cat::available()->latest('id')->get())->resolve(),
            'events' => NewsEventResource::collection(
                NewsEvent::query()->orderByDesc('event_date')->limit(3)->get()
            )->resolve(),
        ]);
    }

    public function index(Request $request): View
    {
        return view('cats.index', [
            'cats' => Cat::available()->withPendingRequestCount()->get(),
            // Cats the signed-in user is already waiting on, so their Adopt button can say so.
            'requestedCatIds' => $request->user()?->adoptionRequests()
                ->whereIn('status', AdoptionStatus::open())
                ->pluck('cat_id')
                ->all() ?? [],
        ]);
    }

    // Cats that are off the adoption list get a page explaining why instead of a 404.
    public function show(Request $request, Cat $cat): Response
    {
        $available = $cat->isAvailable();
        $user = $request->user();

        return Inertia::render('Cats/Show', [
            'cat' => (new CatResource($cat))->resolve(),
            'medicalRecord' => $cat->Medical_Record,
            'status' => $available ? null : $this->statusMessage($cat, $cat->unavailableReason() ?? 'inactive'),
            // What the adopt panel offers this visitor: log in, send a request, or why they can't.
            'adoption' => $available ? [
                'refusal' => $user ? $cat->requestRefusalFor($user) : null,
                'alreadyRequested' => $user !== null && $cat->adoptionRequests()
                    ->where('user_id', $user->getKey())
                    ->whereIn('status', AdoptionStatus::open())
                    ->exists(),
            ] : null,
            'otherCats' => CatResource::collection(
                Cat::available()->whereKeyNot($cat->getKey())->inRandomOrder()->limit(4)->get()
            )->resolve(),
        ]);
    }

    /**
     * @return array{reason: string, title: string, text: string, archiveReason: ?string, archivedOn: ?string}
     */
    private function statusMessage(Cat $cat, string $reason): array
    {
        $name = $cat->cat_name;

        [$title, $text] = match ($reason) {
            'archived' => ["This cat's profile has been archived", $name.' is no longer listed for adoption.'],
            'adopted' => [$name.' has been adopted', 'Good news: '.$name.' has already gone home with a new family, so we are not taking adoption requests for this cat any more.'],
            'reserved' => ['Adoption in progress', 'An adoption request for '.$name.' has been approved and the cat is getting ready to go home, so we are not taking new requests for now.'],
            default => [$name.' is not available right now', $name.' is not open for adoption at the moment. Please check back later or meet the other cats looking for a home.'],
        };

        $archived = $reason === 'archived';

        return [
            'reason' => $reason,
            'title' => $title,
            'text' => $text,
            'archiveReason' => $archived
                ? (filled($cat->archive_reason) ? $cat->archive_reason : 'The shelter has not given a reason for archiving this profile. Contact us if you have questions about this cat.')
                : null,
            'archivedOn' => $archived ? $cat->archived_at?->format('M j, Y') : null,
        ];
    }
}
