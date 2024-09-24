<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    @include('scripts')
    <style>
        *{
            
        }
        .text1{
            margin: auto;
            width: 70%;
            border: 3px ;
            padding: 10px;
            font-size: 20px;
            font-family: verdana;
            text-align: center;
            box-shadow: 0 15px 10px 0 rgba(0, 0, 0, 0.2);
        }

        .text1 p{
            color:#662200;
        }

        .container{
            width: 100%;
            height: 110vh;
            background: lightgray;
            box-shadow: 0 15px 12px 0 rgba(0, 0, 0, 0.2);
        }

        .footer {
        padding: 20px;
        text-align: left;
        background: #ddd;
        }

    </style>
    <body class="..">
        <div class="font-sans antialiased dark:bg-black dark:text-white/50">
            @include('Navigationbar')
        </div>

        <div class="about-section">
            <img src="images\Cat2.jpg" alt="Jane" style="width:100%; height: 500px;">
        </div>

<br>

    <div class="text-center">
            <p class="fs-6 fw-light" style="font-family:verdana;">AdUCats advocate peaceful co-existence between the humans and furry friends</p> 
    </div>
    

    <br><br>
    
    <div class="text1 y-5">
        <div>
            <h2>
                <p>Being around cats, helps in boosting one’s immune system through improving immune functions. 
                    Thus, allowing the prevention or the disposal of various sicknesses.
                </p>
            </h2>                    
        </div>
    </div> 

    <br>
    
    <div class="text1 y-5">
        <div>
            <h2>
                <p>Cats play a major role in improving students’ 
                    psychology through decreasing depression, anxiety and creating a positive mood.    
                </p>
            </h2>                    
        </div>
    </div>

    <br>

    <div class="text1 y-5">
        <div>
            <h2>
                <p>On students’ health level, cats play a major role in healing 
                    and preventing various illnesses.     
                </p>
            </h2>                    
        </div>
    </div>
    
    <br>

    <div class="text1 y-5">
        <div>
            <h2>
                <p>Being around cats, helps in boosting one’s immune system through improving immune functions. 
                    Thus, allowing the prevention or the disposal of various sicknesses.  
                </p>
            </h2>                    
        </div>
    </div>
          
<br><br>

        <div class="text-center">
            <p class="fs-2">Meet Our Cats</p>
        </div>

<br><br>

        <div class="container">
            <div class="row">
                <img src="images\aducat2.jpg" alt="Jane" style="width:100%; height: 500px;">
            </div>

            <br>
            
            <div class="row">
                <div class="g-col-6">
                    <img src="images\aducat1.jpg" alt="Jane">
                </div>

                <div class="g-col-6">
                    <img src="images\aducat3.jpg" alt="Jane">
                </div>

                <div class="g-col-6">
                    <img src="images\aducat4.jpg" alt="Jane">
                </div>
            </div>
        </div>

<br><br>

        <div class="footer fw-light">
            <b><p class="fs-1" style="font-family:serif;">ABOUT US</p><b>
                <h2>COME VISIT US</h2>
                    <p>900 San Marcelino St. Ermita, Manila, 1000 Metro Manila</p>
        </div>




    </body>
</html>