@include('scripts')
@include('admin.adminNavbar')

<style>
    .news-event-show .card {
        background: #fff;
        border: 1px solid rgba(0, 0, 0, .125);
        border-radius: 8px;
        overflow: hidden;
    }

    .news-event-show h1 {
        font-size: clamp(1.5rem, 4vw, 2.25rem);
        font-weight: 600;
        text-align: left;
        padding-top: 0;
        margin-bottom: .25rem;
        overflow-wrap: anywhere;
    }

    .news-event-show .event-date {
        color: #555;
        margin-bottom: 1rem;
    }

    .news-event-show .event-description {
        white-space: pre-line;
        overflow-wrap: anywhere;
    }
</style>

<div class="container news-event-show py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                @if ($newsEvent->eventimage)
                    <img src="{{ asset('images/' . $newsEvent->eventimage) }}" alt="Event photo" class="card-img-top">
                @endif
                <div class="card-body p-4">
                    <h1>{{ $newsEvent->title }}</h1>
                    @if ($newsEvent->event_date)
                        <p class="event-date">{{ $newsEvent->event_date->format('F j, Y') }}</p>
                    @endif
                    <p class="event-description">{{ $newsEvent->description }}</p>
                </div>
            </div>

            <a href="{{ route('news-events.index') }}" class="btn btn-secondary">Back to Events</a>
        </div>
    </div>
</div>
