<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\StoresPublicImages;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CatRequest;
use App\Models\Cat;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\View\View;

class CatController extends Controller
{
    use StoresPublicImages;

    public function index(): View
    {
        $cats = Cat::available()->get();

        return view('admin.cats.index', compact('cats'));
    }

    public function create(): View
    {
        return view('admin.cats.create');
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

    public function show(Cat $cat): View
    {
        return view('admin.cats.show', compact('cat'));
    }

    public function edit(Cat $cat): View
    {
        return view('admin.cats.edit', compact('cat'));
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

    public function destroy(Cat $cat): RedirectResponse
    {
        $cat->delete();

        return redirect()->route('admin.cats.index');
    }

    public function archive(Cat $cat): RedirectResponse
    {
        $cat->archived_at = now();
        $cat->status = 'ARCHIVED';
        $cat->save();

        return redirect()->route('admin.cats.index')->with('success', 'Cat archived successfully.');
    }

    public function archived(): View
    {
        $archivedCats = Cat::whereNotNull('archived_at')->get();

        return view('admin.cats.archived', compact('archivedCats'));
    }
}
