<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AdoptionStatus;
use App\Http\Controllers\Concerns\StoresPublicImages;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CatRequest;
use App\Models\Cat;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\View\View;

class CatController extends Controller
{
    use StoresPublicImages;

    public function index(): View
    {
        // Admins see every cat that isn't archived, including Inactive and adopted ones;
        // the flags label cats that are off the public list because of an adoption.
        $cats = Cat::notArchived()
            ->withExists(['adoptionRequests as is_adopted' => fn ($query) => $query->where('status', AdoptionStatus::Released)])
            ->withExists(['adoptionRequests as is_reserved' => fn ($query) => $query->where('status', AdoptionStatus::Approved)])
            ->get();

        return view('admin.cats.index', compact('cats'));
    }

    // create, show and edit are modals on the index page; their direct URLs
    // would only render a bare modal fragment, so send the admin to the index.
    public function create(): RedirectResponse
    {
        return redirect()->route('admin.cats.index');
    }

    public function store(CatRequest $request): RedirectResponse
    {
        // New cats always start Active.
        $input = Arr::except($request->validated(), ['cat_image', 'cat_clip', 'status']);

        if ($request->hasFile('cat_image')) {
            $input['cat_image'] = $this->moveToPublicImages($request->file('cat_image'));
        }

        if ($request->hasFile('cat_clip')) {
            $input['cat_clip'] = $this->moveToPublicImages($request->file('cat_clip'));
        }

        Cat::create($input);

        return redirect()->route('admin.cats.index')->with('success', 'Pet created successfully.');
    }

    public function show(Cat $cat): RedirectResponse
    {
        return redirect()->route('admin.cats.index');
    }

    public function edit(Cat $cat): RedirectResponse
    {
        return redirect()->route('admin.cats.index');
    }

    public function update(CatRequest $request, Cat $cat): RedirectResponse
    {
        $cat->fill(Arr::except($request->validated(), ['cat_image', 'cat_clip']));

        if ($request->hasFile('cat_image')) {
            $cat->cat_image = $this->moveToPublicImages($request->file('cat_image'));
        }

        if ($request->hasFile('cat_clip')) {
            $cat->cat_clip = $this->moveToPublicImages($request->file('cat_clip'));
        }

        $cat->save();

        return redirect()->route('admin.cats.index')->with('success', 'Cat updated successfully');
    }

    // Adoption requests for the cat are kept: their cat_id becomes NULL and they still carry the cat's name.
    public function destroy(Cat $cat): RedirectResponse
    {
        $cat->delete();

        return redirect()->route($cat->archived_at ? 'admin.cats.archived' : 'admin.cats.index')
            ->with('success', $cat->cat_name.' was deleted.');
    }

    public function archive(Request $request, Cat $cat): RedirectResponse
    {
        $validated = $request->validate([
            'archive_reason' => ['nullable', 'string', 'max:255'],
        ]);

        $cat->archived_at = now();
        $cat->archive_reason = $validated['archive_reason'] ?? null;
        $cat->status = Cat::STATUS_ARCHIVED;
        $cat->save();

        return redirect()->route('admin.cats.index')->with('success', 'Cat archived successfully.');
    }

    public function archived(): View
    {
        $archivedCats = Cat::whereNotNull('archived_at')->latest('archived_at')->get();

        return view('admin.cats.archived', compact('archivedCats'));
    }

    // Back into the inventory as Active; the public list still follows Cat::available().
    public function restore(Cat $cat): RedirectResponse
    {
        $cat->archived_at = null;
        $cat->archive_reason = null;
        $cat->status = Cat::STATUS_ACTIVE;
        $cat->save();

        return redirect()->route('admin.cats.archived')->with('success', $cat->cat_name.' was restored to the cat inventory.');
    }
}
