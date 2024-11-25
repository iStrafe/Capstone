@include('scripts')
@include('admin.adminNavbar')

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

<h2>Released Request</h2>
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
                <th>Released Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($released_request as $request)
                <tr>
                    <td><input type="text" class="form-control" name="name" value="{{ $request->name }}" disabled></td>
                    <td><input type="text" class="form-control" name="address" value="{{ $request->address }}" disabled></td>
                    <td><input type="text" class="form-control" name="email" value="{{ $request->email }}" disabled></td>
                    <td><input type="text" class="form-control" name="mobile_phone" value="{{ $request->mobile_phone }}" disabled></td>
                    <td>
                        @if($request->valid_id)
                            <a href="{{ route('viewValidIds', ['id' => $request->id]) }}" target="_blank" class="btn btn-link">
                                View IDs
                            </a>
                        @else
                            <span>No ID Provided</span>
                        @endif
                    </td>
                    <td><input type="text" class="form-control" name="name_of_cat" value="{{ $request->name_of_cat }}" disabled></td>
                    <td>
                        <select class="form-control status-dropdown" name="status" data-id="{{ $request->id }}" disabled>
                            <option value="Pending" {{ $request->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="Approved" {{ $request->status == 'Approved' ? 'selected' : '' }}>Approved</option>
                            <option value="Not approved" {{ $request->status == 'Not approved' ? 'selected' : '' }}>Not approved</option>
                            <option value="Released" {{ $request->status == 'Released' ? 'selected' : '' }}>Released</option>
                        </select>
                    </td>
                    <td><input type="date" class="form-control" name="approval_date" value="{{ $request->approval_date }}" disabled></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>