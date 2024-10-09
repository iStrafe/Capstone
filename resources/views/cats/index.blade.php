@include('Navigationbar') <!-- Include your navbar for navigation -->
@include('scripts')
@include('Services.adoptionForm')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adopt a Cat</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .card-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
        }

        .cat-card {
            width: 18rem;
            margin: 1rem;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        .cat-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .cat-card .card-body {
            text-align: center;
        }

        .btn-adopt {
            background-color: #28a745;
            color: white;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Meet Our Cats Available for Adoption</h1>
                        <div class="input-group mb-3 px-5">
                            <input type="text" class="form-control" placeholder="Search Cat" aria-label="Recipient's username" aria-describedby="basic-addon2" id="searchInput">
                        </div>
        <div class="card-container">
            @foreach($cats as $cat)
                <div class="card cat-card">
                    @if($cat->cat_image)
                        <img src="{{ asset('images/' . $cat->cat_image) }}" alt="{{ $cat->cat_name }}">
                    @else
                        <img src="{{ asset('images/default_cat.png') }}" alt="No Image Available">
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{ $cat->cat_name }}</h5>
                        <a href="{{ route('user.cats.show', $cat->id) }}" class="btn btn-primary">View Details</a>
                        <a href="#" class="btn btn-adopt" data-toggle="modal" data-target="#adoptionFormModal"
                           data-name="{{ $cat->cat_name }}"
                           data-age="{{ $cat->age }}"
                           data-sex="{{ $cat->sex }}"
                           data-color="{{ $cat->color }}"
                           data-breed="{{ $cat->breed }}">Proceed to Adopt</a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script>
        document.querySelectorAll('.btn-adopt').forEach(button => {
            button.addEventListener('click', function() {
                const name = this.getAttribute('data-name');
                const age = this.getAttribute('data-age');
                const sex = this.getAttribute('data-sex');
                const color = this.getAttribute('data-color');
                const breed = this.getAttribute('data-breed');

                document.querySelector('input[name="name_of_cat"]').value = name;
                document.querySelector('input[name="approximate_age"]').value = age;
                document.querySelector('input[name="sex"]').value = sex;
                document.querySelector('input[name="color"]').value = color;
                document.querySelector('input[name="breed"]').value = breed;
            });
        });
    </script>
</body>
</html>