<!DOCTYPE html>
<html lang="en">
@include('Navigationbar')
<body>
<h1>ADOPTION CONTRACT</h1>
    <form action="{{route('adoption.request')}}" method="post">
        @csrf
        @method('POST')
        <label for="name">Name</label>
        <input type="text" name="name" required>

        <label for="address">Address</label>
        <input type="text" name="address" required>

        <label for="phone">Phone Number</label>
        <input type="phone" name="phone">
      
        <label for="name_of_cat">Cat Name</label>
        <input type="text" name="name_of_cat" required>

        <label for="cat_image">Cat Image</label>
        <input type="file" name="cat_image">

        <label for="age">Age</label>
        <input type="number" name="age" required>

        <label for="sex">Sex</label>
        <select name="sex" required>
            <option value="male">Male</option>
            <option value="female">Female</option>
        </select>

        <label for="color">Color</label>
        <input type="text" name="color" required>

        <label for="breed">Breed</label>
        <input type="text" name="breed" required>

        <label for="date_of_adoption">Date of Adoption</label>
        <input type="date" name="date_of_adoption" required>

         <div class="btn btn-outline-secondary" >
            <label for="">Submit</label>
            <input type="submit" value="Save new info">
        </div>
    </form>
</body>
</html>