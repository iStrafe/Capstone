<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    //
    public function store(Request $request)
    {
        // Validate the form data
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            // Optional, so the shelter can answer by email from the admin inbox.
            'email' => 'nullable|email|max:255',
            'mobile_number' => 'required|string|max:15',
            'message' => 'required|string',
        ]);

        // Create a new contact entry
        Contact::create($validated);

        // Redirect or return response
        return redirect()->back()->with('success', 'Message sent successfully! Wait for the update');
    }
}
