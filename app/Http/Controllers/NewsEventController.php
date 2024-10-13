<?php

namespace App\Http\Controllers;
use App\Models\NewsEvent; 

use Illuminate\Http\Request;

class NewsEventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    $newsEvent = NewsEvent::all();
    return view('news-events.index', compact('newsEvent'));
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
{
    return view('news-events.create');
}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required',
            'eventimage' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Ensure it's an image
        ]);
    
        $data = $request->all();
    
        // Handle the file upload
        if ($request->hasFile('eventimage')) {
            // Get the file with the extension
            $image = $request->file('eventimage');
            // Generate a unique filename
            $filename = time() . '.' . $image->getClientOriginalExtension();
            // Move the file to the public/images directory
            $image->move(public_path('images'), $filename);
            // Save the filename to the database
            $data['eventimage'] = $filename;
        }
    
        // Save the event data (including image filename)
        NewsEvent::create($data);
    
        return redirect()->route('news-events.index')->with('success', 'Event created successfully');
    }
    
    

    /**
     * Display the specified resource.
     */


public function show($id)
{
    $newsEvent = NewsEvent::findorFail($id);
    return view('news-events.show', compact('newsEvent'));
}


    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
{
    $newsEvent = NewsEvent::findOrFail($id);
    return view('news-events.edit', compact('newsEvent'));
}


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)

{
    $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'required',
        'event_date' => 'required|date',
        'catimage' => 'nullable|image|mimes:jpeg,png,jpg,gif'
    ]);

    $input = $request->all();

        $renew = NewsEvent::find($id);
        $renew->fill($request->all());

        if ($request->hasFile('eventimage')) {
            $fileName = time() . '.' . $request->eventimage->extension();
            $request->eventimage->move(public_path('images'), $fileName);
            $renew->eventimage = $fileName;
        }

        $renew->save();
        return redirect()->route('news-events.index')->with('success', 'Pet updated successfully.');
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
{
    $newsEvent = NewsEvent::findOrFail($id);
    $newsEvent->delete();

    return redirect()->route('news-events.index')
                     ->with('success', 'News Event deleted successfully.');
}
 //Cards for User Events
 public function index3()
    {
        // Fetch the most recent news event (you can modify this to fetch a specific one)
        $newsEvent = NewsEvent::all();  // Use first() to get a single instance
        
        // Pass the single event to the view
        return view('news-events.events', compact('newsEvent'));
    }


}
