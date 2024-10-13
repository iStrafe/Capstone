@include('scripts')
@include('Navigationbar')

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Name</th>
            <th>Address</th>
            <!--<th>Phone Number</th>-->
            <th>Valid ID</th>
            <th>Cat Name</th>
            <th>Age</th>
            <th>Sex</th>
            <th>Color</th>
            <th>Breed</th>
            <!--<th>Date of Adoption</th>-->
        </tr>
    </thead>
    <tbody>
        @foreach($adoption_request as $request)
            <tr>
                <td>{{ $request->name }}</td>
                <td>{{ $request->address }}</td>
              
                <td>
                    <a href="{{ asset('storage/valid_id/' . $request->valid_id) }}" target="_blank">
                        View ID
                    </a>
                </td>
                <td>{{ $request->name_of_cat }}</td>
                <td>{{ $request->approximate_age }}</td>
                <td>{{ $request->sex }}</td>
                <td>{{ $request->color }}</td>
                <td>{{ $request->breed }}</td>
                
            </tr>
        @endforeach
    </tbody>
</table>