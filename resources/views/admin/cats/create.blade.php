@include('scripts')
@include('admin.adminNavbar')
<!DOCTYPE html>
    <title>Add Cat</title>
        <body>
    <h1>Add New Cat</h1>

    <form action="{{ route('admin.cats.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <label for="name">Name</label>
        <input type="text" name="name" required>

        <label for="address">Address</label>
        <input type="text" name="address" required>

        <label for="telephone_number">Telephone Number</label>
        <input type="text" name="telephone_number" required>

        <label for="mobile_number">Mobile Number</label>
        <input type="text" name="mobile_number" required>

        <label for="cat_name">Cat Name</label>
        <input type="text" name="cat_name" required>

        <label for="cat_image">Cat Image</label>
        <input type="file" name="cat_image">

        <label for="age">Age</label>
        <input type="number" name="age" required>

        <label for="color">Color</label>
        <input type="text" name="color" required>

        <label for="breed">Breed</label>
        <input type="text" name="breed" required>

        <label for="sex">Sex</label>
        <select name="sex" required>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
        </select>

        <label for="date_of_adoption">Date of Adoption</label>
        <input type="date" name="date_of_adoption" required>

        <button type="submit">Add Cat</button>
    </form>
        </body>
</html>