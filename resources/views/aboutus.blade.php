<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        .text1 {
            margin: auto;
            width: 70%;
            border: 3px solid transparent;
            padding: 10px;
            font-size: 20px;
            font-family: Verdana, sans-serif;
            text-align: center;
            box-shadow: 0 15px 10px 0 rgba(0, 0, 0, 0.2);
            background-color: #f9f9f9;
            margin-bottom: 20px;
        }

        .text1 p {
            color: #662200;
        }

        .container {
            width: 100%;
            background: lightgray;
            padding: 20px;
            box-shadow: 0 15px 12px 0 rgba(0, 0, 0, 0.2);
        }

        .about-section img {
            width: 100%;
            height: auto;
        }

        .footer {
            background-color: #002366;
            color: #ffffff;
            text-align: center;
            padding: 20px;
            font-size: 18px;
        }

        .footer h2 {
            margin: 10px 0;
            font-size: 24px;
        }

        .footer p {
            font-size: 16px;
        }

        .text-center {
            text-align: center;
        }

        /* Responsive Styling */
        @media (max-width: 768px) {
            .text1 {
                width: 90%;
                font-size: 16px;
            }

            .text1 h2 {
                font-size: 1.2em;
            }

            .container img {
                height: auto;
            }

            .footer p, .footer h2 {
                font-size: 16px;
                text-align: center;
            }

            .text-center p {
                font-size: 1.5em;
            }
        }

        .gallery-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            padding: 20px;
            gap: 10px;
        }

        .gallery-item {
            flex: 1 1 calc(33.333% - 20px);
            max-width: calc(33.333% - 20px);
            position: relative;
        }

        .gallery-item img {
            width: 100%;
            height: auto;
            border-radius: 8px;
        }

        /* Responsive styling */
        @media (max-width: 768px) {
            .gallery-item {
                flex: 1 1 calc(50% - 20px);
                max-width: calc(50% - 20px);
            }
        }

        @media (max-width: 576px) {
            .gallery-item {
                flex: 1 1 100%;
                max-width: 100%;
            }
        }

        .gallery-item .caption {
            position: absolute;
            bottom: 8px;
            left: 8px;
            background-color: rgba(0, 0, 0, 0.6);
            color: #ffffff;
            padding: 5px;
            border-radius: 4px;
            font-size: 14px;
        }

        @media (max-width: 576px) {
            .text1 {
                width: 100%;
                font-size: 14px;
                padding: 10px;
            }

            .container {
                padding: 10px;
            }

            .footer p, .footer h2 {
                font-size: 14px;
                text-align: center;
            }

            .text-center p {
                font-size: 1.2em;
            }
        }
    </style>
</head>

<body class="..">
    <div class="font-sans antialiased dark:bg-black dark:text-white/50">
        @include('Navigationbar')
    </div>

    <div class="about-section">
        <img src="images/Cat2.jpg" alt="Jane">
    </div>

    <br>

    <div class="text-center">
        <p class="fs-6 fw-light" style="font-family: Verdana, sans-serif;">
            AdUCats advocate peaceful co-existence between the humans and furry friends
        </p>
    </div>

    <br><br>

    <div class="text1">
        <h2><p>Being around cats helps in boosting one’s immune system through improving immune functions, allowing the prevention or disposal of various sicknesses.</p></h2>
    </div>

    <div class="text1">
        <h2><p>Cats play a major role in improving students’ psychology by decreasing depression and anxiety and creating a positive mood.</p></h2>
    </div>

    <div class="text1">
        <h2><p>On students’ health level, cats play a major role in healing and preventing various illnesses.</p></h2>
    </div>

    <div class="text1">
        <h2><p>Being around cats helps in boosting one’s immune system through improving immune functions, allowing the prevention or disposal of various sicknesses.</p></h2>
    </div>

    <br><br>

    <div class="text-center">
        <p class="fs-2">Meet Our Cats</p>
    </div>

        <div class="gallery-container">
        <div class="gallery-item">
            <img src="images/aducat1.jpg" alt="Event Image 1">
            <div class="caption">Adamson University</div>
        </div>
        <div class="gallery-item">
            <img src="images/aducat2.jpg" alt="Event Image 1">
            <div class="caption">Adamson University</div>
        </div>
        <div class="gallery-item">
            <img src="images/aducat3.jpg" alt="Event Image 2">
            <div class="caption">Adamson University</div>
        </div>
        <div class="gallery-item">
            <img src="images/aducat4.jpg" alt="Event Image 3">
            <div class="caption">Adamson University</div>
        </div>
        <div class="gallery-item">
            <img src="images/aducat1.jpg" alt="Event Image 3">
            <div class="caption">Adamson University</div>
        </div>
    </div>
    </div>

    <br><br>

    <div class="footer fw-light">
        <p class="fs-1" style="font-family: serif;">ABOUT US</p>
        <h2>COME VISIT US</h2>
        <p>900 San Marcelino St. Ermita, Manila, 1000 Metro Manila</p>
    </div>

</body>
</html>
