<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AdUCats</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @include('scripts')
    <!--@include('scripts2')-->
   
    <style>
        body {
            font-family: 'Figtree', sans-serif;
            background-color: #d0e7ff; /* Light blue background */
            background-size: cover;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .navbar {
            background-color: #03045E; /* Adamson blue */
            padding: 1rem;
        }
        .navbar a {
            color: white;
            margin-right: 1rem;
        }
        .navbar .dropdown-menu {
            background-color: #003366;
        }
        .logo {
            max-width: 150px;
            margin: 0 auto 20px; /* Center the logo and add space below */
            display: block;
        }
        .center {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .input-group {
            width: 100%;
            max-width: 500px; /* Adjust the maximum width of the input group */
        }

           .card {
        flex-wrap: wrap;
        position: relative;
        width: 25em;
        height: 25em;
        box-shadow: 0px 1px 13px rgba(0,0,0,0.1);
        cursor: pointer;
        transition: all 120ms;
        display: flex;
        align-items: center;
        justify-content: space-around;
        background: #fff;
        padding: 0.5em;
        padding-bottom: 3.4em;
    }

    .card::after {
        content: "Adopt Me!";
        padding-top: 1.25em;
        padding-left: 1.25em;
        position: absolute;
        left: 0;
        bottom: -60px;
        background: #00AC7C;
        color: #fff;
        height: 4em;
        width: 100%;
        transition: all 80ms;
        font-weight: 600;
        text-transform: uppercase;
        opacity: 0;
    }

    .card .title {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 0.9em;
        position: absolute;
        left: 0.625em;
        bottom: 1.875em;
        font-weight: 400;
        color: #000;
        margin-bottom: 15px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .card .price {
        font-family: Impact, Haettenschweiler, 'Arial Narrow Bold', sans-serif;
        font-size: 0.9em;
        position: absolute;
        left: 0.625em;
        bottom: 0.625em;
        color: #000;
    }

    .card:hover::after {
        bottom: 0;
        opacity: 1;
    }

    .card:active {
        transform: scale(0.98);
    }

    .card:active::after {
        content: "Added !";
        height: 3.125em;
    }

    .text {
        max-width: 55px;
    }

    .image {
        background: rgb(241, 241, 241);
        width: 100%;
        height: 70%; /* Adjust the height as needed */
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        background: rgb(241, 241, 241);
        display: grid;
        place-items: center;
    }
    </style>
</head>

<body class="font-sans antialiased">
<nav class="navbar navbar-expand-lg border-bottom border-body" data-bs-theme="dark">
    <div class="container-fluid">
             @if(Auth::check() && Auth::user()->role === "admin")
                <a class="button" data-text="Awesome" href="{{url('adminDashboard')}}">
                    <span class="actual-text">&nbsp;adminpage&nbsp;</span>
                    <span aria-hidden="true" class="hover-text">&nbsp;adminpage&nbsp;</span>
                </a>
                <!--<a class="navbar-brand px-5 py-3" href="{{url('adminDashboard')}}">adminpage</a>-->
            @endif

        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
           
        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="navbar-nav">
                <a class="nav-link" href="{{ url('/') }}">HOME</a>
                <a class="nav-link" href="{{ url('aboutus') }}">ABOUT US</a>
                <a class="nav-link" data-bs-toggle="modal" data-bs-target="#modalId2">ADOPT</a>
                @include('Services.adoptContract')
                <a class="nav-link" href="{{ url('events') }}">NEWS / EVENTS</a>
                <a class="nav-link" href="{{ url('ContactUs') }}">CONTACT US</a>
                <a class="nav-link" href="{{ url('analyzeImage') }}">IMAGE ANALYSIS</a>
                <button type="button" class="btn btn-secondary btn-lg" data-bs-toggle="modal" data-bs-target="#modalId">DONATE</button>
                @include('payment')
            </div>

            <ul class="navbar-nav ms-auto">
                @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                    </li>
                @else
                    <li class="nav-item dropdown">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <div>{{ Auth::user()->name }}</div>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">{{ __('Profile') }}</a>
                            <a class="dropdown-item" href="{{ route('logout') }}"
                               onclick="event.preventDefault();
                               document.getElementById('logout-form').submit();">
                                {{ __('Log Out') }}
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>

        <!-- Masthead-->
        <header class="masthead">
            <div class="container2"><br><br>
                <div class="masthead-subheading">Welcome To Our AduCats Website!</div>
                <div class="masthead-heading text-uppercase">It's Nice To Meet You</div>
            </div>
        </header>

      <section class="bg-blacks">
            <div class="container">
               
                    <div class="center">
                    <h1>CAT GALLERY</h1>
                        <div class="input-group mb-3">
                                <input type="text" class="form-control" placeholder="Search Cat" aria-label="Recipient's username" aria-describedby="basic-addon2" id="searchInput">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" id="searchButton">search</button>
                            </div>
                        </div>
                    </div>
                    <!--Next & previous buttons-->
                    <div class="pagination-buttons">
                        <button class="btn btn-outline-secondary" type="button" id="prevBtn" style="display: none;">Previous</button>
                        <button class="btn btn-outline-secondary" type="button" id="nextBtn" style="display: none;">Next</button>
                    </div>

                <div class="row" id="catGallery">
                    @foreach($cats as $cat)
                        <div class="col-md-4 cat-card" data-bs-toggle="modal" data-bs-target="#modalId2">
                            <div class="card" data-name="{{ $cat->cat_name }}" data-age="{{ $cat->age }}" data-color="{{ $cat->color }}" data-breed="{{ $cat->breed }}" data-sex="{{ $cat->sex }}">
                                <div class="image">
                                    @if($cat->cat_image)
                                        <img src="{{ asset('images/' . $cat->cat_image) }}" alt="Image of {{ $cat->cat_name }}" style="width: 100%; height: auto;">
                                    @else
                                        <span class="text">No image available</span>
                                    @endif
                                </div>
                                <span class="title">{{ $cat->cat_name }}</span>
                               
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
    </section>  
        
</body>
</html>
    
   <script>
    document.getElementById('searchInput').addEventListener('input', function() {
        const searchInput = document.getElementById('searchInput').value.toLowerCase();
        const searchTerms = searchInput.split(/[\s,]+/); // Split by spaces or commas
        const catCards = document.querySelectorAll('.cat-card');

        if (searchInput.trim() !== "") {
            catCards.forEach(function(card) {
                const catName = card.querySelector('.title').textContent.toLowerCase();
                const catAge = card.querySelector('.card').getAttribute('data-age').toLowerCase();
                const catColor = card.querySelector('.card').getAttribute('data-color').toLowerCase();
                const catBreed = card.querySelector('.card').getAttribute('data-breed').toLowerCase();
                const catSex = card.querySelector('.card').getAttribute('data-sex').toLowerCase();

                const cardText = `${catName} ${catAge} ${catColor} ${catBreed} ${catSex}`;

                const matches = searchTerms.every(term => cardText.includes(term));

                if (matches) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        } else {
            catCards.forEach(function(card) {
                card.style.display = 'block';
            });
        }
    });
</script>