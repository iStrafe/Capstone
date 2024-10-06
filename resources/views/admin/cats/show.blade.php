<!-- Modal -->
<div class="modal fade" id="showCatModal" tabindex="-1" aria-labelledby="showCatModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="showCatModalLabel">{{ $cat->cat_name }} Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        @if($cat->cat_image)
          <div>
            <img src="{{ asset('images/' . $cat->cat_image) }}" alt="Image of {{ $cat->cat_name }}" style="width: 300px; height: auto;">
          </div>
        @else
          <p>No image available for {{ $cat->cat_name }}</p>
        @endif

        <p><strong>Age:</strong> {{ $cat->age }} years</p>
        <p><strong>Color:</strong> {{ $cat->color }}</p>
        <p><strong>Breed:</strong> {{ $cat->breed }}</p>
        <p><strong>Sex:</strong> {{ $cat->sex }}</p>

        <a href="{{ route('admin.cats.index') }}" class="btn btn-secondary">Back to List</a>
      </div>
    </div>
  </div>
</div>