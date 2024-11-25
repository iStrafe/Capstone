<head>
    <!-- Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Figtree', sans-serif;
            background-color: #d0e7ff;
            color: #333;
            margin: 0;
            padding: 0;
        }

        /* Sidebar styles */
        .sidebar {
            height: 100%;
            width: 250px;
            position: fixed;
            top: 0;
            left: -250px; /* Hide sidebar off-screen by default */
            background-color: #03045E;
            padding-top: 20px;
            z-index: 1000;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.2);
            transition: left 0.3s ease;
        }

        .sidebar.active {
            left: 0; /* Slide sidebar in when active */
        }

        .sidebar a {
            padding: 10px 15px;
            text-decoration: none;
            font-size: 18px;
            color: white;
            display: block;
            margin: 10px 0;
        }

        .sidebar a:hover {
            background-color: #0077cc;
        }

        .sidebar .logo {
            max-width: 100px;
            margin: 0 auto 20px;
            display: block;
            text-align: center;
        }

        /* Content area */
        .content {
            padding: 20px;
            margin-left: 0;
            transition: margin-left 0.3s ease;
        }

        /* Sidebar toggle button */
        .sidebar-toggle-btn {
            background-color: #03045E;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 2000;
        }

        /* Set text color to black for the navbar links and dropdown */
        #content .navbar-nav .nav-link,
        #content .navbar-nav .dropdown-toggle,
        #content .dropdown-menu .dropdown-item {
            color: #000 !important; /* Force black text */
        }

        /* Optional: Customize background and hover effects if needed */
        #content .dropdown-menu {
            background-color: #ffffff; /* White background */
        }

        #content .dropdown-menu .dropdown-item:hover {
            background-color: #f0f0f0; /* Light gray on hover */
        }

        



        /* Responsive adjustments */
        @media (min-width: 769px) {
            .content {
                margin-left: 0; /* Content does not shift when sidebar is hidden */
            }
        }
    </style>
</head>

<body>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="logo">
        <img src="images/adu logo.png" alt="Logo" />
    </div>
    @if(Auth::check() && Auth::user()->role === "admin")
        <a href="{{ url('userDashboard') }}">User Page</a>
    @endif

    <a href="{{ url('AdoptionRequest') }}">Adoption Requests</a>
    <a href="{{ url('RejectedRequest') }}">Rejected Requests</a>
    <a href="{{ url('ReleasedRequest') }}">Released Requests</a>
    <a href="{{ url('adminDashboard') }}">Cats</a>
    <a href="{{ Auth::check() && Auth::user()->role === 'admin' ? url('news-events') : url('events') }}">News / Events</a>
    <a href="{{ url('ContactUs') }}">Contact Us</a>
    <a href="{{ url('analyzeImage') }}">Image Analysis</a>

    <button type="button" class="btn btn-primary btn-lg donate-btn" data-bs-toggle="modal" data-bs-target="#modalId">Donate</button>
    @include('payment')
</div>

<!-- Toggle button -->
<button class="sidebar-toggle-btn" id="toggle-btn">
    &#9776; <!-- Hamburger icon -->
</button>

<!-- Main content -->
<div class="content" id="content">
    <nav class="navbar navbar-expand-lg border-bottom border-body" data-bs-theme="dark">
        <div class="container-fluid">
            
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
</div>

<script>
    const sidebar = document.getElementById('sidebar');
    const toggleButton = document.getElementById('toggle-btn');

    toggleButton.addEventListener('click', function() {
        sidebar.classList.toggle('active'); // Toggle sidebar visibility on all screen sizes
    });
</script>

</body>
