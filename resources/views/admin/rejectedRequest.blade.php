@include('scripts')
@include('admin.adminNavbar')

<div class="container-fluid">
 <h2>Rejected Requests</h2>
    <table class="table table-bordered table-striped table-responsive">
        <thead>
            <tr>
                <th>Name</th>
                <th>Address</th>
                <th>Email</th>
                <th>Mobile Phone</th>
                <th>Valid ID</th>
                <th>Name of Cat</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rejected_request as $request)
                <tr>
                <td><input type="text" class="form-control" name="name" value="{{ $request->name }}" disabled></td>
                <td><input type="text" class="form-control" name="address" value="{{ $request->address }}" disabled></td>
                <td><input type="text" class="form-control" name="address" value="{{ $request->email }}" disabled></td>
                <td><input type="text" class="form-control" name="mobile_phone" value="{{ $request->mobile_phone }}" disabled></td>
                <td>
                    @if($request->valid_id)
                        <img src="{{ asset('images/' . $request->valid_id) }}" alt="Valid ID" style="max-width: 100px;">
                    @else
                        <a href="{{ asset('images/' . $request->valid_id) }}" target="_blank" class="btn btn-link">
                            Empty
                        </a>
                    @endif
                </td>
                <td><input type="text" class="form-control" name="name_of_cat" value="{{ $request->name_of_cat }}" disabled></td>
                <td>
                    <select class="form-control status-dropdown" name="status" data-id="{{ $request->id }}" disabled>
                        <option value="Pending" {{ $request->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Approved" {{ $request->status == 'Approved' ? 'selected' : '' }}>Approved</option>
                        <option value="Not approved" {{ $request->status == 'Not approved' ? 'selected' : '' }}>Not approved</option>
                    </select>
                </td>
                <td>
                    <button class="btn btn-sm btn-primary edit-entry" data-id="{{ $request->id }}">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn btn-sm btn-success save-status" data-id="{{ $request->id }}" style="display:none;">
                        <i class="fas fa-save"></i> Save
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
             <div class="d-flex justify-content-center">
                {{ $rejected_request->links() }}
             </div>
    </div>
    </table>
</div>

<script>
    document.querySelectorAll('.edit-entry').forEach(button => {
        button.addEventListener('click', function() {
            const row = this.closest('tr');
            row.querySelectorAll('input, select').forEach(input => input.disabled = false);
            row.querySelector('.save-status').style.display = 'inline-block';
            this.style.display = 'none';
        });
    });

    document.querySelectorAll('.save-status').forEach(button => {
        button.addEventListener('click', function() {
            const row = this.closest('tr');
            const requestId = this.getAttribute('data-id');
            const name = row.querySelector('input[name="name"]').value;
            const address = row.querySelector('input[name="address"]').value;
            const mobile_phone = row.querySelector('input[name="mobile_phone"]').value;
            const name_of_cat = row.querySelector('input[name="name_of_cat"]').value;
            const status = row.querySelector('select[name="status"]').value;

            fetch(`/update-status/${requestId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ name, address, mobile_phone, name_of_cat, status })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Entry updated successfully');
                    row.querySelectorAll('input, select').forEach(input => input.disabled = true);
                    row.querySelector('.edit-entry').style.display = 'inline-block';
                    this.style.display = 'none';
                    location.reload(); // Refresh the page
                } else {
                    alert('Failed to update entry');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the entry');
            });
        });
    });
</script>

<style>
    h2 {
        font-size: 2em;
        font-weight: bold;
        color: #03045E; /* Dark blue color for professionalism */
        text-align: center;
        position: relative;
        padding-bottom: 5px;
    }

    /* Add a subtle underline effect */
    h2::after {
        content: "";
        display: block;
        width: 200px;
        height: 3px;
        background-color: #0077cc; /* Slightly lighter blue for contrast */
        margin: 2px auto 0; /* Center align underline */
        border-radius: 5px;
    }
    /* Container styling */
    .container-fluid {
        padding: 20px;
        background-color: #f8f9fa;
    }

    /* Table Styling */
    .table {
        background-color: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .table th {
        background-color: #004080;
        color: #ffffff;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 14px;
    }

    .table td {
        font-size: 13px;
        color: #333;
        padding: 12px;
        text-align: left;
        border-top: 1px solid #e9ecef;
    }

    /* Link Button Styling */
    .btn-link {
        color: #004080;
        font-weight: 500;
    }

    .btn-link:hover {
        color: #0066cc;
        text-decoration: underline;
    }

    /* Status Cell Styling */
    .status {
        font-weight: bold;
    }

    /* Conditional Status Colors */
    .status[data-status="Approved"] {
        color: green;
    }

    .status[data-status="Not Approved"] {
        color: red;
    }

    /* Hover Effect */
    .table-striped tbody tr:hover {
        background-color: #f1f1f1;
    }

    /* Button Styling */
    .btn-info {
        background-color: #004080;
        color: white;
        border: none;
        padding: 6px 12px;
        font-size: 12px;
        border-radius: 4px;
        cursor: pointer;
    }

    .btn-info:hover {
        background-color: #0056b3;
    }

    /* Responsive Table */
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
            position: relative;
            padding: 8px;
            border: none;
            border-bottom: 1px solid #ddd;
        }

        .table td:before {
            content: attr(data-label);
            font-weight: 600;
            color: #555;
            position: absolute;
            left: 10px;
            top: 8px;
            font-size: 12px;
        }

        .btn-info {
            font-size: 12px;
        }
    }
</style>