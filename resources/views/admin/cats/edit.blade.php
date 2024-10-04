@include('scripts')
@include('admin.adminNavbar')
<!DOCTYPE html>
    <title>Add Cat</title>
        <body>
    <h1>Add New Cat</h1>

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

    <form action="{{ route('admin.cats.update', $cat->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <label for="cat_name">Cat Name</label>
        <input type="text" name="cat_name" value="{{ $cat->cat_name }}" required>

        <label for="cat_image">Cat Image</label>
        <input type="file" name="cat_image">

        <label for="age">Age</label>
        <input type="number" name="age" value="{{ $cat->age }}" required>

        <label for="color">Color</label>
        <input type="text" name="color" value="{{ $cat->color }}" required>

        <label for="breed">Breed</label>
        <input type="text" name="breed" value="{{ $cat->breed }}" required>

        <label for="sex">Sex</label>
        <select name="sex" required>
            <option value="Male" {{ $cat->sex == 'Male' ? 'selected' : '' }}>Male</option>
            <option value="Female" {{ $cat->sex == 'Female' ? 'selected' : '' }}>Female</option>
        </select>

        <button type="submit">Update Cat</button>
    </form>
        </body>
</html>