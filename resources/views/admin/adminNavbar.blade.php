<head>
    <!-- Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Figtree', sans-serif;
            background-color: #d0e7ff; /* Light blue background */
            background-size: cover;
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
            left: 0;
            background-color: #03045E; /* Adamson blue */
            padding-top: 20px;
            z-index: 1000;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.2);
            transition: width 0.3s ease;
        }

        .sidebar.collapsed {
            width: 0;
        }

        .sidebar a {
            padding: 10px 15px;
            text-decoration: none;
            font-size: 18px;
            color: white;
            display: block;
            margin: 10px 0;
            transition: padding 0.3s ease;
        }

        .sidebar.collapsed a {
            display: none;
        }

        .sidebar a:hover {
            background-color: #0077cc;
        }

        .sidebar .logo {
            max-width: 150px;
            margin: 0 auto 20px;
            display: block;
            text-align: center;
        }

        /* Hide the Donate button when sidebar is collapsed */
        .sidebar.collapsed .donate-btn {
            display: none;
        }

        /* Content area */
        .content {
            margin-left: 250px; /* Adjust the content to make space for the sidebar */
            padding: 20px;
            transition: margin-left 0.3s ease;
        }

        .content.sidebar-collapsed {
            margin-left: 0; /* Adjust content when sidebar is collapsed */
        }

        .footer {
            background-color: #03045E;
            color: white;
            padding: 20px 0;
            text-align: center;
            position: relative;
        }

        .footer p, .footer h2 {
            margin: 0;
        }

        /* Button to toggle sidebar */
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
            transition: left 0.3s ease;
        }

        /* Mobile responsiveness */
        @media (max-width: 768px) {
            .sidebar {
                width: 0;
                padding-top: 50px;
                position: absolute;
                left: -250px;
            }

            .sidebar.collapsed {
                width: 0;
            }

            .sidebar-toggle-btn {
                display: block;
                position: fixed;
                top: 20px;
                left: 20px;
            }

            .content {
                margin-left: 0;
            }

            .content.sidebar-collapsed {
                margin-left: 0;
            }
        }

        /* For large screens, the sidebar is expanded */
        @media (min-width: 769px) {
            .sidebar {
                width: 250px;
                position: fixed;
                left: 0;
            }

            .sidebar.collapsed {
                width: 80px;
            }
        }
    </style>
</head>

<body>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="logo">
        <!-- Place your logo here -->
        <img src="images/adu logo.png" alt="Logo" />
    </div>
    @if(Auth::check() && Auth::user()->role === "admin")
        <a href="{{url('userDashboard')}}">User Page</a>
    @endif

    <a href="{{ url('AdoptionRequest') }}">Adoption Requests</a>
    <a href="{{ url('adminDashboard') }}">Cats</a>

    <a href="{{ Auth::check() && Auth::user()->role === 'admin' ? url('news-events') : url('events') }}">News / Events</a>
    <a href="{{ url('ContactUs') }}">Contact Us</a>
    <a href="{{ url('analyzeImage') }}">Image Analysis</a>

    <!-- Donate button with added class -->
    <button type="button" class="btn btn-primary btn-lg donate-btn" data-bs-toggle="modal" data-bs-target="#modalId">Donate</button>

    <!-- Paymongo Modal -->
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
                            <a class="dropdown-item" href="{{ route('logout') }} "
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
    const content = document.getElementById('content');
    const toggleButton = document.getElementById('toggle-btn');

    toggleButton.addEventListener('click', function() {
        sidebar.classList.toggle('collapsed');
        content.classList.toggle('sidebar-collapsed');
        toggleButton.classList.toggle('sidebar-collapsed');
    });
</script>

</body>
