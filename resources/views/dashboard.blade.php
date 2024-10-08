@include('scripts')
@include('Navigationbar')
<style>
    /* From Uiverse.io by G4b413l */ 
    .card {
        flex-wrap: wrap;
        position: relative;
        width: 25em;
        height: 25em;
        box-shadow: 0px 1px 13px rgba(0,0,0,0.1);
        cursor: pointer;
        transition: all 120ms;
        display: flex;
        align-items: center;
        justify-content: space-around;
        background: #fff;
        padding: 0.5em;
        padding-bottom: 3.4em;
    }

    .card::after {
        content: "Adopt Me!";
        padding-top: 1.25em;
        padding-left: 1.25em;
        position: absolute;
        left: 0;
        bottom: -60px;
        background: #00AC7C;
        color: #fff;
        height: 4em;
        width: 100%;
        transition: all 80ms;
        font-weight: 600;
        text-transform: uppercase;
        opacity: 0;
    }

    .card .title {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 0.9em;
        position: absolute;
        left: 0.625em;
        bottom: 1.875em;
        font-weight: 400;
        color: #000;
    }

    .card .price {
        font-family: Impact, Haettenschweiler, 'Arial Narrow Bold', sans-serif;
        font-size: 0.9em;
        position: absolute;
        left: 0.625em;
        bottom: 0.625em;
        color: #000;
    }

    .card:hover::after {
        bottom: 0;
        opacity: 1;
    }

    .card:active {
        transform: scale(0.98);
    }

    .card:active::after {
        content: "Added !";
        height: 3.125em;
    }

    .text {
        max-width: 55px;
    }

    .image {
        background: rgb(241, 241, 241);
        width: 100%;
        height: 70%; /* Adjust the height as needed */
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        background: rgb(241, 241, 241);
        display: grid;
        place-items: center;
    }
</style>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

 <!-- Masthead-->
        <header class="masthead">
            <div class="container2"><br><br>
                <div class="masthead-subheading">Welcome To Our AduCats Website!</div>
                <div class="masthead-heading text-uppercase">It's Nice To Meet You</div>
            </div>
        </header>

         <section class="bg-blacks">

            <div class="container">
               
                    <div class="center">
                    <h1>CAT GALLERY</h1>
                        <div class="input-group mb-3">
                                <input type="text" class="form-control" placeholder="Search Cat" aria-label="Recipient's username" aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button">search</button>
                            </div>
                        </div>
                    </div>

                <div class="row">
                    @foreach($cats as $cat)
                        <div class="col-md-4" data-bs-toggle="modal" data-bs-target="#modalId2">
                            <div class="card">
                                <div class="image">
                                    @if($cat->cat_image)
                                        <img src="{{ asset('images/' . $cat->cat_image) }}" alt="Image of {{ $cat->cat_name }}" style="width: 100%; height: auto;">
                                    @else
                                        <span class="text">No image available</span>
                                    @endif
                                </div>
                                <span class="title">{{ $cat->cat_name }}</span>
                                <span class="price">{{ $cat->age }} years old</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>  
</body>
</html>