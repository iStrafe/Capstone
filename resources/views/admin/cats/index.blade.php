@include('scripts')
@include('admin.adminNavbar')
<style>
        .section{
            width: 100%;
            min-height: 60vh;
        }

    .container{
                width: 100%;
                height: 5vh;
                padding: 0 10%;
                height: 60vh;
                padding: 0 8%;
                padding-top: 1px;
            }

            .container h1{
                font-size: 40px;
                text-align: center;
                padding-top: 5%;
                font-weight: 600;
                position: relative;
            }

    /* Inactive card overlay */
    .card.inactive::before {
        content: "Inactive";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5em;
        font-weight: bold;
        border-radius: 12px;
        z-index: 1;
    }
    /* Basic card styles */
    .card {
        display: flex;
        flex-direction: column;
        align-items: center;
        background: white;
        padding: 1.5em;
        border-radius: 12px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        cursor: pointer;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
        transform: scale(1.05);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
    }

    /* Card grid setup */
    .card-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 20px;
    }

    /* Image styling */
    .card-image {
        width: 100%;
        height: 200px;
        overflow: hidden;
        border-radius: 8px;
    }

    .card-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .card-image:hover .card-img {
        transform: scale(1.1);
    }

    /* Text content styles */
    .card-content {
        padding-top: 1em;
        text-align: center;
        width: 100%;
    }

    .category {
        font-size: 1em;
        font-weight: 600;
        color: #3f79e6;
        margin-bottom: 8px;
        text-transform: uppercase;
    }

    .heading {
        font-weight: 600;
        font-size: 1.2em;
        color: #595959;
        margin-bottom: 12px;
        word-wrap: break-word; /* Ensure long words wrap and stay within bounds */
    }

    .author {
        font-size: 1em;
        color: gray;
        font-weight: 400;
        word-wrap: break-word; /* Ensure long words wrap and stay within bounds */
    }

    .actions button {
        margin: 5px;
        padding: 8px 16px;
        font-size: 1em;
    }

    /* Header styling */
    .header-container {
        text-align: center;
        margin-bottom: 10px;
    }

    .header-container h1 {
        font-size: 2.5em;
        margin-bottom: 10px;
        color: #333;
    }

    .header-container .btn {
        font-size: 1.2em;
    }

    .btn-secondary {
    color: black; /* Set text color to black */
    }

    /* The reset in the shared scripts partial clears the background of [type=submit] buttons. */
    #archiveCatModal .btn {
        background-color: var(--bs-btn-bg);
    }

    #archiveCatModal .btn:hover {
        background-color: var(--bs-btn-hover-bg);
    }
    

    /* Mobile responsiveness */
    @media (max-width: 768px) {
        .card-grid {
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        }

        .card {
            padding: 1.5em;
        }

        .header-container h1 {
            font-size: 2em;
        }

        .card-img {
            height: 180px;
        }
    }

    @media (max-width: 480px) {
        .header-container h1 {
            font-size: 1.8em;
        }

        .category, .heading, .author {
            font-size: 1em;
        }

        .card-content {
            padding-top: 1em;
        }

        .actions button {
            font-size: 1em;
        }
    }
</style>

<!DOCTYPE html>
<title>Cats list</title>
<body>


    <div class="section">
        <div class="container">
        <div class="header-container">
            <h1>CAT INVENTORY</h1>
            <button type="button" class="btn btn-secondary text-black" data-bs-toggle="modal" data-bs-target="#addCatModal">Add New Cat</button>
        </div>
        <div class="card-grid">
            @foreach($cats as $cat)
                <div class="card {{ $cat->status == 'Inactive' ? 'inactive' : '' }}" data-bs-toggle="modal" data-bs-target="#showCatModal" data-cat-id="{{ $cat->id }}"
                    data-cat-name="{{ $cat->cat_name }}"
                    data-cat-image-url="{{ $cat->cat_image ? asset('images/' . $cat->cat_image) : '' }}"
                    data-cat-clip-url="{{ $cat->cat_clip ? asset('images/' . $cat->cat_clip) : '' }}"
                    data-cat-age="{{ $cat->age !== null ? $cat->age.' years' : 'Unknown' }}"
                    data-cat-color="{{ $cat->color }}"
                    data-cat-breed="{{ $cat->breed }}"
                    data-cat-sex="{{ $cat->sex }}"
                    data-cat-status="{{ $cat->status }}"
                    data-cat-medical-record="{{ $cat->Medical_Record }}">
                    <div class="card-image">
                        @if($cat->cat_image)
                            <img src="{{ asset('images/' . $cat->cat_image) }}" alt="Image of {{ $cat->cat_name }}" class="card-img">
                        @else
                            <span>No image</span>
                        @endif

                        @if($cat->cat_clip)
                         <div>
                         <video controls width="300">
                          <source src="{{ asset('images/' . $cat->cat_clip) }}" type="video/mp4">
                          Your browser does not support the video tag.
                            </video>
                           </div>
                         @else
                         <p>No video available for {{ $cat->cat_name }}</p>
                           @endif
                    </div>
                  
                    <div class="card-content">
                    
                        <div class="heading">{{ $cat->cat_name }}</div>
                        @if($cat->is_adopted)
                            <p><span class="badge bg-success">Adopted</span></p>
                        @elseif($cat->is_reserved)
                            <p><span class="badge bg-info text-dark">Adoption in progress</span></p>
                        @endif

                        <div class="actions">
                            <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#editCatModal" 
                                data-cat-id="{{ $cat->id }}" 
                                data-cat-name="{{ $cat->cat_name }}" 
                                data-cat-image="{{ $cat->cat_image }}"
                                data-cat-clip="{{ $cat->cat_clip }}"
                                data-cat-age="{{ $cat->age }}" 
                                data-cat-color="{{ $cat->color }}" 
                                data-cat-breed="{{ $cat->breed }}" 
                                data-cat-sex="{{ $cat->sex }}" 
                                data-cat-status="{{ $cat->status }}" 
                                data-cat-medical-record="{{ $cat->Medical_Record }}">
                                Edit
                            </button>
                            <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#archiveCatModal"
                                data-cat-id="{{ $cat->id }}"
                                data-cat-name="{{ $cat->cat_name }}">
                                Archive
                            </button>

                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    </div>

@include('admin.cats.create') 
{{-- These modals render outside the loop; fall back to an empty cat so an empty inventory still renders. --}}
@include('admin.cats.edit', ['cat' => $cats->last() ?? new \App\Models\Cat])
@include('admin.cats.show', ['cat' => $cats->last() ?? new \App\Models\Cat])

{{-- Shared by every card's Archive button; the script below points the form at the clicked cat. --}}
<div class="modal fade" id="archiveCatModal" tabindex="-1" aria-labelledby="archiveCatModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form class="modal-content" method="POST" action="">
            @csrf
            @method('PATCH')
            <div class="modal-header">
                <h5 class="modal-title" id="archiveCatModalLabel">Archive <span data-archive="name"></span>?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>The cat leaves the inventory and the adoption list. You can restore it from View Archived Cats.</p>
                <label for="archive_reason" class="form-label">Reason (optional, shown on the cat's public page)</label>
                <textarea class="form-control" id="archive_reason" name="archive_reason" rows="3" maxlength="255" placeholder="e.g. Moved to a partner shelter"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger">Archive</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    var editCatModal = document.getElementById('editCatModal');
    editCatModal.addEventListener('show.bs.modal', function (event) {
      var button = event.relatedTarget;
      var catId = button.getAttribute('data-cat-id');
      var catName = button.getAttribute('data-cat-name');
      var catImage = button.getAttribute('data-cat-image');
      var cat_clip = button.getAttribute('data-cat-clip');
      var catAge = button.getAttribute('data-cat-age');
      var catColor = button.getAttribute('data-cat-color');
      var catBreed = button.getAttribute('data-cat-breed');
      var catSex = button.getAttribute('data-cat-sex');
      var catStatus = button.getAttribute('data-cat-status');
      var catMedicalRecord = button.getAttribute('data-cat-medical-record');

      var modal = this;
      modal.querySelector('form').action = @json(route('admin.cats.update', '__CAT__')).replace('__CAT__', catId);
      modal.querySelector('input[name="cat_name"]').value = catName;
      modal.querySelector('input[name="age"]').value = catAge;
      modal.querySelector('input[name="color"]').value = catColor;
      modal.querySelector('input[name="breed"]').value = catBreed;
      modal.querySelector('select[name="sex"]').value = catSex;
      modal.querySelector('select[name="status"]').value = catStatus;
      modal.querySelector('input[name="Medical_Record"]').value = catMedicalRecord;
    });

    // Point the Archive form at the clicked cat and start with an empty reason.
    var archiveCatModal = document.getElementById('archiveCatModal');
    archiveCatModal.addEventListener('show.bs.modal', function (event) {
      var button = event.relatedTarget;
      if (!button) return;
      var form = archiveCatModal.querySelector('form');
      form.action = @json(route('admin.cats.archive', '__CAT__')).replace('__CAT__', button.getAttribute('data-cat-id'));
      form.querySelector('textarea[name="archive_reason"]').value = '';
      archiveCatModal.querySelector('[data-archive="name"]').textContent = button.getAttribute('data-cat-name');
    });

    // The whole card toggles the View modal, and Bootstrap listens for that in the capture
    // phase, so stopPropagation on a button cannot stop it. Remember whether the click came
    // from the card's Edit/Archive buttons and cancel the View modal in that case.
    var clickFromCardActions = false;
    window.addEventListener('click', function (event) {
      clickFromCardActions = !!(event.target.closest && event.target.closest('.card .actions'));
    }, true);

    // Fill the View modal from the clicked card (it is rendered once, outside the loop).
    var showCatModal = document.getElementById('showCatModal');
    showCatModal.addEventListener('show.bs.modal', function (event) {
      var card = event.relatedTarget;
      if (clickFromCardActions) {
        clickFromCardActions = false;
        event.preventDefault();
        return;
      }
      if (!card) return;
      var data = function (name) { return card.getAttribute('data-cat-' + name) || ''; };
      var field = function (name) { return showCatModal.querySelectorAll('[data-show="' + name + '"]'); };
      var toggle = function (name, hidden) { field(name).forEach(function (el) { el.classList.toggle('d-none', hidden); }); };

      ['name', 'age', 'color', 'breed', 'sex', 'status', 'medical-record'].forEach(function (name) {
        field(name).forEach(function (el) { el.textContent = data(name); });
      });

      var imageUrl = data('image-url');
      var image = field('image')[0];
      image.src = imageUrl;
      image.alt = 'Image of ' + data('name');
      toggle('image-wrap', !imageUrl);
      toggle('no-image', !!imageUrl);

      var clipUrl = data('clip-url');
      var clip = field('clip')[0];
      clip.pause();
      if (clipUrl) { clip.src = clipUrl; } else { clip.removeAttribute('src'); clip.load(); }
      toggle('clip-wrap', !clipUrl);
      toggle('no-clip', !!clipUrl);
    });
  });
</script>
