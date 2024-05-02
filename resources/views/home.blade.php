<html>
    <head>
    
     <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
         <!-- Scripts -->
        @vite(['resources/sass/app.scss', 'resources/js/app.js'])
        
        

        <!-- Styles -->
        @include('scripts')
      

    </head>

    <body class="font-sans antialiased dark:bg-black dark:text-white/50">
    
         <nav class="navbar navbar-expand-lg border-bottom border-body border border-warning" data-bs-theme="dark">
            <div class="container-fluid">
                <a class="navbar-brand px-5 py-3" href="{{url('/')}}">HOME</a>
                <a class="navbar-brand px-4 py-3" href="{{url('aboutus')}}">ABOUT US</a>
                <a class="navbar-brand px-4 py-3" href="{{url('services')}}">SERVICES</a>
                <a class="navbar-brand px-4 py-3" href="{{url('events')}}">NEWS / EVENTS</a>
                <a class="navbar-brand px-4 py-3" href="ContactUs">CONTACT US</a>
                <a class="navbar-brand px-4 py-3" href="#">FEED</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">

                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <!--In between navbar items and dropdown menu-->
                </ul>

                <form class="d-flex px-5" role="search">
                     @if (Route::has('login'))
                            <nav class="-mx-3 flex flex-1 justify-end ">
                                @auth
                                   <!--Dropdown menu-->
                                    <div class="dropdown">
                                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                             <div>{{ Auth::user()->name }}</div>
                                             
                                        </button>
                                        
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                <x-dropdown-link :href="route('profile.edit')" class="dropdown-item">
                                                {{ __('Profile') }}
                                                </x-dropdown-link>

                                            <form method="POST" action="{{ route('logout') }}">
                                                @csrf
                                                <x-dropdown-link :href="route('logout')" class="dropdown-item"
                                                        onclick="event.preventDefault();
                                                                    this.closest('form').submit();">
                                                    {{ __('Log Out') }}
                                                </x-dropdown-link>
                                            </form>
                                        </div>
                                    </div>  <!--Dropdown menu end-->
                                @else
                                    <a
                                        href="{{ route('login') }}"
                                        class="rounded-md px-5 py-3 text-white ring-1 ring-transparent transition hover:text-White/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/100 dark:focus-visible:ring-white"
                                    >
                                        Log in
                                    </a>

                                    @if (Route::has('register'))
                                        <a
                                            href="{{ route('register') }}"
                                            class="rounded-md px-5 py-3 text-white ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                                        >
                                            Register
                                        </a>
                                    @endif
                                @endauth
                                <div class="dropdown show">
                            </nav>
                        @endif
                    </ul>
                </form>
                </div>
            </div>
        </nav>


        <div class="border border-warning">
            <img src="images\Cat2.jpg"  style="width:100%; height: 500px;">
        </div>

        <section class="bg-blacks">
            <div class="container">
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


