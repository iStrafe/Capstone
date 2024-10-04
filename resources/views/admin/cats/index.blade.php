@include('scripts')
@include('admin.adminNavbar')
<!DOCTYPE html>
    <title>Cats list</title>
        <body>

    <h1>Cat Adoption List</h1>
    <button type="button" class="btn btn-success"><a href="{{ route('admin.cats.create') }}">Add New Cat</a></button>
    
    <table class="table table-hover">
        <tr>
            <th>Cat Name</th>
            <th>Cat Image</th>
            <th>Age</th>
            <th>Color</th>
            <th>Breed</th>
            <th>Sex</th>
            <th>Actions</th>
        </tr>
        @foreach($cats as $cat)
            <tr>
                <td>{{ $cat->cat_name }}</td>
                <td>
                    @if($cat->cat_image)
                        <img src="{{ asset('images/' . $cat->cat_image) }}" alt="Image of {{ $cat->cat_name }}" style="width: 50px; height: auto;">
                    @else
                        No image
                    @endif
                </td>
                <td>{{ $cat->age }}</td>
                <td>{{ $cat->color }}</td>
                <td>{{ $cat->breed }}</td>
                <td>{{ $cat->sex }}</td>
                <td>
                <button type="button" class="btn btn-link"><a href="{{ route('admin.cats.show', $cat->id) }}">View</a> </button>
                <button type="button" class="btn btn-link"><a href="{{ route('admin.cats.edit', $cat->id) }}">Edit</a> </button>
                    
                    <form action="{{ route('admin.cats.destroy', $cat->id) }}" method="POST" style="display:inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-link">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>
        </body>
</html>