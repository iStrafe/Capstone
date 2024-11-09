@include('scripts')
@include('admin.adminNavbar')

<div class="container-fluid">
    <table class="table table-bordered table-striped table-responsive">
        <thead>
            <tr>
                <th>Name</th>
                <th>Address</th>
                <th>Mobile Phone</th>
                <th>Valid ID</th>
                <th>Name of Cat</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($adoption_request as $request)
                <tr>
                    <td>{{ $request->name }}</td>
                    <td>{{ $request->address }}</td>
                    <td>{{ $request->mobile_phone }}</td>
                    <td>
                        <a href="{{ asset('storage/valid_id/' . $request->valid_id) }}" target="_blank" class="btn btn-link">
                            View ID
                        </a>
                    </td>
                    <td>{{ $request->name_of_cat }}</td>
                    <td class="status">{{ $request->status }}</td>
                    <td>
                        <button class="btn btn-sm btn-info toggle-status" data-id="{{ $request->id }}">
                            <i class="fas fa-sync-alt"></i> Toggle Status
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
    document.querySelectorAll('.toggle-status').forEach(button => {
        button.addEventListener('click', function() {
            const row = this.closest('tr');
            const statusCell = row.querySelector('.status');
            const currentStatus = statusCell.textContent.trim();
            const newStatus = currentStatus === 'Approved' ? 'Not Approved' : 'Approved';

            // Smooth transition for status change
            statusCell.style.transition = "color 0.3s";
            statusCell.textContent = newStatus;

            // Change color based on new status
            statusCell.style.color = newStatus === 'Approved' ? 'green' : 'red';

            // Optional: Send an AJAX request to update status in the database
            // AJAX logic can be added here
        });
    });
</script>

<style>
    /* Ensure table is responsive */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    /* Button Styles */
    .btn-info {
        background-color: #03045E;
        border: none;
        color: white;
        font-size: 14px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-info:hover {
        background-color: #0077cc;
        color: white;
        transform: translateY(-2px); /* Subtle hover animation */
    }

    .btn-link {
        color: #0077cc;
        text-decoration: none;
    }

    .btn-link:hover {
        text-decoration: underline;
    }

    /* Table hover effects for better UX */
    .table-striped tbody tr:hover {
        background-color: #f1f1f1;
        transition: background-color 0.3s;
    }

    /* Padding and borders for clarity */
    .table th, .table td {
        padding: 12px;
        text-align: center;
        font-size: 14px;
    }

    .table th {
        background-color: #03045E;
        color: white;
    }

    .table td {
        border: 1px solid #ddd;
    }

    .status {
        font-weight: bold;
    }

    /* For small screens, adjust the table's layout */
    @media (max-width: 768px) {
        .table thead {
            display: none;
        }

        .table, .table tbody, .table tr, .table td {
            display: block;
            width: 100%;
        }

        .table td {
            text-align: left;
            padding: 8px;
            position: relative;
            border: 1px solid #ddd;
        }

        .table td:before {
            content: attr(data-label);
            font-weight: bold;
            position: absolute;
            left: 0;
            top: 0;
            padding-left: 10px;
            font-size: 12px;
        }

        .table td:last-child {
            border-bottom: 0;
        }

        .btn-link {
            font-size: 14px;
        }
    }
</style>
