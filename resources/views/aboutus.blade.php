@include('Navigationbar')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Space+Mono:ital,wght@0,400;0,700;1,400;1,700&display=swap');
        *{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        .section{
            
            width: 100%;
            min-height: 60vh;
            background-color: #CAF0F8;
        }

        .section2{
            
            width: 100%;
            min-height: 60vh;
            background-color: #00509d;
        }
       
        .containter{
            width: 80%;
            display:block;
            margin:auto;
            padding-top: 100px;
        }

        .containter2{
            width: 80%;
            display:block;
            margin:auto;
            padding-top: 100px;
        }

        .content-section{
            float: Left;
            width: 55%;
        }

        .content-section2{
            float: right;
            width: 55%;
        }

        .image-section{
            float: right;
            width: 40%;
        }

         .image-section2{
            float: left;
            width: 45%;
        }


        .image-section img{
            width: 100%;
            height: auto;
        }

        .image-section2 img{
            width: 70%;
            height: auto;
        }
        .content-section .title{
            text-transform:uppercase;
            font-size: 28px;
        }
        .content-section2 .title{
            text-transform:uppercase;
            color: #ffffff;
            font-size: 28px;
        }
        .content-section .content h3{
            margin-top: 20px;
            color:#5d5d5d;
        }
        .content-section2 .content h3{
            margin-top: 20px;
            color:#caf0f8;
        }
        .content-section .content p{
            margin-top: 10px;
            font-family: sans-serif;
            font-size: 17px;
            line-height: 1.5;
        }
        .content-section2 .content p{
            margin-top: 10px;
            font-family: sans-serif;
            color: #ffffff;
            font-size: 17px;
            line-height: 1.5;
        }
        .content-section .content .button{
            margin-top: 20px;
        }
        .content-section2 .content .button{
            margin-top: 20px;
        }
        .content-section .content .button a{
            background-color: #3d3d3d;
            padding: 1px 30px;
            text-decoration: none;
            color: #fff;
            font-size: 25px;
            letter-spacing: 1.5px;
        }
        .content-section2 .content .button a{
            background-color: #3d3d3d;
            padding: 1px 30px;
            text-decoration: none;
            color: #fff;
            font-size: 25px;
            letter-spacing: 1.5px;
        }
    
        .content-section .content .button a:hover{
            background-color: #0077B6 !important;
            color: #fff;
        }
        .content-section2 .content .button a:hover{
            background-color: #006400 !important;
            color: #fff;
        }

        .content-section .social{
            margin-top: 20px;
        }
         .content-section2 .social{
            margin-top: 20px;
        }
        .content-section .social i{
            color: #3d3d3d;
            font-size: 30px;
            padding: 0px 10px;
        }
        .content-section2 .social i{
            color: #3d3d3d;
            font-size: 30px;
            padding: 0px 10px;
        }

            @media screen and (max-width: 768px){
            .containter{
                width: 80%;
                display:block;
                margin:auto;
                padding-top: 50px;
            }

            .content-section{
                float: none;
                width: 100%;
                display: block;
                margin:auto;
            }

             .content-section2{
                float: none;
                width: 100%;
                display: block;
                margin:auto;
            }

             .image-section{
                float: none;
                width: 100%;
            }

            .image-section2{
                float: none;
                width: 100%;
            }

             .image-section img{
                float: none;
                width: 100%;
                display: block;
                margin:auto;
            }

             .content-section .title{
                text-align: center;
                font-size: 19px;
            }

            .content-section .content .button{
                text-align: center;
            }

            .content-section2 .content .button{
                text-align: center;
            }

            .content-section .content .button a{
                padding: 9px 30px;
            }

            .content-section2 .content .button a{
                padding: 9px 30px;
            }
            .content-section .social{
                text-align: center;
            }

            .content-section2 .social{
                text-align: center;
            }

        }

    </style>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css">
    
    <title>Document</title>
</head>
<body>
    <body class="">

    <div class="section py-5">
        <div class="container">
            <div class="content-section">
                <div class="title">
                    <h1>ABOUT US</h1>
                </div>
                <div class="content">
                    <h3>AdUCats advocate peaceful co-existence between the humans and furry friends...</h3>
                    <p>Cats play a major role in improving both students’ health and their psychological well-being through decreasing anxiety. According to Dabbos, “Animals reduce anxiety and stress because they release a chemical that actually works on reducing the stress.”</p>
                    <p>Cats play a major role in improving students’ psychology through decreasing depression, anxiety and creating a positive mood.</p>
                    <p>On students’ health level, cats play a major role in healing and preventing various illnesses.</p>
                    <p>Being around cats, helps in boosting one’s immune system through improving immune functions. Thus, allowing the prevention or the disposal of various sicknesses.</p>
                    <div class="button">
                        <a href="https://www.facebook.com/groups/946027585595117" style="background-color: #00B4D8;">Facebook</a>
                        
                    </div>
                </div>
            </div>
            <div class="image-section py-5">
                <img src="images/aducat1.jpg" alt="">
            </div>
        </div>
    </div>

     <div class="section2 py-5 px-5">
        <div class="container2">
            <div class="content-section2">
                <div class="title">
                    <h1>disclaimer</h1>
                </div>
                <div class="content">
                    <p>Aducats is not a shelter. We are a non-profit organization run by dedicated volunteers from Adamson University, working to connect cats in need with loving adopters. While we do not house cats directly, we ensure that every cat we assist is cared for until they find their forever home.</p>
                    <h3>Leaving your pets or cats here is strongly discouraged.</h3>
                    <p>
                        The university is not an animal
                        shelter. RA 10631, Section 7
                        of the Animal Welfare Act
                        states that, "it shall be unlawful
                        for any person who has custody
                        of an animal to abandon the said
                        animal."
                        We will only be responsible
                        for all accounted cats in the
                        campus and will facilitate their
                        adoption later on.</p>
                        <p>Thank you for supporting our mission to promote responsible pet adoption and animal welfare!</p>
                    <div class="button">
                        <a href="https://www.officialgazette.gov.ph/2013/10/03/republic-act-no-10631/" style="background-color: lightgreen;">RA 10631 SECTION-7</a>
                    </div>
                </div>
            </div>
            <div class="image-section2 py-5">
                <img src="images/Disclaimer.jpg" alt="">
            </div>
        </div>
    </div>

    <div class="footer fw-light">
        <p class="fs-1" style="font-family: serif;">ABOUT US</p>
        <h2>COME VISIT US</h2>
        <p>900 San Marcelino St. Ermita, Manila, 1000 Metro Manila</p>
    </div>
       
</body>

    
</html>
