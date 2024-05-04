
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@include('scripts')
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

    
    <h1>Create Information</h1>
     <div class="container">
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
       </div>
            <div class="container">
             <div class="row">
                <div class="col-auto mr-auto">
              <table border="1">
            @foreach($displayData as $catinfo)
            <tr>
                <td>{{$catinfo->id}}</td>
                <td>{{$catinfo->name}}</td>
                <td>{{$catinfo->gender}}</td>
                <td>{{$catinfo->eye_color}}</td>
                <td>{{$catinfo->fur_color}}</td>
                <td>{{$catinfo->description}}</td>
                <td></td>
            </tr>@endforeach
        </table>
                </div>
             </div>
           </div>
        
</body>
<style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
    }
    
    .container {
        max-width: 800px;
        margin: 20px auto;
        padding: 20px;
        border: 1px solid #ccc;
        border-radius: 5px;
        background-color: #f9f9f9;
    }
    
    form {
        margin-bottom: 20px;
    }
    
    label {
        font-weight: bold;
    }
    
    input[type="text"] {
        width: 100%;
        padding: 8px;
        margin-top: 6px;
        margin-bottom: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
    }
    
    input[type="submit"] {
        background-color: #4CAF50;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }
    
    input[type="submit"]:hover {
        background-color: #45a049;
    }
    
    table {
        width: 100%;
        border-collapse: collapse;
    }
    
    th, td {
        padding: 8px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }
    
    th {
        background-color: #4CAF50;
        color: white;
    }
    
    tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    h1 {
        text-align: center;
        font-size: 36px; /* Adjust font size as needed */
        font-family: "Arial Black", sans-serif; /* Noticeable font */
        color: #333; /* Darker color for better visibility */
    }
</style>

</html>