<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Admin inbox for messages sent through the Contact Us form.
 */
class ContactMessageController extends Controller
{
    public function index(): View
    {
        return view('admin.messages.index', [
            'messages' => Contact::latest()->latest('id')->paginate(15),
            'unhandledCount' => Contact::unhandled()->count(),
        ]);
    }

    // Mark a message as handled, or back to unhandled if it was marked by mistake.
    public function handled(Request $request, Contact $contact): RedirectResponse
    {
        $request->validate(['handled' => ['required', 'boolean']]);
        $handled = $request->boolean('handled');

        $contact->handled_at = $handled ? ($contact->handled_at ?? now()) : null;
        $contact->save();

        return back()->with('success', $handled ? 'Message marked as handled.' : 'Message moved back to unhandled.');
    }

    public function destroy(Contact $contact): RedirectResponse
    {
        $contact->delete();

        return back()->with('success', 'Message deleted.');
    }
}
