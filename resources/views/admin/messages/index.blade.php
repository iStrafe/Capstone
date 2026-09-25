@include('scripts')
@include('admin.adminNavbar')
<style>
    .messages-page h1 {
        font-size: 2.2em;
        color: #2e3b4e;
        font-weight: 600;
        text-align: center;
        padding-top: 20px;
        margin-bottom: 10px;
    }

    .messages-table {
        background-color: #ffffff;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .messages-table th {
        background-color: #03045E;
        color: white;
        text-align: left;
        white-space: nowrap;
    }

    .messages-table td {
        text-align: left;
        vertical-align: top;
        border: 1px solid #dee2e6;
    }

    .messages-table tr.is-unhandled td {
        background-color: #fff8e1;
        font-weight: 600;
    }

    .messages-table .message-body {
        white-space: pre-line;
        font-weight: normal;
        min-width: 260px;
        max-width: 520px;
        overflow-wrap: anywhere;
    }

    .messages-table .actions form {
        display: inline;
    }

    /* The reset in the shared scripts partial clears the background of [type=submit] buttons. */
    .messages-table .actions .btn {
        background-color: var(--bs-btn-bg);
        font-weight: normal;
    }

    .messages-table .actions .btn:hover {
        background-color: var(--bs-btn-hover-bg);
    }
</style>

<!DOCTYPE html>
<title>Contact messages</title>
<body>
<div class="container-fluid px-4 messages-page">
    <h1>Contact Messages</h1>
    <p class="text-center text-muted">
        Messages sent through the Contact Us page, newest first. {{ trans_choice('{0} Nothing is waiting for a reply.|{1} :count message is not handled yet.|[2,*] :count messages are not handled yet.', $unhandledCount) }}
    </p>

    @if($messages->isEmpty())
        <p class="text-center">No messages yet.</p>
    @else
        <div class="table-responsive">
            <table class="table messages-table">
                <thead>
                    <tr>
                        <th>Received</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Message</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($messages as $contact)
                        <tr class="{{ $contact->handled_at ? 'is-handled' : 'is-unhandled' }}">
                            <td>{{ $contact->created_at?->format('M j, Y g:i A') }}</td>
                            <td>{{ $contact->full_name }}</td>
                            <td>
                                @if($contact->email)
                                    <a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a>
                                @else
                                    <span class="text-muted">Not given</span>
                                @endif
                            </td>
                            <td>{{ $contact->mobile_number }}</td>
                            <td class="message-body">{{ $contact->message }}</td>
                            <td>
                                @if($contact->handled_at)
                                    <span class="badge bg-success">Handled</span>
                                    <div class="small text-muted">{{ $contact->handled_at->format('M j, Y') }}</div>
                                @else
                                    <span class="badge bg-warning text-dark">New</span>
                                @endif
                            </td>
                            <td class="actions">
                                <form action="{{ route('admin.messages.handled', $contact) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="handled" value="{{ $contact->handled_at ? 0 : 1 }}">
                                    <button type="submit" class="btn btn-sm {{ $contact->handled_at ? 'btn-outline-secondary' : 'btn-success' }}">
                                        {{ $contact->handled_at ? 'Mark as unhandled' : 'Mark as handled' }}
                                    </button>
                                </form>
                                <form action="{{ route('admin.messages.destroy', $contact) }}" method="POST" onsubmit="return confirm('Delete this message? This cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $messages->links('pagination::bootstrap-5') }}
    @endif
</div>
</body>
