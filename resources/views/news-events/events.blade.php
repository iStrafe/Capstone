@include('scripts')
@include('admin.adminNavbar')

<div class="container mt-5">
    @foreach($newsEvent as $newsEvent) <!-- Loop through all news events -->
        <div class="card" style="width: 100%; max-width: 600px; margin: auto; margin-bottom: 20px;">
            <div class="card-body">
                <h1 class="card-title">{{ $newsEvent->title }}</h1>
                <p class="card-text">{{ $newsEvent->description }}</p>
                <p class="card-text"><strong>Date:</strong> {{ $newsEvent->event_date }}</p>

                @if(!empty($newsEvent->eventimage) && file_exists(public_path('images/' . $newsEvent->eventimage)))
    <div class="text-center">
        <img src="{{ asset('images/' . $newsEvent->eventimage) }}" alt="Image of {{ $newsEvent->title }}" class="img-fluid" style="max-width: 100%; height: auto;">
    </div>
@else
    <p class="text-muted">No image available for {{ $newsEvent->title }}</p>
@endif

            </div>
           
        </div>
    @endforeach
</div>
