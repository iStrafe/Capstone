<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Admin inbox for messages sent through the Contact Us form: a list on the left, the opened
 * message on the right.
 */
class ContactMessageController extends Controller
{
    private const FILTERS = ['open', 'handled', 'all'];

    public function index(Request $request): Response
    {
        $filter = in_array($request->query('filter'), self::FILTERS, true) ? $request->query('filter') : 'open';

        $messages = Contact::query()
            ->when($filter === 'open', fn (Builder $query) => $query->whereNull('handled_at'))
            ->when($filter === 'handled', fn (Builder $query) => $query->whereNotNull('handled_at'))
            ->latest()
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        // The opened message stays open after it's marked handled, even though it leaves the list.
        $selected = $request->filled('message')
            ? Contact::find($request->integer('message'))
            : $messages->first();

        return Inertia::render('Admin/Messages', [
            'messages' => [
                'data' => $messages->getCollection()->map(fn (Contact $contact) => $this->summary($contact, $request))->values(),
                'meta' => ['currentPage' => $messages->currentPage(), 'lastPage' => $messages->lastPage(), 'total' => $messages->total()],
                'links' => ['prev' => $messages->previousPageUrl(), 'next' => $messages->nextPageUrl()],
            ],
            'selected' => $selected ? [
                ...$this->summary($selected, $request),
                'email' => $selected->email,
                'phone' => $selected->mobile_number,
                'message' => $selected->message,
                'receivedAtFull' => $selected->created_at?->format('M j, Y g:i A'),
                'handledAt' => $selected->handled_at?->format('M j, Y g:i A'),
                'handledUrl' => route('admin.messages.handled', $selected),
                'deleteUrl' => route('admin.messages.destroy', [$selected, ...array_filter(['filter' => $request->query('filter')])]),
            ] : null,
            'filter' => $filter,
            'counts' => [
                'open' => Contact::unhandled()->count(),
                'handled' => Contact::whereNotNull('handled_at')->count(),
            ],
        ]);
    }

    // Mark a message as handled, or back to unhandled if it was marked by mistake.
    public function handled(Request $request, Contact $contact): RedirectResponse
    {
        $request->validate(['handled' => ['required', 'boolean']]);
        $handled = $request->boolean('handled');

        $contact->handled_at = $handled ? ($contact->handled_at ?? now()) : null;
        $contact->save();

        return redirect($this->backToMessage($contact))
            ->with('success', $handled ? 'Message marked as handled.' : 'Message moved back to unhandled.');
    }

    public function destroy(Request $request, Contact $contact): RedirectResponse
    {
        $contact->delete();

        // Don't send the admin back to the deleted message.
        return redirect()->route('admin.messages.index', array_filter([
            'filter' => $request->query('filter'),
        ]))->with('success', 'Message deleted.');
    }

    /**
     * Back to the inbox page the admin came from, with this message still open. Without this,
     * a message marked handled would leave the "to handle" list and another one would open.
     */
    private function backToMessage(Contact $contact): string
    {
        $previous = url()->previous();
        $index = route('admin.messages.index');

        if (strtok($previous, '?') !== $index) {
            return $previous;
        }

        parse_str((string) parse_url($previous, PHP_URL_QUERY), $query);

        return route('admin.messages.index', ['message' => $contact->id] + $query);
    }

    /**
     * @return array<string, mixed>
     */
    private function summary(Contact $contact, Request $request): array
    {
        return [
            'id' => $contact->id,
            'name' => $contact->full_name,
            'preview' => mb_strimwidth(preg_replace('/\s+/', ' ', (string) $contact->message), 0, 90, '…'),
            'receivedAt' => $contact->created_at?->isToday() ? $contact->created_at->format('g:i A') : $contact->created_at?->format('M j'),
            'handled' => $contact->handled_at !== null,
            'url' => route('admin.messages.index', array_filter([
                'filter' => $request->query('filter'),
                'page' => $request->query('page'),
                'message' => $contact->id,
            ])),
        ];
    }
}
