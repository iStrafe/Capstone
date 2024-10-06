@include('scripts')
@include('admin.adminNavbar')
<!DOCTYPE html>
<title>Cats list</title>
<body>

<h1>Cat Adoption List</h1>
<button type="button" class="btn btn-success text-white" data-bs-toggle="modal" data-bs-target="#addCatModal">Add New Cat</button>

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
                <button type="button" class="btn btn-link" data-bs-toggle="modal" data-bs-target="#showCatModal">View</button>
                <button type="button" class="btn btn-link" data-bs-toggle="modal" data-bs-target="#editCatModal">Edit</button>
                
                
                <form action="{{ route('admin.cats.destroy', $cat->id) }}" method="POST" style="display:inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-link">Delete</button>
                </form>
            </td>
        </tr>
    @endforeach
</table>

@include('admin.cats.create') 
@include('admin.cats.edit')
@include('admin.cats.show') 

</body>
</html>