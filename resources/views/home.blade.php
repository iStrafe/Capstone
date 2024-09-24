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
        .container {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 10px;
            margin: 20px auto;
            width: 30%;
            max-width: 1200px;
            text-align: center; /* Center-align text inside the container */
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

        /*
        */
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

        <a class="navbar-brand px-5" href="{{ url('/') }}">HOME</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
           
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item px-4">
                    <a class="nav-link" href="{{ url('aboutus') }}">ABOUT US</a>
                </li>
                <li class="nav-item px-4">
                    <a class="nav-link" href="{{ url('services') }}">SERVICES</a>
                </li>
                <li class="nav-item px-4">
                    <a class="nav-link" href="{{ url('events') }}">NEWS / EVENTS</a>
                </li>
                <li class="nav-item px-4">
                    <a class="nav-link" href="{{ url('ContactUs') }}">CONTACT US</a>
                </li>

                <!-- Modal trigger button -->
                <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#modalId">DONATE</button>
                <!--Paymongo Modal-->
                @include('payment')
            </ul>

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

    <br><br><br><br><br>
    <section>
        <div class="container">
            <img src="images/logo.png" alt="Logo" class="logo">
            <div class="center">
                <h1>SEARCH CAT</h1>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Search Cat" aria-label="Recipient's username" aria-describedby="basic-addon2">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button">Advanced Search</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>
</html>
