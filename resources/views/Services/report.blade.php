<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    @include('scripts')
    <style>

        

    </style>
    <body class="..">
        <div class="font-sans antialiased dark:bg-black dark:text-white/50">
            @include('Navigationbar')
        </div>
</head>
<body>


<form method="POST" action="{{route('admin.report')}}">
                @csrf
                @method('POST')
                <div>
                    <label for="">Name</label>
                    <input type="text" name="name" placeholder="name">
                </div>
                <div>
                    <label for="">Gender</label>
                    <input type="text" name="gender" placeholder="gender">
                </div>
                <div>
                    <label for="">Breed</label>
                    <input type="text" name="breed" placeholder="Breed">
                </div>
                <div>
                    <label for="">Eye Color</label>
                    <input type="text" name="eye_color" placeholder="eye color">
                </div>
                <div>
                    <label for="">Fur Color</label>
                    <input type="text" name="fur_color" placeholder="fur color">
                </div>
                <div>
                    <label for="">Last seen date</label>
                    <input type="date" name="last_seen_date" placeholder="last seen date">
                </div>
                <div>
                    <label for="">Last seen location</label>
                    <input type="text" name="last_seen_location" placeholder="last seen location">
                </div>
                <div>
                    <label for="">Contact email</label>
                    <input type="email" name="contact_email" placeholder="contact email">
                </div>
                <div>
                    <label for="">Upload image</label>
                    <input type="file" name="catimage" placeholder="upload image">
                </div>
                <div>
                    <label for="">Submit</label>
                    <input type="submit" value="Save new info">
                </div>
                    
            </form>


</body>
