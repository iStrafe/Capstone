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
        
        .footer {
            background-color: #03045E;
            color: white;
            padding: 20px 0;
            text-align: center;
            position: relative;
        }
        .footer .fs-1 {
            font-family: serif;
        }
        .footer p, .footer h2 {
            margin: 0;
        }
    </style>


<nav class="navbar navbar-expand-lg border-bottom border-body" data-bs-theme="dark">
    <div class="container-fluid">
             @if(Auth::check() && Auth::user()->role === "admin")
                <a class="button" data-text="Awesome" href="{{url('dashboard')}}">
                    <span class="actual-text">&nbsp;homepage&nbsp;</span>
                    <span aria-hidden="true" class="hover-text">&nbsp;homepage&nbsp;</span>
                </a>
                <!--<a class="navbar-brand px-5 py-3" href="{{url('adminDashboard')}}">adminpage</a>-->
            @endif

           
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
