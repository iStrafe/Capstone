@include('scripts')
@include('admin.adminNavbar')
    <h1>{{ $newsEvent->title }}</h1>
    <p>{{ $newsEvent->description }}</p>
    <p>{{ $newsEvent->event_date }}</p>

   <body>
    <div class="container w-50">
     <div class="shadow p-4 mb-4 bg--white" style="height: 450px;overflow: auto;">
        @foreach ($newsEvent as $event)
        <div class="card m-2">
            <div class="card-body pb-2">
            <h1>{{ $newsEvent->title }}</h1>
            <p>{{ $newsEvent->description }}</p>
            <p>{{ $newsEvent->event_date }}</p>
            </div>
        </div>
        @endforeach
     </div>
   </body>


@if($newsEvent->eventimage)
        <div>
            <img src="{{ asset('images/' . $newsEvent->eventimage) }}" alt="Image of {{ $newsEvent->eventimage }}" style="width: 300px; height: auto;">
        </div>
    @else
        <p>No image available for {{ $newsEvent->title }}</p>
    @endif
    <a href="{{ route('news-events.index') }}">Back to Events</a>
