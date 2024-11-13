<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <script src="https://kit.fontawesome.com/c32adfdcda.js" crossorigin="anonymous"></script>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <style>
        section {
            position: relative;
            z-index: 3;
            padding-top: 10px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .section-header h2 {
            text-align: center;
            font-size: 2em;
            color: #333;
            margin-bottom: 20px;
        }

        /* From Uiverse.io by Javierrocadev */ 
        .card {
          width: 350px;
          height: 300px;
          display: flex;
          flex-direction: column;
          align-items: center;
          justify-content: center;
          text-align: center;
          gap: 10px;
          background-color: #fffffe;
          border-radius: 15px;
          position: relative;
          overflow: hidden;
          transition: all 0.5s ease;
        }

        .card::before {
          content: "";
          width: 350px;
          height: 100px;
          position: absolute;
          top: 0;
          border-top-left-radius: 15px;
          border-top-right-radius: 15px;
          border-bottom: 3px solid #fefefe;
          background: linear-gradient(40deg, rgba(131,58,180,1) 0%, rgba(253,29,29,1) 50%, rgba(252,176,69,1) 100%);
          transition: all 0.3s ease;
        }

        .card * {
          z-index: 1;
        }

        .image {
          width: 90px;
          height: 90px;
          background-color: #1468BF;
          border-radius: 50%;
          border: 4px solid #fefefe;
          margin-top: 30px;
          transition: all 0.5s ease;
        }

        .card-info {
          display: flex;
          flex-direction: column;
          align-items: center;
          gap: 15px;
          transition: all 0.5s ease;
        }

        .card-info span {
          font-weight: 600;
          font-size: 24px;
          color: #161A42;
          margin-top: 15px;
          line-height: 5px;
        }

        .card-info p {
          color: rgba(0, 0, 0, 0.5);
        }

        .button {
          text-decoration: none;
          background-color: #1468BF;
          color: white;
          padding: 5px 20px;
          border-radius: 5px;
          border: 1px solid white;
          transition: all 0.5s ease;
        }

        .card:hover {
          width: 300px;
          border-radius: 250px;
        }

        .card:hover::before {
          width: 300px;
          height: 240px;
          border-radius: 50%;
          border-bottom: none;
          transform: scale(0.95);
        }

        .card:hover .card-info {
          transform: translate(0%,-20%);
        }

        .button:hover {
          background-color: #FF6844;
          transform: scale(1.1);
        }


        .site-footer
{
  background-color:#26272b;
  padding:45px 0 20px;
  font-size:15px;
  line-height:24px;
  color:#737373;
}
.site-footer hr
{
  border-top-color:#bbb;
  opacity:0.5
}
.site-footer hr.small
{
  margin:20px 0
}
.site-footer h6
{
  color:#fff;
  font-size:16px;
  text-transform:uppercase;
  margin-top:5px;
  letter-spacing:2px
}
.site-footer a
{
  color:#737373;
}
.site-footer a:hover
{
  color:#3366cc;
  text-decoration:none;
}
.footer-links
{
  padding-left:0;
  list-style:none
}
.footer-links li
{
  display:block
}
.footer-links a
{
  color:#737373
}
.footer-links a:active,.footer-links a:focus,.footer-links a:hover
{
  color:#3366cc;
  text-decoration:none;
}
.footer-links.inline li
{
  display:inline-block
}
.site-footer .social-icons
{
  text-align:right
}
.site-footer .social-icons a
{
  width:40px;
  height:40px;
  line-height:40px;
  margin-left:6px;
  margin-right:0;
  border-radius:100%;
  background-color:#33353d
}
.copyright-text
{
  margin:0
}
@media (max-width:991px)
{
  .site-footer [class^=col-]
  {
    margin-bottom:30px
  }
}
@media (max-width:767px)
{
  .site-footer
  {
    padding-bottom:0
  }
  .site-footer .copyright-text,.site-footer .social-icons
  {
    text-align:center
  }
}
.social-icons
{
  padding-left:0;
  margin-bottom:0;
  list-style:none
}
.social-icons li
{
  display:inline-block;
  margin-bottom:4px
}
.social-icons li.title
{
  margin-right:15px;
  text-transform:uppercase;
  color:#96a2b2;
  font-weight:700;
  font-size:13px
}
.social-icons a{
  background-color:#eceeef;
  color:#818a91;
  font-size:16px;
  display:inline-block;
  line-height:44px;
  width:44px;
  height:44px;
  text-align:center;
  margin-right:8px;
  border-radius:100%;
  -webkit-transition:all .2s linear;
  -o-transition:all .2s linear;
  transition:all .2s linear
}
.social-icons a:active,.social-icons a:focus,.social-icons a:hover
{
  color:#fff;
  background-color:#29aafe
}
.social-icons.size-sm a
{
  line-height:34px;
  height:34px;
  width:34px;
  font-size:14px
}
.social-icons a.facebook:hover
{
  background-color:#3b5998
}
.social-icons a.twitter:hover
{
  background-color:#00aced
}
.social-icons a.linkedin:hover
{
  background-color:#007bb6
}
.social-icons a.dribbble:hover
{
  background-color:#ea4c89
}
@media (max-width:767px)
{
  .social-icons li.title
  {
    display:block;
    margin-right:0;
    font-weight:600
  }
}
        /* Contact Form Styling */
        .contact-form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        .contact-form h2 {
            font-size: 1.5em;
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }

        .contact-form .input-box {
            position: relative;
            margin-bottom: 20px;
        }

        .contact-form .input-box input,
        .contact-form .input-box textarea {
            width: 100%;
            padding: 10px;
            font-size: 1em;
            border: 1px solid #ccc;
            border-radius: 4px;
            outline: none;
            transition: border-color 0.3s;
        }

        .contact-form .input-box span {
            position: absolute;
            top: 50%;
            left: 10px;
            font-size: 0.9em;
            color: #666;
            transform: translateY(-50%);
            pointer-events: none;
            transition: 0.3s;
        }

        .contact-form .input-box input:focus ~ span,
        .contact-form .input-box textarea:focus ~ span,
        .contact-form .input-box input:valid ~ span,
        .contact-form .input-box textarea:valid ~ span {
            top: 5px;
            font-size: 0.8em;
            color: #007bff;
        }

        .contact-form input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: #fff;
            font-size: 1em;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .contact-form input[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
    @include('Navigationbar')
</head>

<body class="font-sans antialiased dark:bg-black dark:text-white/50">
    <div class="container" style="margin-top: 20px; margin-bottom: 20px;">
        <div class="section-header">
            <h2 style="color: white; font-size: 4em;">Our Team</h2>
            <div class="row">
                <!-- Team Member Cards -->
                <div class="card">
                    <div class="image"></div>
                    <div class="card-info">
                        <span>Name</span>
                        <p>Role Here</p>
                    </div>
                    <a class="button" href="#">Follow</a>
                </div>

                <div class="card">
                    <div class="image"></div>
                    <div class="card-info">
                        <span>Name</span>
                        <p>Role Here</p>
                    </div>
                    <a class="button" href="#">Follow</a>
                </div>

                <div class="card">
                    <div class="image"></div>
                    <div class="card-info">
                        <span>Name</span>
                        <p>Role Here</p>
                    </div>
                    <a class="button" href="#">Follow</a>
                </div>
                <!-- Repeat other team member cards here -->
            </div>
        </div>

        <!-- Contact Section -->
        <section>
            <div class="section-header">
                <h2>Contact Us</h2>
            </div>
            <div class="contact-form">
                <form action="{{ route('contact.store') }}" method="POST" id="contact-form">
                    @csrf
                    <h2>Send Message</h2>

                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <div class="input-box">
                        <input type="text" name="full_name" required>
                        <span>Full Name</span>
                    </div>

                    <div class="input-box">
                        <input type="text" name="mobile_number" required>
                        <span>Mobile Number</span>
                    </div>

                    <div class="input-box">
                        <textarea name="message" required></textarea>
                        <span>Type your Message...</span>
                    </div>

                    <div class="input-box">
                        <input type="submit" value="Send">
                    </div>
                </form>
            </div>
        </section>
    </div>
</body>
</html>
