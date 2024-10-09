@include('scripts')
@include('admin.adminNavbar')

<h1>Edit News Event</h1>

<form action="{{ route('news-events.update', $newsEvent->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <!-- Title Field -->
    <div class="mb-3">
        <label for="title">Title:</label>
        <input type="text" name="title" id="title" value="{{ $newsEvent->title }}" class="form-control">
    </div>

    <!-- Description Field -->
    <div class="mb-3">
        <label for="description">Description:</label>
        <textarea name="description" id="description" class="form-control">{{ $newsEvent->description }}</textarea>
    </div>

    <!-- Event Date Field -->
    <div class="mb-3">
        <label for="event_date">Event Date:</label>
        <input type="date" name="event_date" id="event_date" value="{{ $newsEvent->event_date }}" class="form-control">
    </div>

    <!-- Submit Button -->
    <button type="submit" class="btn btn-primary">Update</button>
</form>
