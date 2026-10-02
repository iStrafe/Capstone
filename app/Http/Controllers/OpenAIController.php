<?php

namespace App\Http\Controllers;

use App\Support\UploadLimit;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class OpenAIController extends Controller
{
    private const FAILURE_MESSAGE = 'Image analysis is unavailable right now. Please try again later.';

    // The breed helper page, with the last analysis (or why it failed) when there is one.
    public function showUploadForm(): Response
    {
        $analysis = session('analysis');

        return Inertia::render('Admin/BreedHelper', [
            'result' => is_string($analysis) ? self::parse($analysis) : null,
            'error' => session('analysis_error'),
            'analyzeUrl' => route('analyze.image'),
            'addCatUrl' => route('admin.cats.create'),
        ]);
    }

    /**
     * Pull the breed, color and listed traits out of the model's answer. The prompt asks for
     * "Color:", "Breed:" and a bulleted list, but the answer is free text, so anything that
     * doesn't fit is still shown as plain text.
     *
     * @return array{breed: ?string, color: ?string, traits: list<string>, text: string}
     */
    public static function parse(string $text): array
    {
        $field = function (string $label) use ($text): ?string {
            if (! preg_match('/^[\W_]*'.$label.'[\W_]*?:\s*(.+)$/mi', $text, $match)) {
                return null;
            }

            $value = trim(str_replace(['**', '__'], '', $match[1]), " \t*_");

            return $value === '' ? null : Str::limit($value, 100, '');
        };

        preg_match_all('/^\s*(?:[-*•]|\d+[.)])\s+(.+)$/mu', $text, $bullets);
        $traits = collect($bullets[1])
            ->map(fn (string $line) => trim(str_replace(['**', '__'], '', $line)))
            ->reject(fn (string $line) => $line === '' || preg_match('/^(color|breed)\s*:/i', $line))
            ->values()
            ->all();

        return ['breed' => $field('Breed'), 'color' => $field('Colou?r'), 'traits' => $traits, 'text' => trim($text)];
    }

    // Handle image upload and send it to OpenAI API
    public function analyzeImage(Request $request)
    {
        // Validate the uploaded image
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:10240',
        ], [
            // PHP drops a file over its upload_max_filesize before Laravel sees it.
            'image.uploaded' => 'The photo didn’t upload. This server takes files up to '.UploadLimit::label(UploadLimit::perFile()).', set by upload_max_filesize in php.ini.',
            'image.max' => 'The photo can be up to 10 MB.',
        ]);

        $apiKey = config('services.openai.key');

        if (blank($apiKey)) {
            Log::warning('Image analysis skipped: OPENAI_API_KEY is not set');

            return redirect()->route('analyze.form')->with('analysis_error', self::FAILURE_MESSAGE);
        }

        // Store the uploaded image
        $image = $request->file('image');
        $imagePath = $image->store('uploads', 'public');

        // Encode the image to base64
        $base64Image = base64_encode(Storage::disk('public')->get($imagePath));
        $mimeType = $image->getMimeType();

        // Prepare the payload for OpenAI API
        $payload = [
            'model' => 'gpt-4o-mini',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => 'Predict what cat breed is this. List down aleast 5 characteristics why you think so.',
                        ],
                        [
                            'type' => 'text',
                            'text' => 'Use this format: 
                                    Color:
                                    Breed:
                                    List of 5 characteristics(In bullet form):',
                        ],
                        [
                            'type' => 'text',
                            'text' => 'Reject the image if it is not a cat.',
                        ],
                        [
                            'type' => 'image_url',
                            'image_url' => [
                                'url' => "data:{$mimeType};base64,{$base64Image}",
                            ],
                        ],
                    ],
                ],
            ],
            'max_tokens' => 2000,
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

            return redirect()->route('analyze.form')->with('analysis_error', self::FAILURE_MESSAGE);
        }

        return redirect()->route('analyze.form')->with('analysis', $analysis);
    }
}
