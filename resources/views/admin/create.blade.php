<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Create Information</h1>
         <form method="POST" action="{{route('admin.store')}}">
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
                    <label for="">Description</label>
                    <input type="text" name="description" placeholder="description">
                </div>
                <div>
                    <label for="">Status</label>
                    <input type="text" name="status" placeholder="status">
                </div>
                <div>
                    <label for="">Submit</label>
                    <input type="submit" value="Save new info">
                </div>
                    
            </form>
<!--
              <table border="1">
            @foreach($name as $catinfo)
            <tr>
                <td>{{$catinfo->id}}</td>
                <td>{{$catinfo->name}}</td>
                <td>{{$catinfo->gender}}</td>
                <td>{{$catinfo->eye_color}}</td>
                <td>{{$catinfo->fur_color}}</td>
                <td>{{$catinfo->description}}</td>
                <td></td>
            </tr>@endforeach
        </table>-->
</body>
</html>