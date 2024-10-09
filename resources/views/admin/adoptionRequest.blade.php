@include('scripts')
@include('admin.adminNavbar')

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Name</th>
            <th>Address</th>
            <th>Home Phone</th>
            <th>Mobile Phone</th>
            <th>Name of Cat</th>
            <th>Breed</th>
            <th>Approximate Age</th>
            <th>Sex</th>
            <th>Color</th>
            <th>Valid_id</th>
            <th>Status</th>
            <th>Action</th> <!-- New column for action button -->
        </tr>
    </thead>
    <tbody>
        @foreach($adoption_request as $request)
            <tr>
                <td>{{ $request->name }}</td>
                <td>{{ $request->address }}</td>
                <td>{{ $request->home_phone }}</td>
                <td>{{ $request->mobile_phone }}</td>
                <td>{{ $request->name_of_cat }}</td>
                <td>{{ $request->breed }}</td>
                <td>{{ $request->approximate_age }}</td>
                <td>{{ $request->sex }}</td>
                <td>{{ $request->color }}</td>
                <td>
                    <a href="{{ asset('storage/valid_ids/' . $request->valid_id) }}" target="_blank">
                        View ID
                    </a>
                </td>
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