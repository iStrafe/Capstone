<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    @include('scripts')
    <body class="..">
        <div class="font-sans antialiased dark:bg-black dark:text-white/50">
            @include('Navigationbar')
        </div>

        <div class="about-section">
        <img src="images\cute-feline-cate.jpg" alt="Jane" style="width:100%; height: 300px;">
        </div>

        <div class="container">
            <h1>OUR SERVICES</h1>
            <div class="row">
                <div class="service">
                <a href="http://127.0.0.1:8000/Adopt"><h2>Adopt</h2></a>
                    <p>Give home to those in need, <br> Make them feel the warmth of a home, <br> Adopt an AduCat Now!</p>
                </div>
                <div class="service">
                <a href="http://127.0.0.1:8000/Report"><h2>Report Cat</h2></a>
                    <p>Notify us that require attention, <br> such as those in need of assistance, <br> food and shelter.</p>
                </div>
                <div class="service">
                <a href="http://127.0.0.1:8000/Donate"><h2>Donate</h2></a>
                    <p>Help our feline friends provide their needs, <br> We accept cashless donations</p>
                </div>
            </div>
        </div>


</body>
</html>