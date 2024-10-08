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
        $request->validate([
            'full_name' => 'required|string|max:255',
            'mobile_number' => 'required|string|max:15',
            'message' => 'required|string',
        ]);

        // Create a new contact entry
        Contact::create([
            'full_name' => $request->full_name,
            'mobile_number' => $request->mobile_number,
            'message' => $request->message,
        ]);

        // Redirect or return response
        return redirect()->back()->with('success', 'Message sent successfully! Wait for the update');
    }
}
