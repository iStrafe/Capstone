<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    @include('scripts')
    <body class="..">
        <div class="font-sans antialiased dark:bg-black dark:text-white/50">
            @include('Navigationbar')
        </div>

        <div class="border border-warning">
        <img src="images\Cat2.jpg" alt="Jane" style="width:100%; height: 500px;">
        </div>

        <div class="container">
            <h1>OUR SERVICES</h1>
            <div class="row">

<<<<<<< HEAD
                <a href="http://127.0.0.1:8000/report"><div class="service">
                <h2>Report Cat</h2>
<<<<<<< HEAD
=======
                </div>
                <div class="service">
                <a href="http://127.0.0.1:8000/report"><h2>Report Cat</h2></a>
>>>>>>> 2ec0914622893a991e320805a0111cd271a020df
                    <p>Notify us that require attention, <br> such as those in need of assistance, <br> food and shelter.</p>
                </div></a>
=======
                <a href="{{url('adopt')}}">
                    <div class="service">
                        <h2>Adopt</h2>
                        <p>Give home to those in need, <br> Make them feel the warmth of a home, <br> Adopt an AduCat Now!</p>
                    </div>
                </a>
>>>>>>> ad806b514497c2dac495421d15b0940c3727494e

                <a href="{{url('adopt')}}">
                    <div class="service">
                        <h2>Report Cat</h2>
                        <p>Notify us that require attention, <br> such as those in need of assistance,  <br> food and shelter.</p>
                    </div>
                </a>

                <a href="{{url('adopt')}}">
                    <div class="service">
                        <h2>Donate</h2>
                        <p>Help our feline friends provide their needs, <br> We accept cashless donations</p>
                    </div>
                </a>
                
            </div>
        </div>


</body>
</html>