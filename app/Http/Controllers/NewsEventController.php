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
            'title' => 'required',
            'description' => 'required',
            'event_date' => 'required|date',
            'eventimage' => 'nullable|image|mimes:jpeg,png,jpg,gif'
        ]);
    
        NewsEvent::create($request->all());
    
        return redirect()->route('news-events.index')
                         ->with('success', 'News Event created successfully.');
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

    $newsEvent = NewsEvent::findOrFail($id);
    $newsEvent->update($request->all());

    return redirect()->route('news-events.index')
                     ->with('success', 'News Event updated successfully.');
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
