@include('scripts')
@include('admin.adminNavbar')

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Name</th>
            <th>Address</th>
            <th>Home Phone</th>
            <th>Mobile Phone</th>
            <th>Name of Cat</th>
            <th>Breed</th>
            <th>Approximate Age</th>
            <th>Sex</th>
            <th>Color</th>
            <th>Date of Adoption</th>
            <th>Medical Record</th>
        </tr>
    </thead>
    <tbody>
        @foreach($adoption_request as $request)
            <tr>
                <td>{{ $request->name }}</td>
                <td>{{ $request->address }}</td>
                <td>{{ $request->home_phone }}</td>
                <td>{{ $request->mobile_phone }}</td>
                <td>{{ $request->name_of_cat }}</td>
                <td>{{ $request->breed }}</td>
                <td>{{ $request->approximate_age }}</td>
                <td>{{ $request->sex }}</td>
                <td>{{ $request->color }}</td>
                <td>{{ $request->date_of_adoption }}</td>
                <td>{{ $request->Medical_Record }}</td>
            </tr>
        @endforeach
    </tbody>
</table>