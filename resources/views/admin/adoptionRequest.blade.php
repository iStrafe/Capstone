@include('scripts')
@include('admin.adminNavbar')

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Name</th>
            <th>Address</th>
            <th>Mobile Phone</th>
            <th>Valid_id</th>
            <th>Name of Cat</th>
            <th>Status</th>
            <th>Action</th> <!-- New column for action button -->
        </tr>
    </thead>
    <tbody>
        @foreach($adoption_request as $request)
            <tr>
                <td>{{ $request->name }}</td>
                <td>{{ $request->address }}</td>
                <td>{{ $request->mobile_phone }}</td>
                <td>
                    <a href="{{ asset('storage/valid_id/' . $request->valid_id) }}" target="_blank">
                        View ID
                    </a>
                </td>
                <td>{{ $request->name_of_cat }}</td>
                <td class="status">{{ $request->status }}</td>
                <td>
                    <button class="toggle-status" data-id="{{ $request->id }}">
                        Toggle Status
                    </button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<script>
    document.querySelectorAll('.toggle-status').forEach(button => {
        button.addEventListener('click', function() {
            const row = this.closest('tr');
            const statusCell = row.querySelector('.status');
            const currentStatus = statusCell.textContent.trim();
            const newStatus = currentStatus === 'Approved' ? 'Not Approved' : 'Approved';

            // Update the status cell
            statusCell.textContent = newStatus;

        });
    });
</script>