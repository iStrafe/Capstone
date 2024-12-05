<!-- Modal -->
<div class="modal fade" id="showCatModal" tabindex="-1" aria-labelledby="showCatModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="showCatModalLabel">{{ $cat->cat_name }} Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Display Cat Image -->
        @if($cat->cat_image)
          <div>
            <img src="{{ asset('images/' . $cat->cat_image) }}" alt="Image of {{ $cat->cat_name }}" style="width: 300px; height: auto;">
          </div>
        @else
          <p>No image available for {{ $cat->cat_name }}</p>
        @endif

        <!-- Display Cat Video -->
        @if($cat->cat_clip)
  <div class="text-center mb-3">
    <video controls style="width: 100%; max-width: 500px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
      <source src="{{ asset('images/' . $cat->cat_clip) }}" type="video/mp4">
      Your browser does not support the video tag.
    </video>
  </div>
@else
  <p class="text-center text-muted">No video available for {{ $cat->cat_name }}</p>
@endif


        <!-- Additional Details -->
        <p><strong>Age:</strong> {{ $cat->age }} years</p>
        <p><strong>Color:</strong> {{ $cat->color }}</p>
        <p><strong>Breed:</strong> {{ $cat->breed }}</p>
        <p><strong>Sex:</strong> {{ $cat->sex }}</p>
        <p><strong>Medical Record:</strong> {{ $cat->Medical_Record }}</p>

        <a href="{{ route('cats.index') }}" class="btn btn-secondary">Back to List</a>
      </div>
    </div>
  </div>
</div>
