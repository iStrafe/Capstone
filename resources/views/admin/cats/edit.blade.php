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

        <label for="name">Name</label>
        <input type="text" name="name" value="{{ $cat->name }}" required>

        <label for="address">Address</label>
        <input type="text" name="address" value="{{ $cat->address }}" required>

        <label for="telephone_number">Telephone Number</label>
        <input type="text" name="telephone_number" value="{{ $cat->telephone_number }}" required>

        <label for="mobile_number">Mobile Number</label>
        <input type="text" name="mobile_number" value="{{ $cat->mobile_number }}" required>

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

        <label for="date_of_adoption">Date of Adoption</label>
        <input type="date" name="date_of_adoption" value="{{ $cat->date_of_adoption }}" required>

        <button type="submit">Update Cat</button>
    </form>
        </body>
</html>