<!-- Modal -->
<div class="modal fade" id="editCatModal" tabindex="-1" aria-labelledby="editCatModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editCatModalLabel">Edit Cat</h5>
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

        <form id="editCatForm" action="{{ route('admin.cats.update', $cat->id) }}" method="POST" enctype="multipart/form-data">
          @csrf
          @method('PUT')

          <div class="container-sm">
            <div class="mb-3">
              <label for="cat_name" class="form-label">Cat Name</label>
              <input type="text" name="cat_name" class="form-control" value="{{ $cat->cat_name }}" required>
            </div>

            <div class="mb-3">
              <label for="cat_image" class="form-label">Cat Image</label>
              <input type="file" name="cat_image" class="form-control">
            </div>

            <div class="mb-3">
              <label for="age" class="form-label">Age</label>
              <input type="number" name="age" class="form-control" value="{{ $cat->age }}" required>
            </div>

            <div class="mb-3">
              <label for="color" class="form-label">Color</label>
              <input type="text" name="color" class="form-control" value="{{ $cat->color }}" required>
            </div>

            <div class="mb-3">
              <label for="breed" class="form-label">Breed</label>
              <input type="text" name="breed" class="form-control" value="{{ $cat->breed }}" required>
            </div>

            <div class="mb-3">
              <label for="sex" class="form-label">Sex</label>
              <select name="sex" class="form-select" required>
                <option value="Male" {{ $cat->sex == 'Male' ? 'selected' : '' }}>Male</option>
                <option value="Female" {{ $cat->sex == 'Female' ? 'selected' : '' }}>Female</option>
              </select>
            </div>

            <button type="submit" class="btn btn-primary mb-3">Update Cat</button>
            <button type="button" class="btn btn-secondary mb-3" onclick="clearForm()">Clear</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  function clearForm() {
    document.getElementById('editCatForm').reset();
  }
</script>