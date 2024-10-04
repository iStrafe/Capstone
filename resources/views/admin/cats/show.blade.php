@include('scripts')
@include('admin.adminNavbar')
<!DOCTYPE html>
    <title>Details</title>
        <body>
    <h1>{{ $cat->cat_name }} Details</h1>
    
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

    <a href="{{ route('admin.cats.index') }}">Back to List</a>
        </body>
</html>