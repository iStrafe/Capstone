<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cat;
use Illuminate\Http\Request;

class CatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cats = Cat::all();
        return view('admin.cats.index', compact('cats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.cats.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $cat = new Cat();
        $cat->fill($request->all());

        if ($request->hasFile('cat_image')) {
            $fileName = time() . '.' . $request->cat_image->extension();
            $request->cat_image->move(public_path('images'), $fileName);
            $cat->cat_image = $fileName;
        }

        $cat->save();
        return redirect()->route('admin.cats.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $cat = Cat::find($id);
        return view('admin.cats.show', compact('cat'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $cat = Cat::find($id);
        return view('admin.cats.edit', compact('cat'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $cat = Cat::find($id);
        $cat->fill($request->all());

        if ($request->hasFile('cat_image')) {
            $fileName = time() . '.' . $request->cat_image->extension();
            $request->cat_image->move(public_path('images'), $fileName);
            $cat->cat_image = $fileName;
        }

        $cat->save();
        return redirect()->route('admin.cats.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $cat = Cat::find($id);
        $cat->delete();
        return redirect()->route('admin.cats.index');
    }


}
