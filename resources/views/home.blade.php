<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AdUCats</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=login" />
    
    <!-- Styles -->
    @vite('resources/sass/app.scss')
    @vite('resources/js/app.js')
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

                .container{
                width: 100%;
                height: 5vh;
                padding: 0 10%;
                height: 60vh;
                padding: 0 8%;
            }

            .container h1{
                font-size: 40px;
                text-align: center;
                padding-top: 5%;
                font-weight: 600;
                position: relative;
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
            /* Make inactive cards unclickable */
            .card.inactive {
                pointer-events: none;
            }

             .mast-header {
                max-width: 100%;
                height: 70vh;
                margin: 0;
                padding: 0;
                overflow: hidden;
            }

    .main-container {
        width: 100%;
        height: 100%;
        display: flex;
        background-color: #003087; /* Ensure proper hex syntax */
    }

    .main-container .image-preview {
        flex: 1;
        position: relative;
        overflow: hidden;
        transition: flex 1s cubic-bezier(0.08, 0.82, 0.17, 1);
    }

    .main-container .image-preview:hover {
        flex: 2;
    }

    .main-container .image-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: opacity 1s cubic-bezier(0.08, 0.82, 0.17, 1);
    }

    .main-container .image-preview:hover img {
        opacity: 0.5;
    }

    .main-container .image-preview .overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 50%;
        background: linear-gradient(180deg, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, 1) 100%);
        opacity: 0;
        transform: translateY(10%);
        transition: all 1s cubic-bezier(0.08, 0.82, 0.17, 1);
    }

    .main-container .image-preview:hover .overlay {
        opacity: 1;
        transform: translateY(0);
    }

    .main-container .image-preview .overlay .desc {
        position: absolute;
        bottom: 3rem;
        padding: 1rem;
        opacity: 0;
        transform: translateY(100%);
        transition: all 0.5s cubic-bezier(0.23, 1, 0.32, 1);
        color: #fff;
    }

    .main-container .image-preview .overlay .desc h1 {
        margin-bottom: 1rem;
        font-size: 1.5rem;
        text-transform: uppercase;
    }

    .main-container .image-preview:hover .overlay .desc {
        opacity: 1;
        transform: translateY(0);
    }

    .main-container .image-preview video {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: none;
    }

    .main-container .image-preview:hover video {
        display: block;
    }

            /* Mobile view adjustments */
            @media (max-width: 768px) {
                .navbar-nav {
                    flex-direction: column; /* Stack links vertically on small screens */
                    align-items: left;
                }

                .navbar .container-fluid {
                    justify-content: left; /* Center the navbar content */
                }

                .navbar-toggler {
                    display: block; /* Show the toggle button on smaller screens */
                }

                .navbar-nav .nav-item {
                    margin: 10px 0; /* Adjust space between items on mobile */
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
                <!--<a class="navbar-brand px-5 py-3" href="{{url('adminDashboard')}}">adminpage</a>-->
            @endif

        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
           
        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="navbar-nav">
                <a class="nav-link" href="{{ url('/') }}">HOME</a>
                <a class="nav-link" href="{{ url('aboutus') }}">ABOUT US</a>
                <a class="nav-link" href="{{ url('events') }}">NEWS / EVENTS</a>
                <a class="nav-link" href="{{ url('ContactUs') }}">CONTACT US</a>
                
                <a class="nav-link" data-bs-toggle="modal" data-bs-target="#modalId2">ADOPT</a>
                <a class="nav-link" data-bs-toggle="modal" data-bs-target="#modalId">DONATE</a>
                @include('payment')
               <!-- <li class="nav-item px-4">
                    <a class="nav-link" href="{{ url('myRequest') }}">---</a>
                </li>-->
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
    </div>
</nav>

        <!-- Adoption contract modal-->
        <div data-bs-theme="dark">
            @include('Services.adoptContract')
        </div>
            
        <!-- Masthead -->
        <div class="mast-header py-1">
            <div class="main-container">
            <div class="image-preview">
                    <img src="https://scontent.fmnl30-3.fna.fbcdn.net/v/t1.15752-9/467474751_937653994952718_4473491335332974707_n.png?_nc_cat=105&ccb=1-7&_nc_sid=9f807c&_nc_eui2=AeGlh9F1gZrdZiC1hVjuKGi_fSgS73JsVzB9KBLvcmxXMAYlnE1FsHvEboJxiI77YemdwOk0zyklY2rSNK1dPdiL&_nc_ohc=gsyOSDjx5AEQ7kNvgGAsUJj&_nc_zt=23&_nc_ht=scontent.fmnl30-3.fna&oh=03_Q7cD1QENp_nA26yYR7VSyvbJ88HDtjrDXO48dbO3F58FGo6o2g&oe=67698B65" alt="Adopt a cat">
                    <video loop muted playsinline>
                        <source src="https://media.giphy.com/media/gVsmn4qdyBn1Bra2tN/giphy.webm" type="video/webm">
                    </video>
                    <span class="overlay">
                        <div class="desc">
                            <h1 class="masthead-heading">Welcome to the AduCats Website!</h1>
                        </div>
                    </span>
                </div>

                <div class="image-preview">
                    <img src="https://aducats.online/images/1730883560.png" alt="Adopt a cat">
                    <video loop muted playsinline>
                        <source src="https://aducats.online/images/1730883560.png" type="video/webm">
                    </video>
                    <span class="overlay">
                        <div class="desc">
                            <h1 class="masthead-heading">Adopt A Cat Today!</h1>
                        </div>
                    </span>
                </div>

                <div class="image-preview">
                    <img src="https://aducats.online/images/1730882812.png" alt="Adopt a cat">
                    <video loop muted playsinline>
                        <source src="https://media.giphy.com/media/gVsmn4qdyBn1Bra2tN/giphy.webm" type="video/webm">
                    </video>
                    <span class="overlay">
                        <div class="desc">
                            <h1 class="masthead-heading">Help Our Fur-iends by Donating!</h1>
                        </div>
                    </span>
                </div>

                <div class="image-preview">
                    <img src="https://aducats.online/images/1730883440.png" alt="Nice to meet you">
                    <video loop muted playsinline>
                        <source src="https://media2.giphy.com/media/WXB88TeARFVvi/200w.webm" type="video/webm">
                    </video>
                    <span class="overlay">
                        <div class="desc">
                            <h1 class="masthead-heading">It's Nice To Meet You</h1>
                        </div>
                    </span>
                </div>

                <div class="image-preview">
                    <img src="images/1732147692.png" alt="Support our mission">
                    <video loop muted playsinline>
                        <source src="https://media3.giphy.com/media/t4ujDuOYqa1OoJkwyU/giphy.webm" type="video/webm">
                    </video>
                    <span class="overlay">
                        <div class="desc">
                            <h1 class="masthead-subheading">Support Our Mission</h1>
                        </div>
                    </span>
                </div>

                <div class="image-preview">
                    <img src="https://aducats.online/images/1731848858.png" alt="Nice to meet you">
                    <video loop muted playsinline>
                        <source src="https://media2.giphy.com/media/WXB88TeARFVvi/200w.webm" type="video/webm">
                    </video>
                    <span class="overlay">
                        <div class="desc">
                            <h1 class="masthead-heading">Meet Our Cats!</h1>
                        </div>
                    </span>
                </div>
            </div>
        </div>
        <!-- Mast-head -->

      <section class="bg-blacks">
            <div class="container">
               
                    <div class="center">
                    <h1>CAT GALLERY</h1>
                        <div class="input-group mb-3">
                                <input class="form-control" type="text" id="searchInput" placeholder="Search for cats..." aria-label="Recipient's username" aria-describedby="basic-addon2">
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
                            <div class="card {{ $cat->status == 'Inactive' ? 'inactive' : '' }}" data-name="{{ $cat->cat_name }}" data-age="{{ $cat->age }}" data-color="{{ $cat->color }}" data-breed="{{ $cat->breed }}" data-sex="{{ $cat->sex }}">
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

        <script>
        document.querySelector('#searchInput').addEventListener('input', function() {
            const searchInput = this.value.trim().toLowerCase();
            const searchTerms = searchInput.split(/[\s,]+/); // Split by spaces or commas
            const catCards = document.querySelectorAll('.cat-card');

            if (searchInput !== "") {
                catCards.forEach(function(card) {
                    const catName = card.querySelector('.title').textContent.toLowerCase().trim();
                    const catAge = card.querySelector('.card').getAttribute('data-age').toLowerCase().trim();
                    const catColor = card.querySelector('.card').getAttribute('data-color').toLowerCase().trim();
                    const catBreed = card.querySelector('.card').getAttribute('data-breed').toLowerCase().trim();
                    const catSex = card.querySelector('.card').getAttribute('data-sex').toLowerCase().trim();

                    const cardText = `${catName} ${catAge} ${catColor} ${catBreed} ${catSex}`.replace(/\s+/g, ' ');

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
    </body>
</html>