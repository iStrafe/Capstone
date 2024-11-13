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

    /* Footer */
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

<!-- Modal for session timeout -->
<div class="modal fade" id="timeoutModal" tabindex="-1" aria-labelledby="timeoutModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="timeoutModalLabel">Session Timeout</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{ session('timeoutMessage') }}
            </div>
        </div>
    </div>
</div>

<!-- Idle Detection Script -->
<script>
    let idleTime = 0;

    function timerIncrement() {
        idleTime++;
        if (idleTime > 4) { // 5 minutes
            document.getElementById('logout-form').submit();
        }
    }

    document.onload = resetTimer;
    document.onmousemove = resetTimer;
    document.onkeypress = resetTimer;

    function resetTimer() {
        idleTime = 0;
    }

    setInterval(timerIncrement, 60000); // 1 minute

    if(session('timeoutMessage')) {
        var timeoutModal = new bootstrap.Modal(document.getElementById('timeoutModal'));
        timeoutModal.show();
        setTimeout(function() {
            timeoutModal.hide();
        }, 5000); // Hide after 5 seconds
    }
</script>

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

