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

        */

            /* Paymongo moadal*/ 
            .orange {
            color: #ff7a01;
            }

            .form-container {
            max-width: 700px;
            margin: 30px;
            background-color: #001925;
            padding: 30px;
            border-left: 5px solid #00B4D8;
            clip-path: polygon(0 0, 100% 0, 100% calc(100% - 20px), calc(100% - 20px) 100%, 0 100%);
            }

            .heading {
            display: block;
            color: white;
            font-size: 1.5rem;
            font-weight: 800;
            margin-bottom: 20px;
            }

            .form-container .form .input {
            color: #87a4b6;
            width: 100%;
            background-color: #002733;
            border: none;
            outline: none;
            padding: 10px;
            margin-bottom: 20px;
            font-weight: bold;
            transition: all 0.2s ease-in-out;
            border-left: 1px solid transparent;
            }

            .form-container .form .input:focus {
            border-left: 5px solid #00B4D8;
            }

            .form-container .form .textarea {
            width: 100%;
            padding: 10px;
            border: none;
            outline: none;
            background-color: #013747;
            color: #ff7a01;
            font-weight: bold;
            resize: none;
            max-height: 150px;
            margin-bottom: 20px;
            border-left: 1px solid transparent;
            transition: all 0.2s ease-in-out;
            }

            .form-container .form .textarea:focus {
            border-left: 5px solid #ff7a01;
            }

            .form-container .form .button-container {
            display: flex;
            gap: 10px;
            }

            .form-container .form .button-container .send-button {
            flex-basis: 70%;
            background: #06d6a0;
            padding: 10px;
            color: #edf2fb;
            text-align: center;
            font-weight: bold;
            border: 1px solid transparent;
            transition: all 0.2s ease-in-out;
            }

            .form-container .form .button-container .send-button:hover {
            background: transparent;
            border: 1px solid #76c893;
            color: #CAF0F8;
            }

            .form-container .form .button-container .reset-button-container {
            filter: drop-shadow(1px 1px 0px #f72585);
            flex-basis: 30%;
            }

            .form-container .form .button-container .reset-button-container .reset-button {
            position: relative;
            text-align: center;
            padding: 10px;
            color: #f72585;
            font-weight: bold;
            background: #001925;
            clip-path: polygon(0 0, 100% 0, 100% calc(100% - 10px), calc(100% - 10px) 100%, 0 100%);
            transition: all 0.2s ease-in-out;
            }

            .form-container .form .button-container .reset-button-container .reset-button:hover {
            background: #f72585;
            color: #001925;
            }
</style>

<!-- Bootstrap Modal -->
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

        // Show timeout modal if session variable exists
        if(session('timeoutMessage'))
            var timeoutModal = new bootstrap.Modal(document.getElementById('timeoutModal'));
            timeoutModal.show();
            setTimeout(function() {
                timeoutModal.hide();
            }, 5000); // Hide after 5 seconds
        endif

        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js">
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
           
        <a class="navbar-brand px-5 py-3" href="{{url('dashboard')}}">HOME</a>
        <a class="navbar-brand px-4 py-3" href="{{url('aboutus')}}">ABOUT US</a>
        <a class="navbar-brand px-4 py-3" href="{{url('services')}}">SERVICES</a>
        <a class="navbar-brand px-4 py-3" href="{{url('events')}}">NEWS / EVENTS</a>
        <a class="navbar-brand px-4 py-3" href="{{url('ContactUs')}}">CONTACT US</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <!-- Modal trigger button -->
            <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#modalId">DONATE</button>
            <!--Paymongo Modal-->
            @include('payment')

             
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
