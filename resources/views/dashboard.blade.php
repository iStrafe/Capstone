
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
                <div class="center">
                 <h1>SEARCH CAT</h1>
                    <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="Search Cat" aria-label="Recipient's username" aria-describedby="basic-addon2">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button">Advanced Search</button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
         
    </body>





