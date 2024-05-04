<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    @include('scripts')
    <style>

        

    </style>
    <body class="..">
        <div class="font-sans antialiased dark:bg-black dark:text-white/50 border border-warning">
            @include('Navigationbar')
        </div>
</head>
<body>

<div class="about-section">
        <img src="images\Cat2.jpg" alt="Jane" style="width:100%; height: 500px;">
        </div>

<div class="container">
    <div class="row">
        <div class="col-sm">
            <form method="POST" action="{{route('admin.report')}}">
                @csrf
                @method('POST')
            <div class="container">
                <div class="form-control">
                    <label for="">Name</label>
                    <input type="text" name="name" placeholder="name">
                </div>
                <div class="form-control">
                    <label for="">Gender</label>
                    <input type="text" name="gender" placeholder="gender">
                </div>
                <div class="form-control">
                    <label for="">Breed</label>
                    <input type="text" name="breed" placeholder="Breed">
                </div>
                <div class="form-control">
                    <label for="">Eye Color</label>
                    <input type="text" name="eye_color" placeholder="eye color">
                </div>
                <div class="form-control">
                    <label for="">Fur Color</label>
                    <input type="text" name="fur_color" placeholder="fur color">
                </div>
                <div class="form-control">
                    <label for="">Last seen date</label>
                    <input type="date" name="last_seen_date" placeholder="last seen date">
                </div>
                <div class="form-control">
                    <label for="">Last seen location</label>
                    <input type="text" name="last_seen_location" placeholder="last seen location">
                </div>
                <div class="form-control">
                    <label for="">Contact email</label>
                    <input type="email" name="contact_email" placeholder="contact email">
                </div>
                <div class="form-control">
                    <label for="">Upload image</label>
                    <input type="file" name="catimage" placeholder="upload image">
                </div>
                <div class="btn btn-outline-secondary" >
                    <label for="">Submit</label>
                    <input type="submit" value="Save new info">
                </div>
            </div>
                    
            </form>
        </div>
        <div calss="col-sm">

<div class="card" style="width: 18rem;">
<img class="card-img-top" src="images\lost cats.jpg" alt="Card image cap">
<div class="card-body">

<a href="" class="btn btn-primary btn-lg active" role="button" aria-pressed="true">Lost Cats</a>

</div>
</div> 






        </div>
        <div calss="col-sm">

        <div class="card" style="width: 25rem;">
  <img class="card-img-top" src="images\found cats.jpg" alt="Card image cap">
  <div class="card-body">

  <a href="" class="btn btn-primary btn-lg active" role="button" aria-pressed="true">Found Cats</a>

  </div>
</div> 

  </div>
</div> 
        </div>
</div>
</div>
</body>
