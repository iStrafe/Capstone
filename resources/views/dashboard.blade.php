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
        margin-bottom: 15px;
        text-transform: uppercase;
        letter-spacing: 1px;
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

            <section class="bg-blacks">

            <div class="container">
               
                    <div class="center">
                    <h1>CAT GALLERY</h1>
                        <div class="input-group mb-3">
                                <input class="form-control" type="text" id="searchInput" placeholder="Search for cats..." aria-label="Recipient's username" aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" id="searchButton">search</button>
                            </div>
                        </div>
                    </div>
                    <!--Next & previous buttons-->
                    <div class="pagination-buttons">
                        <button class="btn btn-outline-secondary" type="button" id="prevBtn" style="display: none;">Previous</button>
                        <button class="btn btn-outline-secondary" type="button" id="nextBtn" style="display: none;">Next</button>
                    </div>

                <div class="row" id="catGallery">
                    @foreach($cats as $cat)
                        <div class="col-md-4 cat-card" data-bs-toggle="modal" data-bs-target="#modalId2">
                            <div class="card" data-name="{{ $cat->cat_name }}" data-age="{{ $cat->age }}" data-color="{{ $cat->color }}" data-breed="{{ $cat->breed }}" data-sex="{{ $cat->sex }}">
                                <div class="image">
                                    @if($cat->cat_image)
                                        <img src="{{ asset('images/' . $cat->cat_image) }}" alt="Image of {{ $cat->cat_name }}" style="width: 100%; height: auto;">
                                    @else
                                        <span class="text">No image available</span>
                                    @endif
                                </div>
                                <span class="title">{{ $cat->cat_name }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>

        <script>
        document.querySelector('#searchInput').addEventListener('input', function() {
            const searchInput = this.value.trim().toLowerCase();
            const searchTerms = searchInput.split(/[\s,]+/); // Split by spaces or commas
            const catCards = document.querySelectorAll('.cat-card');

            if (searchInput !== "") {
                catCards.forEach(function(card) {
                    const catName = card.querySelector('.title').textContent.toLowerCase().trim();
                    const catAge = card.querySelector('.card').getAttribute('data-age').toLowerCase().trim();
                    const catColor = card.querySelector('.card').getAttribute('data-color').toLowerCase().trim();
                    const catBreed = card.querySelector('.card').getAttribute('data-breed').toLowerCase().trim();
                    const catSex = card.querySelector('.card').getAttribute('data-sex').toLowerCase().trim();

                    const cardText = `${catName} ${catAge} ${catColor} ${catBreed} ${catSex}`.replace(/\s+/g, ' ');

                    const matches = searchTerms.every(term => cardText.includes(term));

                    if (matches) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            } else {
                catCards.forEach(function(card) {
                    card.style.display = 'block';
                });
            }
        });
        </script>
</body>
</html>