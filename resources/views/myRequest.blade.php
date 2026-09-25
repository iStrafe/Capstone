@include('scripts')
@include('Navigationbar')

<div class="container mt-5">
    <h1 class="text-center">My Adoption Requests</h1>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($adoption_request->isEmpty())
        <p class="text-center">You haven't sent any adoption requests yet. <a href="{{ route('adoptCat') }}">See the cats available for adoption.</a></p>
    @else
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Cat</th>
                    <th>Date of Adoption</th>
                    <th>Status</th>
                    <th>Submitted</th>
                </tr>
            </thead>
            <tbody>
                @foreach($adoption_request as $request)
                    <tr>
                        <td>{{ $request->cat?->cat_name ?? $request->name_of_cat }}</td>
                        <td>{{ $request->date_of_adoption?->format('M j, Y') }}</td>
                        <td>{{ $request->status }}</td>
                        <td>{{ $request->created_at?->format('M j, Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
