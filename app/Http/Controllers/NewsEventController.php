<?php

namespace App\Http\Controllers;
use App\Models\NewsEvent; 

use App\Http\Controllers\Concerns\StoresPublicImages;
use App\Http\Requests\Admin\NewsEventRequest;

class NewsEventController extends Controller
{
    use StoresPublicImages;

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
    public function store(NewsEventRequest $request)
    {
        $data = $request->safe()->only(['title', 'description', 'event_date']);

        if ($request->hasFile('eventimage')) {
            $data['eventimage'] = $this->moveToPublicImages($request->file('eventimage'));
        }

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
    public function update(NewsEventRequest $request, $id)
    {
        $renew = NewsEvent::findOrFail($id);
        $renew->fill($request->safe()->only(['title', 'description', 'event_date']));

        if ($request->hasFile('eventimage')) {
            $renew->eventimage = $this->moveToPublicImages($request->file('eventimage'));
        }

        $renew->save();

        return redirect()->route('news-events.index')->with('success', 'Event updated successfully.');
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
