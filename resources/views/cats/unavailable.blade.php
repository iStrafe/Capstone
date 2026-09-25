@include('scripts')
@php
    $messages = [
        'archived' => [
            'title' => 'This cat\'s profile has been archived',
            'text' => $cat->cat_name.' is no longer listed for adoption.',
        ],
        'adopted' => [
            'title' => $cat->cat_name.' has been adopted',
            'text' => 'Good news: '.$cat->cat_name.' has already gone home with a new family, so we are not taking adoption requests for this cat any more.',
        ],
        'reserved' => [
            'title' => 'Adoption in progress',
            'text' => 'An adoption request for '.$cat->cat_name.' has been approved and the cat is getting ready to go home, so we are not taking new requests for now.',
        ],
        'inactive' => [
            'title' => $cat->cat_name.' is not available right now',
            'text' => $cat->cat_name.' is not open for adoption at the moment. Please check back later or meet the other cats looking for a home.',
        ],
    ];
    $message = $messages[$reason] ?? $messages['inactive'];
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $cat->cat_name }} is not available</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    @include('Navigationbar')
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }

        .status-wrap {
            max-width: 560px;
            margin: 50px auto;
            padding: 0 15px;
        }

        .cat-status-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
        }

        .cat-image {
            display: block;
            max-width: 100%;
            max-height: 320px;
            object-fit: cover;
            border-radius: 10px;
            margin: 0 auto 20px;
        }

        .cat-status-card.is-unavailable .cat-image {
            filter: grayscale(60%);
        }

        h1 {
            font-size: 2rem;
            color: #343a40;
            margin-bottom: 15px;
        }

        .status-text {
            font-size: 1.15rem;
            color: #495057;
            margin-bottom: 15px;
        }

        .archive-reason {
            text-align: left;
            background: #f1f3f5;
            border-left: 4px solid #6c757d;
            border-radius: 6px;
            padding: 12px 16px;
            margin: 15px 0;
            color: #343a40;
        }

        .back-button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 1.1rem;
            border-radius: 5px;
        }

        .back-button:hover {
            background-color: #0056b3;
            color: white;
        }
    </style>
</head>
<body>
    <div class="status-wrap">
        <div class="cat-status-card is-unavailable" data-cat-status="{{ $reason }}">
            @if($cat->cat_image)
                <img src="{{ asset('images/' . $cat->cat_image) }}" alt="Image of {{ $cat->cat_name }}" class="cat-image">
            @else
                {{-- Stand-in picture for cats without a photo --}}
                <img src="{{ asset('images/placeholder.png') }}" alt="No image available for {{ $cat->cat_name }}" class="cat-image">
            @endif

            <h1>{{ $message['title'] }}</h1>
            <p class="status-text">{{ $message['text'] }}</p>

            @if($reason === 'archived')
                <div class="archive-reason">
                    <strong>Why:</strong>
                    {{ filled($cat->archive_reason) ? $cat->archive_reason : 'The shelter has not given a reason for archiving this profile. Contact us if you have questions about this cat.' }}
                    <div class="small text-muted mt-1">Archived on {{ $cat->archived_at->format('M j, Y') }}</div>
                </div>
            @endif

            <a href="{{ route('adoptCat') }}" class="btn back-button">See cats available for adoption</a>
        </div>
    </div>
</body>
</html>
