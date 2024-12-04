@include('Navigationbar')
@include('admin.cats.create') 
<style>
    /* From Uiverse.io by akshat-patel28 */ 
        .input-div {
        position: relative;
        width: 100px;
        height: 100px;
        border-radius: 50%;
        border: 2px solid rgb(1, 235, 252);
        display: flex;
        justify-content: center;
        align-items: center;
        overflow: hidden;
        box-shadow: 0px 0px 100px rgb(1, 235, 252) , inset 0px 0px 10px rgb(1, 235, 252),0px 0px 5px rgb(255, 255, 255);
        animation: flicker 2s linear infinite;
        }

        .icon {
        color: rgb(1, 235, 252);
        font-size: 2rem;
        cursor: pointer;
        animation: iconflicker 2s linear infinite;
        }

        .input {
        position: absolute;
        opacity: 0;
        width: 100%;
        height: 100%;
        cursor: pointer !important;
        }

        @keyframes flicker {
        0% {
            border: 2px solid rgb(1, 235, 252);
            box-shadow: 0px 0px 100px rgb(1, 235, 252) , inset 0px 0px 10px rgb(1, 235, 252),0px 0px 5px rgb(255, 255, 255);
        }

        5% {
            border: none;
            box-shadow: none;
        }

        10% {
            border: 2px solid rgb(1, 235, 252);
            box-shadow: 0px 0px 100px rgb(1, 235, 252) , inset 0px 0px 10px rgb(1, 235, 252),0px 0px 5px rgb(255, 255, 255);
        }

        25% {
            border: none;
            box-shadow: none;
        }

        30% {
            border: 2px solid rgb(1, 235, 252);
            box-shadow: 0px 0px 100px rgb(1, 235, 252) , inset 0px 0px 10px rgb(1, 235, 252),0px 0px 5px rgb(255, 255, 255);
        }

        100% {
            border: 2px solid rgb(1, 235, 252);
            box-shadow: 0px 0px 100px rgb(1, 235, 252) , inset 0px 0px 10px rgb(1, 235, 252),0px 0px 5px rgb(255, 255, 255);
        }
        }

        @keyframes iconflicker {
        0% {
            opacity: 1;
        }

        5% {
            opacity: 0.2;
        }

        10% {
            opacity: 1;
        }

        25% {
            opacity: 0.2;
        }

        30% {
            opacity: 1;
        }

        100% {
            opacity: 1;
        }
        }
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
                <h1 class="title">Upload a Cat Image to Verify</h1>
            <form action="{{ route('analyze.image') }}" method="POST" enctype="multipart/form-data" class="form">
                    @csrf
                    <!--<div class="form-group">
                        <label for="image" class="form-label">Image:</label>
                        <input type="file" id="image" name="image" class="form-input" required>
                    </div>-->
                    <div class="input-div" style="margin: 0 auto;">
                        <input class="input" type="file" id="image" name="image" class="form-input" required onchange="previewImage(event)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" stroke-linejoin="round" stroke-linecap="round" viewBox="0 0 24 24" stroke-width="2" fill="none" stroke="currentColor" class="icon"><polyline points="16 16 12 12 8 16"></polyline><line y2="21" x2="12" y1="12" x1="12"></line><path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"></path><polyline points="16 16 12 12 8 16"></polyline></svg>
                    </div><br>
                    <div style="text-align: center;">
                        <button type="submit" class="btn btn-primary" style="color: black;">Analyze Cat</button>
                        <button type="button" class="btn btn-secondary text-black" data-bs-toggle="modal" data-bs-target="#addCatModal" onclick="fillCatDetails()">Add this cat to Gallery?</button>
                    </div>
                    
            </form><br>

            <div class="container" style="overflow-x: hidden; border: 2px solid rgb(1, 235, 252); padding: 10px; border-radius: 10px; background-color: rgba(0, 0, 0, 0.8); color: white;">
                <h1 class="title py-1">Response</h1>
                @if(isset($response))
                <div class="result">
                    <h2 class="result-title">Analysis Result:</h2>
                    <pre class="result-content">{{ $response['choices'][0]['message']['content'] }}</pre>
                </div>
                <input type="hidden" id="aiResponse" value="{{ $response['choices'][0]['message']['content'] }}">
                @endif
            </div>

            

                <!-- Alert upon upload -->
            <div style="text-align: center;">
                <img id="uploadedImage" src="#" alt="Uploaded Image" style="max-width: 100%; height: auto; display: none;">
            </div>
        </div>
        
        <script>
            function previewImage(event) {
                var reader = new FileReader();
                reader.onload = function(){
                    var output = document.getElementById('uploadedImage');
                    output.src = reader.result;
                    output.style.display = 'block';
                    alert('Image uploaded successfully!');
                };
                reader.readAsDataURL(event.target.files[0]);
            }

            function fillCatDetails() {
                const aiResponse = document.getElementById('aiResponse').value;
                const colorMatch = aiResponse.match(/Color:\s*(\w+)/i);
                const breedMatch = aiResponse.match(/Breed:\s*(\w+)/i);
                const color = colorMatch ? colorMatch[1] : '';
                const breed = breedMatch ? breedMatch[1] : '';

                document.querySelector('#addCatModal input[name="color"]').value = color.toLowerCase();
                document.querySelector('#addCatModal input[name="breed"]').value = breed.toLowerCase();

                const uploadedImageSrc = document.getElementById('uploadedImage').src;
                document.querySelector('#addCatModal img#uploadedImage').src = uploadedImageSrc;
                document.querySelector('#addCatModal img#uploadedImage').style.display = 'block';
            }
        </script>

        @if(isset($response))
            <input type="hidden" id="aiResponse" value="{{ $response['choices'][0]['message']['content'] }}">
        @endif
    </div>

</body>

</body>
</html>