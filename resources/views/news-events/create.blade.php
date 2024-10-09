@include('scripts')
@include('admin.adminNavbar')
    <h1>Create News Event</h1>
    <form action="{{ route('news-events.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <!-- Title Field -->
    <div class="mb-3">
        <label for="title">Title:</label>
        <input type="text" name="title" id="title" class="form-control">
    </div>

    <!-- Description Field -->
    <div class="mb-3">
        <label for="description">Description:</label>
        <textarea name="description" id="description" class="form-control"></textarea>
    </div>

    <!-- Event Date Field -->
    <div class="mb-3">
        <label for="event_date">Event Date:</label>
        <input type="date" name="event_date" id="event_date" class="form-control">
    </div>

    <!-- Event Image Field -->
    <div class="mb-3">
        <label for="eventimage" class="form-label">Event Image:</label>
        <input type="file" name="eventimage" class="form-control">
    </div>

    <!-- Submit Button -->
    <button type="submit" class="btn btn-primary">Create</button>
</form>

