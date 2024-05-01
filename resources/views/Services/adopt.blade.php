<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    @include('scripts')
    <style>

        

    </style>
    <body class="..">
        <div class="font-sans antialiased dark:bg-black dark:text-white/50">
            @include('Navigationbar')
        </div>

        <div class="about-section">
        <img src="images\cute-feline-cate.jpg" alt="Jane" style="width:100%; height: 300px;">
        </div>


    <div class="container">
        <h1>Adopt Your Companion</h1>
        <div class="row">
            <div class="g-col-6">

                <div class="form-floating">
                    <select class="form-select" id="Breed" aria-label="Floating label select example">
                        <option selected>Choose a Breed</option>
                        <option value="1">Breed One</option>
                        <option value="2">Breed Two</option>
                        <option value="3">Breed Three</option>
                    </select>
                    <label for="floatingSelect">Breed:</label>
                </div>

                <br>

                <div class="form-floating">
                    <select class="form-select" id="Gender" aria-label="Floating label select example">
                        <option selected>Choose gender</option>
                        <option value="M">Male</option>
                        <option value="F">Female</option>
                    </select>
                    <label for="floatingSelect">Gender:</label>
                </div>

                <br>

                <div class="form-floating">
                    <select class="form-select" id="Size" aria-label="Floating label select example">
                        <option selected>Choose size</option>
                        <option value="1">Small</option>
                        <option value="2">Medium</option>
                        <option value="3">Large</option>
                    </select>
                    <label for="floatingSelect">Size:</label>
                </div>

                <br>

                <div class="form-floating">
                    <input type="input" class="form-control" id="Age" placeholder="age">
                    <label for="Age">Age:</label>
                </div>

                <br>

                <div class="form-floating">
                    <input type="input" class="form-control" id="Color" placeholder="color">
                    <label for="Color">Color:</label>
                </div>

                <br>
                
                <div class="form-floating">
                    <input type="input" class="form-control" id="Eye" placeholder="eye">
                    <label for="Eye">Eye Color:</label>
                </div>

                <br>
                
                <div class="form-floating">
                    <input type="input" class="form-control" id="Head" placeholder="head">
                    <label for="Head">Head Shape:</label>
                </div>

                <br>
                
                <div class="form-floating">
                    <input type="input" class="form-control" id="Traits" placeholder="traits">
                    <label for="Traits">Traits:</label>
                </div>

                <br>
            
            </div>

            <div class="g-col-6">

                <div id="carouselExampleIndicators" class="carousel slide">
                    <div class="carousel-indicators">
                        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
                        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
                        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="3" aria-label="Slide 4"></button>
                    </div>

                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <a href="#"><img src="images/cute-feline-cate.jpg" class="d-block w-100" alt="..."></a>
                        </div>

                        <div class="carousel-item">
                            <a href="#"><img src="images/cute-feline-cate.jpg" class="d-block w-100" alt="..."></a>
                        </div>

                        <div class="carousel-item">
                            <a href="#"><img src="images/cute-feline-cate.jpg" class="d-block w-100" alt="..."></a>
                        </div>
                        <div class="carousel-item">
                            <a href="#"><img src="images/cute-feline-cate.jpg" class="d-block w-100" alt="..."></a>
                        </div>
                    </div>

                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>

                    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>

                <br><br><br><br><br><br>

                <div class="d-grid gap-2 col-6 mx-auto">
                    <button type="button" class="btn btn-success">SEARCH</button>
                </div>
            </div>
        </div>

        <br>

    </div>           
</body>
</html>
