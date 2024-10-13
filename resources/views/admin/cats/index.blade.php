@include('scripts')
@include('admin.adminNavbar')
<style>
    .card {
        display: flex;
        align-items: center;
        width: 400px;
        background: white;
        padding: 0.4em;
        border-radius: 6px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        cursor: pointer; /* Make the card look clickable */
    }

    .card-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px; /* Adjust the gap as needed */
    }

    .card-image {
        width: 100%;
        height: 100%;
        overflow: hidden;
    }

    .card-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .card-image:hover {
        transform: scale(0.98);
        transition: transform 0.3s ease;
    }

    .card-content {
        display: flex;
        flex-direction: column;
        justify-content: center;
        height: 20%;
    }

    .category {
        text-transform: uppercase;
        font-size: 0.7em;
        font-weight: 600;
        color: rgb(63, 121, 230);
        padding-bottom: 5px;
        text-align: left; /* Align text to the left */
    }

    .category:hover {
        cursor: pointer;
    }

    .heading {
        font-weight: 600;
        color: rgb(88, 87, 87);
        margin-bottom: 10px;
        text-align: left; /* Align text to the left */
    }

    .heading:hover {
        cursor: pointer;
    }

    .author {
        color: gray;
        font-weight: 400;
        font-size: 11px;
        text-align: left; /* Align text to the left */
    }

    .name {
        font-weight: 600;
    }

    .name:hover {
        cursor: pointer;
    }

    .header-container {
        text-align: center;
        margin-bottom: 20px;
    }

    .header-container h1 {
        font-size: 2.5em;
        margin-bottom: 10px;
    }

    .header-container .btn {
        font-size: 1.2em;
    }
</style>
<!DOCTYPE html>
<title>Cats list</title>
<body>

        <div class="header-container py-5">
            <h1>Cat Gallery</h1>
            <button type="button" class="btn btn-success text-white" data-bs-toggle="modal" data-bs-target="#addCatModal">Add New Cat</button>
        </div>

        <!--<div class="center">
            <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Search Cat" aria-label="Recipient's username" aria-describedby="basic-addon2" id="searchInput">
            </div>
        </div>-->


<div class="container-md">
    <div class="card-grid">
        @foreach($cats as $cat)
            <div class="card" data-bs-toggle="modal" data-bs-target="#showCatModal" data-cat-id="{{ $cat->id }}">
                <div class="card-image">
                    @if($cat->cat_image)
                        <img src="{{ asset('images/' . $cat->cat_image) }}" alt="Image of {{ $cat->cat_name }}" class="card-img">
                    @else
                        No image
                    @endif
                </div>
                <div class="card-content">
                    <div class="heading">
                        {{ $cat->cat_name }}
                    </div>
                    <div class="actions">
                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#editCatModal" data-cat-id="{{ $cat->id }}">Edit</button>
                        <form action="{{ route('admin.cats.destroy', $cat->id) }}" method="POST" style="display:inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-info">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

@include('admin.cats.create') 
@include('admin.cats.edit')
@include('admin.cats.show') 

</body>
</html>