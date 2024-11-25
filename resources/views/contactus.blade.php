<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <script src="https://kit.fontawesome.com/c32adfdcda.js" crossorigin="anonymous"></script>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <style>
       * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
}

body {
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    background: #cbc9e8;
}

.wrapper .title {
    text-align: center;
}

.title h4 {
    display: inline-block;
    padding: 15px; /* Reduced padding */
    color: #585757;
    font-size: 24px; /* Smaller font size */
    font-weight: 500;
    letter-spacing: 1px;
    word-spacing: 3px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px; /* Reduced border radius */
    text-transform: uppercase;
    backdrop-filter: blur(10px); /* Slightly less blur */
    box-shadow: 0 8px 8px rgba(0, 0, 0, 0.1);
    word-wrap: break-word;
}

.wrapper .card_Container {
    position: relative;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;
    margin: 30px 0; /* Smaller margin */
}

.card_Container .card {
    position: relative;
    width: 250px; /* Reduced card width */
    height: 330px; /* Reduced card height */
    margin: 15px; /* Reduced margin */
    overflow: hidden;
    box-shadow: 0 20px 20px -15px rgba(0, 0, 0, 1),
                inset 0 0 0 800px rgba(67, 52, 109, 0.6); /* Adjusted shadow */
    border-radius: 12px; /* Reduced border radius */
    display: flex;
    justify-content: center;
    align-items: center;
}

.card .imbBx, .imbBx img {
    width: 100%;
    height: 100%;
}

.card .content {
    position: absolute;
    bottom: -120px; /* Adjusted height */
    width: 100%;
    height: 120px; /* Smaller content box */
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    backdrop-filter: blur(10px); /* Slightly less blur */
    box-shadow: 0 -8px 8px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px; /* Reduced border radius */
    transition: bottom 0.5s;
    transition-delay: 0.65s;
}

.card:hover .content {
    bottom: 0;
    transition-delay: 0s;
}

.content .contentBx h3 {
    text-transform: uppercase;
    color: #fff;
    letter-spacing: 1.5px; /* Reduced spacing */
    font-weight: 500;
    font-size: 14px; /* Smaller font size */
    text-align: center;
    margin: 15px 0 10px; /* Adjusted margins */
    line-height: 1.1em;
    transition: 0.5s;
    transition-delay: 0.6s;
    opacity: 0;
    transform: translateY(-20px);
}

.card:hover .content .contentBx h3 {
    opacity: 1;
    transform: translateY(0);
}

.content .contentBx h3 span {
    font-size: 10px; /* Smaller subtitle font size */
    font-weight: 300;
    text-transform: initial;
}

.content .sci {
    position: relative;
    bottom: 8px; /* Adjusted position */
    display: flex;
}

.content .sci li {
    list-style: none;
    margin: 0 8px; /* Reduced spacing */
    transform: translateY(40px);
    transition: 0.5s;
    opacity: 0;
    transition-delay: calc(0.2s * var(--i));
}

.card:hover .content .sci li {
    transform: translateY(0);
    opacity: 1;
}

.content .sci li a {
    color: #fff;
    font-size: 20px; /* Smaller icon size */
}
        /* Responsive Design for smaller screens */
        @media (max-width: 768px) {
            .row {
                flex-direction: column;
                align-items: center;
            }
        }

        section {
            position: relative;
            z-index: 3;
            padding-top: 10px;
        }

        .container {
            max-width: 1300px; /* Increased container width */
            margin: 0 auto;
            padding: 20px;
        }

        .section-header h2 {
            text-align: center;
            font-size: 2.5em;
            color: #333;
            margin-bottom: 20px;
        }

/* Contact Section Styling */
.contact-section {
    display: flex;
    justify-content: center;
    align-items: flex-start;
    flex-wrap: wrap; /* Ensures responsiveness on smaller screens */
    gap: 20px;
    padding: 20px;
}

.contact-content {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    align-items: flex-start;
    max-width: 1000px; /* Container width */
    width: 100%;
}

.contact-text {
    flex: 1; /* Left side takes up equal space */
    max-width: 400px; /* Limit the paragraph width */
    padding: 20px;
}

.contact-text h2 {
    font-size: 1.8em;
    color: #333;
    margin-bottom: 15px;
}

.contact-text p {
    font-size: 1em;
    color: #555;
    line-height: 1.6;
}

.contact-form {
    flex: 1; /* Form takes up equal space */
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    max-width: 500px;
    width: 100%;
}

.contact-form h2 {
    font-size: 1.6em;
    color: #333;
    margin-bottom: 20px;
    text-align: center;
}

.contact-form .input-box {
    position: relative;
    margin-bottom: 18px;
}

.contact-form .input-box input,
.contact-form .input-box textarea {
    width: 100%;
    padding: 12px;
    font-size: 1em;
    border: 1px solid #ccc;
    border-radius: 4px;
    outline: none;
    transition: border-color 0.3s;
}

.contact-form .input-box span {
    position: absolute;
    top: 50%;
    left: 12px;
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
    padding: 12px;
    background-color: #007bff;
    color: #fff;
    font-size: 1.1em;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.contact-form input[type="submit"]:hover {
    background-color: #0056b3;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .contact-content {
        flex-direction: column;
    }

    .contact-text, .contact-form {
        max-width: 100%;
    }
}
        
    </style>
    @include('Navigationbar')
</head>

<body>
    <div class="wrapper">

        <div class="title">
            <h4>Our Team Section</h4>
        </div>

        <div class="card_Container">

            <div class="card">

                <div class="imbBx">
                    <img src="images\413-4139803_unknown-profile-profile-picture-unknown.png" alt="">
                </div>

                <div class="content">
                    <div class="contentBx">
                        <h3>NAME<br><span>ROLE</span></h3>
                    </div>
                    <ul class="sci">
                        <li style="--i: 1">
                            <a href="#"><i class="fa-brands fa-instagram"></i></a>
                        </li>
                        <li style="--i: 2">
                            <a href="#"><i class="fa-brands fa-github"></i></a>
                        </li>
                        <li style="--i: 3">
                            <a href="#"><i class="fa-brands fa-linkedin-in"></i></a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card">

                <div class="imbBx">
                    <img src="images\413-4139803_unknown-profile-profile-picture-unknown.png" alt="">
                </div>

                <div class="content">
                    <div class="contentBx">
                        <h3>NAME <br><span>ROLE</span></h3>
                    </div>
                    <ul class="sci">
                        <li style="--i: 1">
                            <a href="#"><i class="fa-brands fa-instagram"></i></a>
                        </li>
                        <li style="--i: 2">
                            <a href="#"><i class="fa-brands fa-github"></i></a>
                        </li>
                        <li style="--i: 3">
                            <a href="#"><i class="fa-brands fa-linkedin-in"></i></a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card">

                <div class="imbBx">
                    <img src="images\413-4139803_unknown-profile-profile-picture-unknown.png" alt="">
                </div>

                <div class="content">
                    <div class="contentBx">
                        <h3>NAME <br><span>ROLE</span></h3>
                    </div>
                    <ul class="sci">
                        <li style="--i: 1">
                            <a href="#"><i class="fa-brands fa-instagram"></i></a>
                        </li>
                        <li style="--i: 2">
                            <a href="#"><i class="fa-brands fa-github"></i></a>
                        </li>
                        <li style="--i: 3">
                            <a href="#"><i class="fa-brands fa-linkedin-in"></i></a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card">

                <div class="imbBx">
                    <img src="images\413-4139803_unknown-profile-profile-picture-unknown.png" alt="">
                </div>

                <div class="content">
                    <div class="contentBx">
                        <h3>NAME <br><span>ROLE</span></h3>
                    </div>
                    <ul class="sci">
                        <li style="--i: 1">
                            <a href="#"><i class="fa-brands fa-instagram"></i></a>
                        </li>
                        <li style="--i: 2">
                            <a href="#"><i class="fa-brands fa-github"></i></a>
                        </li>
                        <li style="--i: 3">
                            <a href="#"><i class="fa-brands fa-linkedin-in"></i></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

        <!-- Contact Section -->
        <section class="contact-section">
    <div class="contact-content">
        <div class="contact-text">
            <h2>Contact Us</h2>
            <p>
                Have questions or need assistance? Feel free to reach out to us! 
                We’re here to help with all your inquiries and provide support as 
                quickly as possible. Fill out the form on the right, and we'll 
                get back to you soon.
            </p>
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
    </div>
</section>

    

    
</body>
</html>