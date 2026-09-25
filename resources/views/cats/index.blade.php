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
        @if ($errors->any())
            <div class="alert alert-danger">
                <p>Your adoption request was not sent:</p>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="card-container">
            @foreach($cats as $cat)
                <div class="card cat-card">
                    @if($cat->cat_image)
                        <img src="{{ asset('images/' . $cat->cat_image) }}" alt="{{ $cat->cat_name }}">
                    @else
                        {{-- Stand-in picture for cats without a photo --}}
                        <img src="{{ asset('images/placeholder.png') }}" alt="No Image Available">
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
                                data-clip="{{ $cat->cat_clip ? asset('images/' . $cat->cat_clip) : '' }}"
                                data-image="{{ $cat->cat_image ? asset('images/' . $cat->cat_image) : asset('images/placeholder.png') }}">View Details</button>
                        @if(in_array($cat->id, $requestedCatIds))
                        <button type="button" class="btn btn-secondary" style="margin-top: 10px;" disabled>Request sent</button>
                        @elseif(! $cat->hasRoomForRequests())
                        <button type="button" class="btn btn-secondary" style="margin-top: 10px;" disabled title="This cat already has {{ \App\Models\Cat::MAX_PENDING_REQUESTS }} adoption requests waiting for a decision">Requests full</button>
                        @else
                        @auth
                        <a href="#" class="btn btn-adopt" data-toggle="modal" data-target="#adoptionFormModal"
                           data-id="{{ $cat->id }}"
                           data-name="{{ $cat->cat_name }}"
                           data-age="{{ $cat->age }}"
                           data-sex="{{ $cat->sex }}"
                           data-color="{{ $cat->color }}"
                           data-breed="{{ $cat->breed }}">Proceed to Adopt</a>
                        @else
                        <a href="{{ route('login') }}" class="btn btn-adopt">Log in to adopt</a>
                        @endauth
                        @endif
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
                    {{-- Filled per cat by the View Details script; this modal is shared by every card. --}}
                    <div class="text-center mb-3" id="catVideoWrap" style="display: none;">
                        <video id="catVideo" controls style="width: 100%; max-width: 500px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                    <p class="text-center text-muted" id="catNoVideo">No video available for this cat</p>
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
                const clip = this.getAttribute('data-clip');
                const video = document.getElementById('catVideo');
                video.pause();
                if (clip) {
                    video.src = clip;
                } else {
                    video.removeAttribute('src');
                }
                video.load();
                document.getElementById('catVideoWrap').style.display = clip ? '' : 'none';
                document.getElementById('catNoVideo').style.display = clip ? 'none' : '';
            });
        });

        document.querySelectorAll('.btn-adopt').forEach(button => {
            button.addEventListener('click', function() {
                // Only the cat's id is submitted; the other fields just show which cat was picked.
                document.querySelector('input[name="cat_id"]').value = this.getAttribute('data-id');
                document.getElementById('adopt_cat_name').value = this.getAttribute('data-name');
                document.getElementById('adopt_cat_age').value = this.getAttribute('data-age');
                document.getElementById('adopt_cat_sex').value = this.getAttribute('data-sex');
                document.getElementById('adopt_cat_color').value = this.getAttribute('data-color');
                document.getElementById('adopt_cat_breed').value = this.getAttribute('data-breed');
            });
        });
    </script>
</body>
</html>