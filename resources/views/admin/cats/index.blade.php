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
            <h1>CAT GALLERY</h1>
            <button type="button" class="btn btn-secondary text-black" data-bs-toggle="modal" data-bs-target="#addCatModal">Add New Cat</button>
        </div>
        <div class="card-grid">
            @foreach($cats as $cat)
                <div class="card {{ $cat->status == 'Inactive' ? 'inactive' : '' }}" data-bs-toggle="modal" data-bs-target="#showCatModal" data-cat-id="{{ $cat->id }}">
                    <div class="card-image">
                        @if($cat->cat_image)
                            <img src="{{ asset('images/' . $cat->cat_image) }}" alt="Image of {{ $cat->cat_name }}" class="card-img">
                        @else
                            <span>No image</span>
                        @endif
                    </div>
                    <div class="card-content">
                    
                        <div class="heading">{{ $cat->cat_name }}</div>
                        
                        <div class="actions">
                            <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#editCatModal" 
                                data-cat-id="{{ $cat->id }}" 
                                data-cat-name="{{ $cat->cat_name }}" 
                                data-cat-image="{{ $cat->cat_image }}" 
                                data-cat-age="{{ $cat->age }}" 
                                data-cat-color="{{ $cat->color }}" 
                                data-cat-breed="{{ $cat->breed }}" 
                                data-cat-sex="{{ $cat->sex }}" 
                                data-cat-status="{{ $cat->status }}" 
                                data-cat-medical-record="{{ $cat->Medical_Record }}">
                                Edit
                            </button>
                            <form action="{{ route('admin.cats.destroy', $cat->id) }}" method="POST" style="display:inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-secondary">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    </div>

@include('admin.cats.create') 
@include('admin.cats.edit')
@include('admin.cats.show') 

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
      var catAge = button.getAttribute('data-cat-age');
      var catColor = button.getAttribute('data-cat-color');
      var catBreed = button.getAttribute('data-cat-breed');
      var catSex = button.getAttribute('data-cat-sex');
      var catStatus = button.getAttribute('data-cat-status');
      var catMedicalRecord = button.getAttribute('data-cat-medical-record');

      var modal = this;
      modal.querySelector('form').action = '/admin/cats/' + catId;
      modal.querySelector('input[name="cat_name"]').value = catName;
      modal.querySelector('input[name="age"]').value = catAge;
      modal.querySelector('input[name="color"]').value = catColor;
      modal.querySelector('input[name="breed"]').value = catBreed;
      modal.querySelector('select[name="sex"]').value = catSex;
      modal.querySelector('select[name="status"]').value = catStatus;
      modal.querySelector('input[name="Medical_Record"]').value = catMedicalRecord;
    });
  });
</script>
