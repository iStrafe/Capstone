<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class OpenAIController extends Controller
{
    private const FAILURE_MESSAGE = 'Image analysis is unavailable right now. Please try again later.';

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

        $apiKey = config('services.openai.key');

        if (blank($apiKey)) {
            Log::warning('Image analysis skipped: OPENAI_API_KEY is not set');

            return back()->with('error', self::FAILURE_MESSAGE);
        }

        // Store the uploaded image
        $image = $request->file('image');
        $imagePath = $image->store('uploads', 'public');

        // Encode the image to base64
        $base64Image = base64_encode(Storage::disk('public')->get($imagePath));

        // Prepare the payload for OpenAI API
        $payload = [
            "model" => "gpt-4o-mini",
            "messages" => [
                [
                    "role" => "user",
                    "content" => [
                        [
                            "type" => "text",
                            "text" => "Predict what cat breed is this. List down aleast 5 characteristics why you think so."
                        ],
                        [
                            "type" => "text",
                            "text" => "Use this format: 
                                    Color:
                                    Breed:
                                    List of 5 characteristics(In bullet form):"
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
            "max_tokens" => 2000
        ];

        // Send the request to OpenAI API; the upload is removed whatever happens
        $response = null;

        try {
            $response = Http::withToken($apiKey)
                ->acceptJson()
                ->timeout(30)
                ->post('https://api.openai.com/v1/chat/completions', $payload);
        } catch (ConnectionException $e) {
            Log::warning('Image analysis failed: could not connect to OpenAI', ['message' => $e->getMessage()]);
        } finally {
            Storage::disk('public')->delete($imagePath);
        }

        $analysis = data_get($response?->json(), 'choices.0.message.content');

        if (! $response || $response->failed() || ! is_string($analysis) || $analysis === '') {
            if ($response) {
                Log::warning('Image analysis failed', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);
            }

            return back()->with('error', self::FAILURE_MESSAGE);
        }

        // Pass only the analysis text to the view
        return view('analyzeImage', ['analysis' => $analysis]);
    }
}
