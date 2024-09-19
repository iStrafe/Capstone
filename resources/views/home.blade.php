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
</head>

<body class="font-sans antialiased">
    <nav class="navbar navbar-expand-lg" data-bs-theme="dark">
        <div class="container-fluid">
            <a class="navbar-brand px-5 py-3" href="{{ url('/') }}">HOME</a>
            <a class="navbar-brand px-4 py-3" href="{{ url('aboutus') }}">ABOUT US</a>
            <a class="navbar-brand px-4 py-3" href="{{ url('services') }}">SERVICES</a>
            <a class="navbar-brand px-4 py-3" href="{{ url('events') }}">NEWS / EVENTS</a>
            <a class="navbar-brand px-4 py-3" href="{{ url('ContactUs') }}">CONTACT US</a>

            <!-- Modal trigger button -->
            <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#modalId">DONATE</button>

                <!-- Modal Body -->
                <!-- Paymongo Modal -->
                <div class="modal fade" id="modalId" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content form-container">
                    <div class="modal-header form">
                        <span class="heading">Donate via PayMongo</span>
                        <button type="reset" class="btn-close" data-bs-dismiss="modal" aria-label="Close">CLOSE</button>
                    </div>
                  <div class="form">
                    <form action="{{ route('paymongo.create') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <input class="input" placeholder="Amount" type="number" name="amount" id="amount" required>
                        </div>
                        <div class="mb-3">
                            <textarea class="input" type="text" name="description" id="description" required></textarea>
                        </div>
                            <div class="button-container">
                                <button type="submit" class="send-button">Send</button>
                                <div class="reset-button-container">
                                    <button type="reset" id="reset-btn" class="reset-button">Reset</button>
                                </div>
                            </div>
                    </form>
                </div>
                    <!--
                    <div class="form">
                       @csrf
                        <form action="{{ route('paymongo.create') }}" method="POST">
                            <div class="mb-3">
                                <input placeholder="Amount" type="number" name="amount" id="amount" min="1" required class="input" required>
                            </div>
                            <div class="mb-3">
                                <textarea class="input" placeholder="Say Hello" rows="4" type="text" name="description" id="description" required></textarea>
                            </div>
                            <div class="button-container">
                                <button type="submit" class="send-button">Send</button>
                                <div class="reset-button-container">
                                    <button type="reset" id="reset-btn" class="reset-button">Reset</button>
                                </div>
                            </div>
                        </form>
                    </div>-->
                    </div>
                </div>
                </div>

        <!-- Bootstrap Modal Initialization Script -->
        <script>
        const myModal = new bootstrap.Modal(document.getElementById('modalId'));
        </script>

                  
            
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                
                </ul>
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
