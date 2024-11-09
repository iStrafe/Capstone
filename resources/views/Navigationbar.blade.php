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
        font-family: 'Figtree', sans-serif; /* Consistent font */
    }

    .navbar a, .navbar .dropdown-toggle {
        color: white;
        text-decoration: none;
        font-weight: 600; /* Consistent font weight */
        padding: 0.75rem 1.5rem; /* Adjust padding for space between items */
        font-size: 1.1rem; /* Set consistent font size */
    }

    .navbar a:hover, .navbar .dropdown-item:hover {
        color: #00B4D8; /* Light blue on hover for links */
    }

    .navbar .dropdown-menu {
        background-color: #003366;
    }

    .navbar .container-fluid {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .navbar-nav {
        display: flex;
        justify-content: flex-start;
        align-items: center;
        margin: 0;
        padding: 0;
    }

    .navbar-nav .nav-item {
        margin: 0 20px; /* Increased spacing between items */
    }

    .navbar-nav .nav-link {
        padding: 0.75rem 1.5rem; /* Adjust padding to fit in one line */
        font-size: 1.1rem; /* Consistent font size */
        display: block;
    }

    .navbar-nav .btn {
        margin-left: 20px; /* Adjust space for the donate button */
        font-size: 1.1rem; /* Consistent font size */
        padding: 0.75rem 1.5rem; /* Adjust button padding */
        background-color: transparent; /* Remove background */
        color: white; /* Keep text white */
        border: none; /* Remove border */
        cursor: pointer; /* Make it clickable */
    }

    .navbar-nav .btn:hover {
        color: #00B4D8; /* Light blue on hover */
    }

    .navbar-toggler {
        display: none; /* Hide the toggle button on larger screens */
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
            align-items: center;
        }

        .navbar .container-fluid {
            justify-content: center; /* Center the navbar content */
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
                <span aria-hidden="true" class="hover-text">&nbsp;&nbsp;</span>
            </a>
        @endif
        <a class="navbar-brand" href="{{ url('/') }}">HOME</a>
        <div class="navbar-nav">
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
</nav>
