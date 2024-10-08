<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
        <!-- Scripts -->
        <script src="https://kit.fontawesome.com/c32adfdcda.js" crossorigin="anonymous"></script>
        @vite(['resources/sass/app.scss', 'resources/js/app.js'])
        <style>
           section {
  position: relative;
  z-index: 3;
  padding-top: 10px;
  padding-bottom: 0px;
}

.container {
  max-width: 100px;
  margin-left: auto;
  margin-right: auto;
  padding-left: 20px;
  padding-right: 20px;
}

.section-header {
  margin-bottom: 20px;
  text-align: center;
}

.section-header h2 {
  color: black;
  font-weight: bold;
  font-size: 3em;
  margin-bottom: 20px;
}

.row  {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
}

.contact-form {
  background-color: #fff;
  padding: 40px;
  width: 45%;
  padding-bottom: 20px;
  padding-top: 20px;
}

.contact-form h2 {
  font-weight: bold;
  font-size: 2em;
  margin-bottom: 10px;
  color: #333;
}

.contact-form .input-box {
  position: relative;
  width: 100%;
  margin-top: 10px;
}

.contact-form .input-box input,
.contact-form .input-box textarea{
  width: 100%;
  padding: 2px 0;
  font-size: 16px;
  margin: 10px 0;
  border: none;
  border-bottom: 2px solid #333;
  outline: none;
  resize: none;
}

.contact-form .input-box span {
  position: absolute;
  left: 0;
  padding: 5px 0;
  font-size: 16px;
  margin: 5px 0;
  pointer-events: none;
  transition: 0.5s;
  color: #666;
}

.contact-form .input-box input:focus ~ span,
.contact-form .input-box textarea:focus ~ span{
  color: #e91e63;
  font-size: 12px;
  transform: translateY(-20px);
}

.contact-form .input-box input[type="submit"]
{
  width: 100%;
  background: #00bcd4;
  color: #FFF;
  border: none;
  cursor: pointer;
  padding: 10px;
  font-size: 18px;
  border: 1px solid #00bcd4;
  transition: 0.5s;
  margin-top: 0;
}

.contact-form .input-box input[type="submit"]:hover
{
  background: #FFF;
  color: #00bcd4;
}

.row {
    flex-direction: column;
  }
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

<section>
    
    <div class="section-header">
        <h2>Contact Us</h2>
    </div>
    
    <div class="container">
      <div class="row">
        
        <div class="contact-form">
          <form action="{{ route('contact.store') }}" method="POST" id="contact-form">
            @csrf
            <h2>Send Message</h2>

            @if(session('success'))
             <div class="alert alert-success">
                      {{ session ('success')}}
             </div> 
            @endif

            <div class="input-box">
              <input type="text" required="true" name="full_name">
              <span>Full Name</span>
            </div>
            
            <div class="input-box">
              <input type="type" required="true" name="mobile_number">
              <span>Mobile Number</span>
            </div>
            
            <div class="input-box">
              <textarea required="true" name="message"></textarea>
              <span>Type your Message...</span>
            </div>
            
            <div class="input-box">
              <input type="submit" value="Send">
            </div>
          </form>
        </div>
        
      </div>
    </div>
  </section>

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