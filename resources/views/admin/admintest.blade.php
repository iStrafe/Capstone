
  
    <body class="font-sans antialiased dark:bg-black dark:text-white/50">
       <h1>Create info</h1>
            <form method="POST" action="{{route('admin.store')}}">
                @csrf
                @method('POST')
                <div>
                    <label for="">Name</label>
                    <input type="text" name="name" placeholder="name">
                </div>
                <div>
                    <label for="">Gender</label>
                    <input type="name" name="gander" placeholder="gender">
                </div>
                <div>
                    <label for="">Breed</label>
                    <input type="name" name="Breed" placeholder="Breed">
                </div>
                <div>
                    <label for="">Eye Color</label>
                    <input type="name" name="eye_color" placeholder="eye color">
                </div>
                <div>
                    <label for="">Fur Color</label>
                    <input type="name" name="fur_color" placeholder="fur color">
                </div>
                <div>
                    <label for="">Description</label>
                    <input type="name" name="description" placeholder="description">
                </div>
                <div>
                    <label for="">Status</label>
                    <input type="name" name="status" placeholder="status">
                </div>
                <div>
                    <label for="">Submit</label>
                    <input type="submit" value="Save new info" name="name" placeholder="name">
                </div>
                    
            </form>
            

       

        
         
    </body>





