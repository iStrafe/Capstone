@include('scripts')
@include('Navigationbar')
<style>
    .card {
                flex-wrap: wrap;
                position: relative;
                width: 25em;
                height: 22em;
                box-shadow: 0px 1px 13px rgba(0,0,0,0.1);
                cursor: pointer;
                transition: all 120ms;
                display: flex;
                align-items: center;
                justify-content: space-around;
                background: #fff;
                padding: 0.5em;
                padding-bottom: 3.4em;
            }
</style>

<div class="container mt-5">
    <h1 class="mb-4">Valid IDs</h1>
    @if($valid_ids)
        <div class="row">
            @foreach ($valid_ids as $valid_id)
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <img src="{{ asset('images/' . $valid_id) }}" class="card-img-top" alt="Valid ID">
                        <div class="card-body">
                            <p class="card-text">Valid ID</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-warning" role="alert">
            No IDs available.
        </div>
    @endif
</div>