@include('NavigationBar')
<style>
    
</style>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Image for Analysis</title>
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}"> <!-- Link to your CSS file -->
</head>
<body>

     <div class="container">
        <h1 class="title">Upload a Cat Image for Analysis</h1>
        <form action="{{ route('analyze.image') }}" method="POST" enctype="multipart/form-data" class="form">
            @csrf
            <div class="form-group">
                <label for="image" class="form-label">Image:</label>
                <input type="file" id="image" name="image" class="form-input" required>
            </div>
            <button type="submit" class="btn btn-primary">Analyze Image</button>
        </form>

        <div class="container">
            @if(isset($response))
            <div class="result">
                <h2 class="result-title">Analysis Result:</h2>
                <pre class="result-content">{{ $response['choices'][0]['message']['content'] }}</pre>
            </div>
            @endif
        </div>
    </div>

</body>
</html>