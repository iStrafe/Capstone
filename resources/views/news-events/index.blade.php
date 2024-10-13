@include('scripts')
@include('admin.adminNavbar')
    <head></head>
    

    <body>
    <table class="table table-hover">
        <tr>
            <th><h1>News Events</h1>
            <a href="{{ route('news-events.create') }}">Create New Event</a></th>
        </tr>
        <tr>
            <th>Title</th>
            <th>Description</th>
            <th>Event Date</th>
            <th>Image</th>
            <th>Edit|Delete</th>
           
        </tr>
        @foreach($newsEvent as $info)
        <tr>
                <td>{{ $info->title }}</td>
                <td>{{ $info->description }}</td>
                <td>{{ $info->event_date }}</td>
                <td>
                @if($info->eventimage)
                    <img src="{{ asset('images/' . $info->eventimage) }}" alt="Image of {{ $info->title }}" style="width: 50px; height: auto;">
                @else
                    No image
                @endif
                </td>
                <td>
                <button type="button" class="btn btn-link"><a href="{{ route('news-events.edit', $info->id) }}">Edit</a> </button>
                    
                    <form action="{{ route('news-events.destroy', $info->id) }}" method="POST" style="display:inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-link">Delete</button>
                    </form>
                </td>

        </tr>
        @endforeach
</table>

</body>
