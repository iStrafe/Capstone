<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class OpenAIController extends Controller
{
     // Show the image upload form
    public function showUploadForm()
    {
        return view('analyzeImage');
    }

    // Handle image upload and send it to OpenAI API
    public function analyzeImage(Request $request)
    {
        // Validate the uploaded image
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Store the uploaded image
        $image = $request->file('image');
        $imagePath = $image->store('uploads', 'public');
        $imageUrl = asset('storage/' . $imagePath);

        // Encode the image to base64
        $base64Image = base64_encode(file_get_contents(storage_path('app/public/' . $imagePath)));

        // Prepare the payload for OpenAI API
        $payload = [
            "model" => "gpt-4o-mini",
            "messages" => [
                [
                    "role" => "user",
                    "content" => [
                        [
                            "type" => "text",
                            "text" => "Predict what cat breed is this? list down key features of this cat breed."
                        ],
                        [
                            "type" => "text",
                            "text" => "Reject the image if it is not a cat."
                        ],
                        [
                            "type" => "image_url",
                            "image_url" => [
                                "url" => "data:image/jpeg;base64,{$base64Image}"
                            ]
                        ]
                    ]
                ]
            ],
            "max_tokens" => 500
        ];

        // Send the request to OpenAI API
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
        ])->post('https://api.openai.com/v1/chat/completions', $payload);

        // Delete the image from storage
        Storage::delete('public/' . $imagePath);

        // Pass the response data to the view
        return view('analyzeImage', ['response' => $response->json()]);
    }
}
