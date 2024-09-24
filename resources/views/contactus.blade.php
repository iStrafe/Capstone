<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
        <!-- Scripts -->
        @vite(['resources/sass/app.scss', 'resources/js/app.js'])
        <style>
           
        </style>

    </head>
    <body class="..">
       <div class="font-sans antialiased dark:bg-black dark:text-white/50">
            @include('Navigationbar')
  
        </div>

        <div>
          <img src="images\Cat2.jpg" alt="Jane" style="width:100%; height: 500px;">
        </div>

    <div class="about-section border border-warning">
        <div class="centered">
            <h1 class>CONTACT US</h1>
        </div>
    </div>

    <div class="team">
      <h2 style="text-align:center"><b>Our Team</b></h2>
    </div>

    <div class="row">
        <div class="column">
            <div class="card">
              <img src="images/pissbuk.jpg" alt="Jane" style="width:100%;">
                <div class="container">
                  <h2>...</h2>
                    <p class="title">UI/UX</p>
                    <p>...@adamson.edu.ph</p>
                    <p><button class="button">Contact</button></p>
                </div>
          </div>
      </div>

  <div class="column">
    <div class="card">
      <img src="images/pissbuk.jpg" alt="Mike" style="width:100%">
      <div class="container">
        <h2>...</h2>
        <p class="title">Project Manager</p>
        <p>...@example.com</p>
        <p><button class="button">Contact</button></p>
      </div>
    </div>
  </div>

   <div class="column">
    <div class="card">
      <img src="images/pissbuk.jpg" alt="Mike" style="width:100%">
      <div class="container">
        <h2>...</h2>
        <p class="title">Project Manager</p>
        <p>...@example.com</p>
        <p><button class="button">Contact</button></p>
      </div>
    </div>
  </div>

  <div class="column">
    <div class="card">
      <img src="images/pissbuk.jpg" alt="Mike" style="width:100%">
      <div class="container">
        <h2>...</h2>
        <p class="title">...</p>
        <p>...@example.com</p>
        <p><button class="button">Contact</button></p>
      </div>
    </div>
  </div>
</div>

<footer class="bg-dark text-light text-center py-3" style = "bottom:0; width:100%;">

            <div class="logo">

            <a href="https://www.facebook.com/groups/946027585595117/">
                <img src="images\facebook.png" alt="facebook">
            </a>

            <a href="https://www.instagram.com/adu_cats?igsh=MWNqYWd6djFhZzNrZA==">
                <img src="images\ig.png" alt="facebook">
            </a>
            
            </div>
            
            <div class="media">
            <h1>Our Media Platforms</h1>
            </div>
        </footer>

</body>
</html>