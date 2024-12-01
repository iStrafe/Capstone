@include('scripts')
@include('Services.adoptionForm')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adopt a Cat</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    @include('Navigationbar')
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
                        <button class="btn btn-primary btn-view-details" data-toggle="modal" data-target="#catDetailsModal"
                                data-name="{{ $cat->cat_name }}"
                                data-age="{{ $cat->age }}"
                                data-sex="{{ $cat->sex }}"
                                data-color="{{ $cat->color }}"
                                data-breed="{{ $cat->breed }}"
                                data-description="{{ $cat->description }}"
                                data-image="{{ $cat->cat_image ? asset('images/' . $cat->cat_image) : asset('images/default_cat.png') }}">View Details</button>
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

    <!-- Cat Details Modal -->
    <div class="modal fade" id="catDetailsModal" tabindex="-1" aria-labelledby="catDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="catDetailsModalLabel">Cat Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <img id="catImage" src="" alt="Cat Image" class="img-fluid mb-3">
                    <h5 id="catName"></h5>
                    <p id="catDescription"></p>
                    <ul>
                        <li>Age: <span id="catAge"></span></li>
                        <li>Sex: <span id="catSex"></span></li>
                        <li>Color: <span id="catColor"></span></li>
                        <li>Breed: <span id="catBreed"></span></li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.btn-view-details').forEach(button => {
            button.addEventListener('click', function() {
                const name = this.getAttribute('data-name');
                const age = this.getAttribute('data-age');
                const sex = this.getAttribute('data-sex');
                const color = this.getAttribute('data-color');
                const breed = this.getAttribute('data-breed');
                const description = this.getAttribute('data-description');
                const image = this.getAttribute('data-image');

                document.getElementById('catName').innerText = name;
                document.getElementById('catAge').innerText = age;
                document.getElementById('catSex').innerText = sex;
                document.getElementById('catColor').innerText = color;
                document.getElementById('catBreed').innerText = breed;
                document.getElementById('catDescription').innerText = description;
                document.getElementById('catImage').src = image;
            });
        });

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