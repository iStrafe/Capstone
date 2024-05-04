
    @include('scripts')
    <body class="font-sans antialiased dark:bg-black dark:text-white/50">

    <div class="border border-warning">
        @include('Navigationbar')
    </div>
        

        
        <div class="border border-warning">
        <img src="images\Cat2.jpg"  style="width:100%; height: 500px;">
        </div>

         <section class="bg-blacks">
            <div class="container">
             <h1>SEARCH CAT</h1>
                <div class="center">
                    <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="Search Cat" aria-label="Recipient's username" aria-describedby="basic-addon2">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button">Advanced Search</button>
                        </div>
                    </div>
                </div>

                <div class = "container">
                    <div class = "row">
                        <div class="card-group">@foreach($displayData as $catinfo)
                            <div class="card border border-warning mb-3" style="max-width: 18rem;">
                                <div class="card-header bg-transparent border-success">{{$catinfo->name}}</div>
                                 <img class="card-img-top" src="..." alt="Card image cap">
                                <div class="card-body text-success bg-transparent">
                                    <h5 class="card-title">Gender: {{$catinfo->gender}}</h5>
                                    <h5 class="card-title">Eye color: {{$catinfo->eye_color}}</h5>
                                    <h5 class="card-title">Fur color: {{$catinfo->fur_color}}</h5>
                                    <p class="card-text">Description: {{$catinfo->description}}</p>
                                </div>
                                <div class="card-footer bg-transparent border-success">
                                      <a href="#" class="btn btn-primary">Adopt</a>
                                </div>
                            </div>
                        </table>@endforeach
                    </div>
                </div>

            </div>
        </section>        
    </body>
    





