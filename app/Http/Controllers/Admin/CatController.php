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

    public function index2()
    {
        $cats = Cat::all();
        return view('dashboard', compact('cats'));
    }
    public function index3()
    {
        $cats = Cat::all();
        return view('home', compact('cats'));
    }
    public function index4()
    {
        $cats = Cat::all();
        return view('cats.index', compact('cats'));
    }
    public function adminDashboard()
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
            'cat_name' => 'required|string|max:255',
            'cat_image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'age' => 'nullable|string|min:0|max:25',
            'color' => 'nullable|string|max:50',
            'breed' => 'nullable|string|max:100',
            'sex' => 'required|in:Male,Female',
        ]);
    
        $input = $request->all();

        // Set default value for age if not provided
        $input['age'] = $input['age'] ?? 'Unknown';
    
        // Handle image upload
        if ($request->hasFile('cat_image')) {
            $imageName = time() . '.' . $request->cat_image->extension();
            $request->cat_image->move(public_path('images'), $imageName);
            $input['cat_image'] = $imageName;
        }
    
        Cat::create($input);
    
        return redirect()->route('admin.cats.index')->with('success', 'Pet created successfully.');
    }

    public function openAiRoute(){
        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $cat = Cat::find($id);
        return view('admin.cats.show', compact('cat'));
    }
    public function showUser(string $id)
    {
        $cat = Cat::find($id);
        return view('cats.show', compact('cat'));
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
                'cat_name' => 'required|string|max:255',
                'cat_image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
                'age' => 'nullable|string|min:0|max:25',
                'color' => 'nullable|string|max:50',
                'breed' => 'nullable|string|max:100',
                'sex' => 'required|in:Male,Female',
                'status' => 'required|in:Active,Inactive',
            ]);

                $cat = Cat::findOrFail($id);
                $cat->cat_name = $request->input('cat_name');
                $cat->age = $request->input('age', 'Unknown');
                $cat->color = $request->input('color');
                $cat->breed = $request->input('breed');
                $cat->sex = $request->input('sex');
                $cat->status = $request->input('status');
                $cat->Medical_Record = $request->input('Medical_Record');

                if ($request->hasFile('cat_image')) {
                    $fileName = time() . '.' . $request->cat_image->extension();
                    $request->cat_image->move(public_path('images'), $fileName);
                    $cat->cat_image = $fileName;
                }

                $cat->save();

                return redirect()->route('admin.cats.index')->with('success', 'Cat updated successfully');
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
