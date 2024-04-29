<nav class="navbar navbar-expand-lg border-bottom border-body" data-bs-theme="dark">
            <div class="container-fluid">
                <a class="navbar-brand px-5 py-3" href="{{url('home')}}">HOME</a>
                <a class="navbar-brand px-4 py-3" href="{{url('aboutus')}}">ABOUT US</a>
                <a class="navbar-brand px-4 py-3" href="{{url('services')}}">SERVICES</a>
                <a class="navbar-brand px-4 py-3" href="{{url('events')}}">NEWS / EVENTS</a>
                <a class="navbar-brand px-4 py-3" href="{{url('ContactUs')}}">CONTACT US</a>
                <a class="navbar-brand px-4 py-3" href="#">FEED</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">

                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <!--In between navbar items and dropdown menu-->
                </ul>

                
                                <!--Dropdown menu-->
                    <div class="dropdown px-5">
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

                </div>
            </div>
        </nav>