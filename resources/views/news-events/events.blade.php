@include('scripts')
@include('Navigationbar')

<div class="container mt-5">
    @foreach($newsEvent as $newsEvent) <!-- Loop through all news events -->
        <div class="card-wrapper" style="width: 100%; max-width: 1000px; margin: auto; margin-bottom: 50px;">
            <div class="card" style="font-family: 'Poppins', sans-serif;">
                <div class="card-body">
                    <!-- Title with larger font size -->
                    <h1 class="card-title" style="font-size: 2.5rem; font-weight: 600; color: white;">{{ $newsEvent->title }}</h1>
                    
                    <!-- Description with larger font size -->
                    <p class="card-text" style="font-size: 1.25rem; color: white;">{{ $newsEvent->description }}</p>
                    
                    <p class="card-text" style="font-size: 1rem; color: white;"><strong>Date:</strong> {{ $newsEvent->event_date }}</p>
                </div>
            </div>
            @if(!empty($newsEvent->eventimage) && file_exists(public_path('images/' . $newsEvent->eventimage)))
                <div class="image-container d-flex justify-content-center" style="margin-top: 15px;">
                    <img src="{{ asset('images/' . $newsEvent->eventimage) }}" alt="Image of {{ $newsEvent->title }}"  
                         class="img-fluid" style="max-width: 100%; height: auto; border-radius: 1rem;">
                </div>
            @else
                <p class="text-muted">No image available for {{ $newsEvent->title }}</p>
            @endif
        </div>
    @endforeach
</div>

<style>
    .card {
        border-radius: 1rem;
        background-color: #4158D0;
        background-image: linear-gradient(43deg, #4158D0 0%, #C850C0 46%, #FFCC70 100%);
        box-shadow: rgba(0, 0, 0, 0.17) 0px -23px 25px 0px inset, rgba(0, 0, 0, 0.15) 0px -36px 30px 0px inset, 
                    rgba(0, 0, 0, 0.1) 0px -79px 40px 0px inset, rgba(0, 0, 0, 0.06) 0px 2px 1px, 
                    rgba(0, 0, 0, 0.09) 0px 4px 2px, rgba(0, 0, 0, 0.09) 0px 8px 4px, 
                    rgba(0, 0, 0, 0.09) 0px 16px 8px, rgba(0, 0, 0, 0.09) 0px 32px 16px;
    }

    .card-body {
        padding: 1rem;
    }

    h1, p {
        margin-bottom: 1rem;
    }

    .image-container {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    img {
        border-radius: 1rem;
        box-shadow: rgba(0, 0, 0, 0.3) 0px 10px 20px;
    }
</style>
