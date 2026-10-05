<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AdoptionStatus;
use App\Http\Controllers\Concerns\StoresPublicImages;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CatRequest;
use App\Http\Resources\AdminCatResource;
use App\Models\Cat;
use App\Support\Paginated;
use App\Support\QueryText;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Inertia\Inertia;
use Inertia\Response;

/**
 * The admin cat inventory: Active, Inactive and Archived tabs, and one page to add or edit a cat.
 */
class CatController extends Controller
{
    use StoresPublicImages;

    private const TABS = ['active', 'inactive', 'archived'];

    public function index(Request $request): Response
    {
        $tab = in_array($request->query('status'), self::TABS, true) ? $request->query('status') : 'active';
        $search = QueryText::get($request, 'q');
        $sex = in_array($request->query('sex'), ['Male', 'Female'], true) ? $request->query('sex') : '';

        $cats = $this->withAdminState(Cat::query())
            ->when($tab === 'active', fn (Builder $query) => $query->notArchived()->where('status', Cat::STATUS_ACTIVE))
            ->when($tab === 'inactive', fn (Builder $query) => $query->notArchived()->where('status', '!=', Cat::STATUS_ACTIVE))
            ->when($tab === 'archived', fn (Builder $query) => $query->whereNotNull('archived_at'))
            ->when($search !== '', function (Builder $query) use ($search) {
                $term = QueryText::like($search);
                $query->where(fn (Builder $match) => $match
                    ->whereRaw("LOWER(cat_name) LIKE ? ESCAPE '!'", [$term])
                    ->orWhereRaw("LOWER(color) LIKE ? ESCAPE '!'", [$term])
                    ->orWhereRaw("LOWER(breed) LIKE ? ESCAPE '!'", [$term]));
            })
            ->when($sex !== '', fn (Builder $query) => $query->where('sex', $sex))
            ->orderByDesc($tab === 'archived' ? 'archived_at' : 'updated_at')
            ->orderByDesc('id')
            ->paginate(15);

        return Inertia::render('Admin/Cats/Index', [
            'cats' => Paginated::make($cats, AdminCatResource::class),
            'filters' => ['status' => $tab, 'q' => $search, 'sex' => $sex],
            'counts' => [
                'active' => Cat::notArchived()->where('status', Cat::STATUS_ACTIVE)->count(),
                'inactive' => Cat::notArchived()->where('status', '!=', Cat::STATUS_ACTIVE)->count(),
                'archived' => Cat::whereNotNull('archived_at')->count(),
            ],
            'createUrl' => route('admin.cats.create'),
        ]);
    }

    public function create(Request $request): Response
    {
        return Inertia::render('Admin/Cats/Edit', [
            'cat' => null,
            // The breed helper links here with its guess filled in.
            'defaults' => [
                'breed' => QueryText::get($request, 'breed', 100),
                'color' => QueryText::get($request, 'color', 50),
            ],
            'submitUrl' => route('admin.cats.store'),
            'indexUrl' => route('admin.cats.index'),
            'placeholder' => asset('images/placeholder.png'),
        ]);
    }

    public function store(CatRequest $request): RedirectResponse
    {
        // New cats always start Active.
        $cat = new Cat(Arr::except($request->validated(), ['cat_image', 'cat_clip', 'status']));
        $this->saveWithMedia($cat, $this->uploads($request));

        return redirect()->route('admin.cats.index')->with('success', $cat->cat_name.' was added. The profile is live on the adoption list.');
    }

    public function show(Cat $cat): RedirectResponse
    {
        return redirect()->route('admin.cats.edit', $cat);
    }

    public function edit(Cat $cat): Response
    {
        $cat = $this->withAdminState(Cat::query())->findOrFail($cat->getKey());

        return Inertia::render('Admin/Cats/Edit', [
            'cat' => (new AdminCatResource($cat))->resolve(),
            'defaults' => null,
            'submitUrl' => route('admin.cats.update', $cat),
            'indexUrl' => route('admin.cats.index', $cat->archived_at ? ['status' => 'archived'] : []),
            'placeholder' => asset('images/placeholder.png'),
        ]);
    }

    public function update(CatRequest $request, Cat $cat): RedirectResponse
    {
        $except = ['cat_image', 'cat_clip'];

        // An archived cat stays archived until it's restored.
        if ($cat->archived_at !== null) {
            $except[] = 'status';
        }

        $cat->fill(Arr::except($request->validated(), $except));
        $this->saveWithMedia($cat, $this->uploads($request));

        return redirect()->route('admin.cats.index', $cat->archived_at ? ['status' => 'archived'] : [])
            ->with('success', 'Saved the changes to '.$cat->cat_name.'.');
    }

    // Adoption requests for the cat are kept: their cat_id becomes NULL and they still carry the cat's name.
    public function destroy(Cat $cat): RedirectResponse
    {
        // Applicants still waiting for this cat get their answer; a deleted cat can't be adopted.
        $cat->adoptionRequests()->whereIn('status', AdoptionStatus::open())->update(['status' => AdoptionStatus::Rejected]);
        $cat->delete();
        $this->deleteUnusedPublicImages($cat->cat_image, $cat->cat_clip);

        return redirect()->route('admin.cats.index', $cat->archived_at ? ['status' => 'archived'] : [])
            ->with('success', $cat->cat_name.' was deleted.');
    }

    public function archive(Request $request, Cat $cat): RedirectResponse
    {
        $validated = $request->validate([
            'archive_reason' => ['nullable', 'string', 'max:255'],
        ]);

        // Archiving twice would overwrite the date and the reason visitors see.
        if ($cat->archived_at !== null) {
            return back()->with('error', $cat->cat_name.' is already archived.');
        }

        $cat->archived_at = now();
        $cat->archive_reason = $validated['archive_reason'] ?? null;
        $cat->status = Cat::STATUS_ARCHIVED;
        $cat->save();

        return redirect()->route('admin.cats.index')->with('success', $cat->cat_name.' was archived. Its public page now explains why it\'s gone.');
    }

    // Old URL for the archived list, now a tab.
    public function archived(): RedirectResponse
    {
        return redirect()->route('admin.cats.index', ['status' => 'archived']);
    }

    // Back into the inventory as Active; the public list still follows Cat::available().
    public function restore(Cat $cat): RedirectResponse
    {
        // Restoring sets the cat Active, which would quietly publish an Inactive cat.
        if ($cat->archived_at === null) {
            return back()->with('error', $cat->cat_name.' isn\'t archived.');
        }

        $cat->archived_at = null;
        $cat->archive_reason = null;
        $cat->status = Cat::STATUS_ACTIVE;
        $cat->save();

        return redirect()->route('admin.cats.index', ['status' => 'archived'])->with('success', $cat->cat_name.' was restored to the cat inventory.');
    }

    /**
     * The photo and clip from the editor; stored under cats/{id}/images/ and cats/{id}/videos/.
     *
     * @return array<string, array{0: ?UploadedFile, 1: string, 2: string}>
     */
    private function uploads(CatRequest $request): array
    {
        return [
            'cat_image' => [$request->file('cat_image'), 'images', 'photo'],
            'cat_clip' => [$request->file('cat_clip'), 'videos', 'clip'],
        ];
    }

    /**
     * Adds what AdminCatResource needs to label each cat without a query per row.
     */
    private function withAdminState(Builder $query): Builder
    {
        return $query
            ->withPendingRequestCount()
            ->withExists(['adoptionRequests as is_adopted' => fn (Builder $requests) => $requests->where('status', AdoptionStatus::Released)])
            ->withExists(['adoptionRequests as is_reserved' => fn (Builder $requests) => $requests->where('status', AdoptionStatus::Approved)]);
    }
}
