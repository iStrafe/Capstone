<!-- Modal: filled per card from the card's data-* attributes (see admin/cats/index.blade.php) -->
<div class="modal fade" id="showCatModal" tabindex="-1" aria-labelledby="showCatModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="showCatModalLabel"><span data-show="name">{{ $cat->cat_name }}</span> Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Display Cat Image -->
        <div data-show="image-wrap" @class(['d-none' => ! $cat->cat_image])>
          <img data-show="image" src="{{ $cat->cat_image ? asset('images/' . $cat->cat_image) : '' }}" alt="Image of {{ $cat->cat_name }}" style="width: 300px; height: auto;">
        </div>
        <p data-show="no-image" @class(['d-none' => (bool) $cat->cat_image])>No image available for <span data-show="name">{{ $cat->cat_name }}</span></p>

        <!-- Display Cat Video -->
        <div data-show="clip-wrap" @class(['text-center', 'mb-3', 'd-none' => ! $cat->cat_clip])>
          <video data-show="clip" controls preload="none" style="width: 100%; max-width: 500px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);" @if($cat->cat_clip) src="{{ asset('images/' . $cat->cat_clip) }}" @endif>
            Your browser does not support the video tag.
          </video>
        </div>
        <p data-show="no-clip" @class(['text-center', 'text-muted', 'd-none' => (bool) $cat->cat_clip])>No video available for <span data-show="name">{{ $cat->cat_name }}</span></p>

        <!-- Additional Details -->
        <p><strong>Age:</strong> <span data-show="age">{{ $cat->age !== null ? $cat->age.' years' : 'Unknown' }}</span></p>
        <p><strong>Color:</strong> <span data-show="color">{{ $cat->color }}</span></p>
        <p><strong>Breed:</strong> <span data-show="breed">{{ $cat->breed }}</span></p>
        <p><strong>Sex:</strong> <span data-show="sex">{{ $cat->sex }}</span></p>
        <p><strong>Status:</strong> <span data-show="status">{{ $cat->status }}</span></p>
        <p><strong>Medical Record:</strong> <span data-show="medical-record">{{ $cat->Medical_Record }}</span></p>

        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Back to List</button>
      </div>
    </div>
  </div>
</div>
