@include('scripts')
@include('admin.adminNavbar')
<!DOCTYPE html>
    <title>Add Cat</title>
        <body>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.cats.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="container-sm">

            <div class="mb-3">
            <label for="cat_name" class="form-label">Cat Name</label>
            <input type="text" name="cat_name" class="form-control" required>
            </div>
            
            <div class="mb-3">
            <label for="cat_image" class="form-label">Cat Image</label>
            <input type="file" name="cat_image" class="form-control">
            </div>
            
            <div class="mb-3">
            <label for="age" class="form-label">Age</label>
            <input type="number" name="age" class="form-control" required>
            </div>

            <div class="mb-3">
            <label for="color" class="form-label">Color</label>
            <input type="text" name="color" class="form-control" required>
            </div>

            <div class="mb-3">
            <label for="breed" class="form-label">Breed</label>
            <input type="text" name="breed" class="form-control" required>
            </div>

            <div class="mb-3">
            <label for="sex" class="form-label">Sex</label>
            <select name="sex" class="form-select" required>
                <option selected>Open this select menu</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
            </div>

            <button type="submit" class="btn btn-primary mb-3">Add Cat</button>

        </div>

        
        <br>
        

        
    </form>
        </body>
</html>