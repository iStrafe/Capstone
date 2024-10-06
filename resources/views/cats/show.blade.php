@include('scripts')
@include('Navigationbar')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $cat->cat_name }} Details</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }

        .container {
            margin-top: 50px;
        }

        .cat-details-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .cat-details-card:hover {
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .cat-image {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        h1 {
            font-size: 2.5rem;
            color: #343a40;
            margin-bottom: 20px;
        }

        p {
            font-size: 1.2rem;
            color: #6c757d;
        }

        .cat-info {
            font-size: 1.1rem;
            color: #495057;
            margin: 10px 0;
        }

        .back-button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 1.1rem;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .back-button:hover {
            background-color: #0056b3;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="cat-details-card">
                    <h1>{{ $cat->cat_name }}'s Details</h1>

                    @if($cat->cat_image)
                        <img src="{{ asset('images/' . $cat->cat_image) }}" alt="Image of {{ $cat->cat_name }}" class="cat-image">
                    @else
                        <p>No image available for {{ $cat->cat_name }}</p>
                    @endif

                    <div class="cat-info">
                        <p><strong>Age:</strong> {{ $cat->age }} years</p>
                        <p><strong>Color:</strong> {{ $cat->color }}</p>
                        <p><strong>Breed:</strong> {{ $cat->breed }}</p>
                        <p><strong>Sex:</strong> {{ $cat->sex }}</p>
                    </div>

                    <a href="{{ route('user.cats.index') }}" class="btn back-button">Back to List</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
