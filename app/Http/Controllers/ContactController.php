<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Support\PhoneNumber;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    //
    public function store(Request $request)
    {
        $request->merge(['mobile_number' => PhoneNumber::normalize($request->input('mobile_number'))]);

        // Validate the form data
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            // Optional, so the shelter can answer by email from the admin inbox.
            'email' => 'nullable|email|max:255',
            'mobile_number' => ['required', 'string', PhoneNumber::RULE],
            'message' => 'required|string|max:5000',
        ], [
            'mobile_number.regex' => PhoneNumber::MESSAGE,
        ]);

        // Create a new contact entry
        Contact::create($validated);

        // Redirect or return response
        return redirect()->back()->with('success', 'Thanks! Your message reached the AduCats team. We will get back to you soon.');
    }
}
