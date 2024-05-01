
  
    <body class="font-sans antialiased dark:bg-black dark:text-white/50">
       <h1>Create info</h1>

        <div class="table-responsive">
            <table class="table table-striped table-hover table-condensed">
                    <tbody>
                        @foreach($displayData as $key => $data)
                        <tr>
                            <th>{{$data->id}}</th>
                            <th>{{$data->name}}</th>
                            <th>{{$data->gender}}</th>
                            <th>{{$data->breed}}</th>
                            <th>{{$data->eye_color}}</th>
                            <th>{{$data->fur_color}}</th>
                            <th>{{$data->description}}</th>
                            <th>{{$data->status}}</th>
                        </tr>
                        @endforeach
                    </tbody>
            </table>
         </div>

        
         
    </body>





