@include('scripts')
@include('admin.adminNavbar')
<head>
    <style>
        /* General page styling */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #d0e7ff; /* Light Blue Background */
            color: #333;
            margin: 0;
            padding: 0;
        }

        h1 {
            font-size: 2.5em;
            color: #2e3b4e;
            font-weight: 600;
            margin-bottom: 20px;
            text-align: center;
            padding-top: 20px;  /* Adds a little top padding to move the header down slightly */
        }

        a {
            text-decoration: none;
        }

        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;  /* Move the table a little closer to the top */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            background-color: #ffffff;
            border-radius: 8px;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            font-size: 1.1em;
        }

        th {
            background-color: #007bff;
            color: white;
            text-transform: uppercase;
        }

        td {
            background-color: #f9f9f9;
        }

        td:hover {
            background-color: #f1f1f1;
        }

        /* Image styling */
        img {
            max-width: 50px;
            height: auto;
            border-radius: 4px;
        }

        /* Button styling */
        .btn-link {
            color: #007bff;
            text-decoration: none;
            padding: 5px 10px;
        }

        .btn-link:hover {
            text-decoration: underline;
            color: #0056b3;
        }

        .btn-success {
            background-color: #28a745;
            color: white;
            padding: 8px 15px;
            font-size: 1.1em;
            border-radius: 6px;
        }

        .btn-success:hover {
            background-color: #218838;
        }

        /* Container padding adjustment */
        .container {
            padding-top: 20px;  /* Move the content closer to the top */
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }

            th, td {
                padding: 8px;
                font-size: 1em;
            }

            h1 {
                font-size: 2em;
            }

            .btn-success {
                font-size: 1em;
            }
        }

        @media (max-width: 480px) {
            th, td {
                padding: 6px;
                font-size: 0.9em;
            }

            h1 {
                font-size: 1.8em;
            }

            .btn-success {
                font-size: 0.9em;
                padding: 6px 12px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>News Events</h1>
        <a href="{{ route('news-events.create') }}" class="btn btn-success mb-3">Create New Event</a>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Event Date</th>
                        <th>Image</th>
                        <th>Edit | Delete</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($newsEvent as $info)
                    <tr>
                        <td>{{ $info->title }}</td>
                        <td>{{ $info->description }}</td>
                        <td>{{ $info->event_date }}</td>
                        <td>
                            @if($info->eventimage)
                                <img src="{{ asset('images/' . $info->eventimage) }}" alt="Image of {{ $info->title }}">
                            @else
                                No image
                            @endif
                        </td>
                        <td>
                            <button type="button" class="btn btn-link"><a href="{{ route('news-events.edit', $info->id) }}">Edit</a></button>
                            <form action="{{ route('news-events.destroy', $info->id) }}" method="POST" style="display:inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-link">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>
