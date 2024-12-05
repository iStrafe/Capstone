<!-- Modal -->
<div class="modal fade" id="addCatModal" tabindex="-1" aria-labelledby="addCatModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="addCatModalLabel" style="font-size: 2rem;">Add Cat</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
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
              <input type="text" name="cat_name" class="form-control" required oninput="this.value = this.value.toLowerCase()">
            </div>

            <div class="mb-3">
              <label for="cat_image" class="form-label">Cat Image</label>
              <input type="file" name="cat_image" class="form-control">
            </div>
             
            <div class="mb-3">
              <label for="cat_clip" class="form-label">Cat Video: Below 25MB</label>
             <input type="file" name="cat_clip" class="form-control" >
            </div>

            <div class="mb-3">
              <label for="age" class="form-label">Age</label>
              <input type="text" name="age" class="form-control">
            </div>

            <div class="mb-3">
              <label for="color" class="form-label">Color</label>
              <input type="text" name="color" class="form-control" required oninput="this.value = this.value.replace(/[,&]/g, ' ').toLowerCase()">
            </div>

            <div class="mb-3">
              <label for="breed" class="form-label">Breed</label>
              <input type="text" name="breed" class="form-control" required oninput="this.value = this.value.toLowerCase()">
            </div>

            <div class="mb-3">
              <label for="sex" class="form-label">Sex</label>
              <select name="sex" class="form-select" required>
                <option selected>Open this select menu</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
              </select>
            </div>

            <div class="mb-3">
              <label for="Medical_Record" class="form-label">Medical_Record</label>
              <input type="text" name="Medical_Record" class="form-control" required oninput="this.value = this.value.toLowerCase()">
            </div>

            <button type="submit" class="btn btn-primary mb-3" style="color: black;">Add Cat</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>