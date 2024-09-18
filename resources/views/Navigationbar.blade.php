
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
                <a class="button" data-text="Awesome" href="{{url('adminDashboard')}}">
                    <span class="actual-text">&nbsp;adminpage&nbsp;</span>
                    <span aria-hidden="true" class="hover-text">&nbsp;adminpage&nbsp;</span>
                </a>
                <!--<a class="navbar-brand px-5 py-3" href="{{url('adminDashboard')}}">adminpage</a>-->
            @endif
           
        <a class="navbar-brand px-5 py-3" href="{{url('dashboard')}}">HOME</a>
        <a class="navbar-brand px-4 py-3" href="{{url('aboutus')}}">ABOUT US</a>
        <a class="navbar-brand px-4 py-3" href="{{url('services')}}">SERVICES</a>
        <a class="navbar-brand px-4 py-3" href="{{url('events')}}">NEWS / EVENTS</a>
        <a class="navbar-brand px-4 py-3" href="{{url('ContactUs')}}">CONTACT US</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <!--In between navbar items and dropdown menu-->
            </ul>
            
            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ms-auto">
                <!-- Authentication Links -->
                @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                    </li>
                    @if (Route::has('register'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                        </li>
                    @endif
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
