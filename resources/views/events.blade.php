<head>
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>

@include('scripts')

<body class="font-sans antialiased dark:bg-black dark:text-white/50">
    <div class="border border-warning">
        @include('Navigationbar')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <div class="container my-5">
        <!-- Add your image carousel here if needed -->

        <!-- Image Cards -->
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <div class="col">
                <div class="card">
                    <img src="public\images\lost cats.jpg" class="card-img-top" alt="Image 1">
                    <div class="card-body">
                        <h5 class="card-title">Card Title 1</h5>
                        <p class="card-text">Description for card 1.</p>
                        <a href="#" class="btn btn-primary">Edit</a>
                        <a href="#" class="btn btn-danger">Delete</a>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card">
                    <img src="public/images/image2.jpg" class="card-img-top" alt="Image 2">
                    <div class="card-body">
                        <h5 class="card-title">Card Title 2</h5>
                        <p class="card-text">Description for card 2.</p>
                        <a href="#" class="btn btn-primary">Edit</a>
                        <a href="#" class="btn btn-danger">Delete</a>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card">
                    <img src="public/images/image3.jpg" class="card-img-top" alt="Image 3">
                    <div class="card-body">
                        <h5 class="card-title">Card Title 3</h5>
                        <p class="card-text">Description for card 3.</p>
                        <a href="#" class="btn btn-primary">Edit</a>
                        <a href="#" class="btn btn-danger">Delete</a>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card">
                    <img src="public/images/image4.jpg" class="card-img-top" alt="Image 4">
                    <div class="card-body">
                        <h5 class="card-title">Card Title 4</h5>
                        <p class="card-text">Description for card 4.</p>
                        <a href="#" class="btn btn-primary">Edit</a>
                        <a href="#" class="btn btn-danger">Delete</a>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card">
                    <img src="public/images/image5.jpg" class="card-img-top" alt="Image 5">
                    <div class="card-body">
                        <h5 class="card-title">Card Title 5</h5>
                        <p class="card-text">Description for card 5.</p>
                        <a href="#" class="btn btn-primary">Edit</a>
                        <a href="#" class="btn btn-danger">Delete</a>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card">
                    <img src="public/images/image6.jpg" class="card-img-top" alt="Image 6">
                    <div class="card-body">
                        <h5 class="card-title">Card Title 6</h5>
                        <p class="card-text">Description for card 6.</p>
                        <a href="#" class="btn btn-primary">Edit</a>
                        <a href="#" class="btn btn-danger">Delete</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        h1 {
            font-size: 50px;
        }

        body {
            background-image: url("public/images/loginbg.jpg");
        }

        h2 {
            font-size: 45px;
            text-align: center;
        }

        /* Adjust carousel image sizes */
        .carousel-inner img {
            max-width: 100%;
            height: auto;
        }

        /* Center carousel within the container */
        .carousel {
            max-width: 50%;
            margin: 0 auto;
        }
    </style>
</body>

</html>
