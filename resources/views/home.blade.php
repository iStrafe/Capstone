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
    @vite('resources/sass/app.scss')
    @vite('resources/js/app.js')
    @include('scripts')
   
    <style>
    body {
        font-family: 'Figtree', sans-serif;
        background-color: #d0e7ff;
        background-size: cover;
        color: #333;
        margin: 0;
        padding: 0;
    }

    .navbar {
        background-color: #03045E;
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
        margin: 0 auto 20px;
        display: block;
    }

    .center {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .input-group {
        width: 100%;
        max-width: 500px;
    }

    /* Center the card container */
    .card-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: center; /* Center the cards horizontally */
        gap: 1rem;
        margin: 0 auto; /* Ensures the container is centered in the middle */
    }

    .card {
        width: 100%;
        max-width: 300px; /* Set maximum width for each card */
        height: auto;
        box-shadow: 0px 1px 13px rgba(0,0,0,0.1);
        cursor: pointer;
        transition: all 120ms;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: space-between;
        background: #fff;
        padding: 1em;
        position: relative;
    }

    .card .title {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 1em;
        font-weight: 600;
        color: #000;
        margin-bottom: 15px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .card .image {
        background: rgb(241, 241, 241);
        width: 100%;
        height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .card .image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .card .price {
        font-family: Impact, Haettenschweiler, 'Arial Narrow Bold', sans-serif;
        font-size: 1em;
        color: #000;
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

    /* Make the layout mobile responsive */
    @media (max-width: 768px) {
        .card-container {
            justify-content: center; /* Center the cards on mobile */
        }

        .card {
            max-width: 100%; /* Allow cards to take full width on smaller screens */
            margin-bottom: 1rem;
        }
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
                @include('Services.adoptContract')
            </ul>
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

        <!-- Next & previous buttons -->
        <div class="pagination-buttons">
            <button class="btn btn-outline-secondary" type="button" id="prevBtn" style="display: none;">Previous</button>
            <button class="btn btn-outline-secondary" type="button" id="nextBtn" style="display: none;">Next</button>
        </div>

        <!-- Card container -->
        <div class="card-container" id="catGallery">
            @foreach($cats as $cat)
                <div class="col-md-3 cat-card" data-bs-toggle="modal" data-bs-target="#modalId2">
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
