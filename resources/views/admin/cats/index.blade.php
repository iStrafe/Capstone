@include('scripts')
@include('admin.adminNavbar')
<style>
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

    <div class="header-container py-5">
        <h1>Cat Gallery</h1>
        <button type="button" class="btn btn-secondary text-black" data-bs-toggle="modal" data-bs-target="#addCatModal">Add New Cat</button>
    </div>

    <div class="container">
        <div class="card-grid">
            @foreach($cats as $cat)
                <div class="card" data-bs-toggle="modal" data-bs-target="#showCatModal" data-cat-id="{{ $cat->id }}">
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
                            <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#editCatModal" data-cat-id="{{ $cat->id }}">Edit</button>
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

@include('admin.cats.create') 
@include('admin.cats.edit')
@include('admin.cats.show') 

</body>
</html>
