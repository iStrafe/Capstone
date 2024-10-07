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
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'telephone_number' => 'nullable|regex:/^[0-9]{7,15}$/',
            'mobile_number' => 'nullable|regex:/^[0-9]{10,15}$/',
            'cat_name' => 'required|string|max:255',
            'cat_image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'age' => 'required|integer|min:0|max:25',
            'color' => 'nullable|string|max:50',
            'breed' => 'nullable|string|max:100',
            'sex' => 'required|in:Male,Female',
            'date_of_adoption' => 'nullable|date',
        ]);
    
        $input = $request->all();
    
        // Handle image upload
        if ($request->hasFile('cat_image')) {
            $imageName = time() . '.' . $request->cat_image->extension();
            $request->cat_image->move(public_path('images'), $imageName);
            $input['cat_image'] = $imageName;
        }
    
        Cat::create($input);
    
        return redirect()->route('admin.cats.index')->with('success', 'Pet created successfully.');
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
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'telephone_number' => 'nullable|regex:/^[0-9]{7,15}$/',
            'mobile_number' => 'nullable|regex:/^[0-9]{10,15}$/',
            'cat_name' => 'required|string|max:255',
            'cat_image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'age' => 'required|integer|min:0|max:25',
            'color' => 'nullable|string|max:50',
            'breed' => 'nullable|string|max:100',
            'sex' => 'required|in:Male,Female',
            'date_of_adoption' => 'nullable|date',
        ]);

        $input = $request->all();

        $cat = Cat::find($id);
        $cat->fill($request->all());

        if ($request->hasFile('cat_image')) {
            $fileName = time() . '.' . $request->cat_image->extension();
            $request->cat_image->move(public_path('images'), $fileName);
            $cat->cat_image = $fileName;
        }

        $cat->save();
        return redirect()->route('admin.cats.index')->with('success', 'Pet updated successfully.');
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
