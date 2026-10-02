<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class PaymentController extends Controller
{
    private const FAILURE_MESSAGE = 'We could not create the payment link. Please try again later.';

    public function createPayment(Request $request)
    {
        // Validate the request inputs
        $request->validate([
            'amount' => 'required|numeric|min:1|max:100000|decimal:0,2',
            'description' => 'required|string|max:255',
        ], [
            'amount.max' => 'For gifts over ₱100,000, please contact us directly.',
        ]);

        // Get the user's input
        $amount = $request->input('amount');
        $description = $request->input('description');

        // Convert amount to cents (PayMongo requires the amount in cents)
        // Round: float maths turns 19.99 * 100 into 1998.999..., and PayMongo expects a whole number.
        $amountInCents = (int) round($amount * 100);

        // Load the PayMongo API key (read through config so it still works when config is cached)
        $secretKey = config('services.paymongo.secret_key');

        if (blank($secretKey)) {
            Log::error('PayMongo payment link skipped: PAYMONGO_SECRET_KEY is not set');

            return $this->failed();
        }

        try {
            // PayMongo uses Basic auth with the secret key as the username and an empty password
            $response = Http::withBasicAuth($secretKey, '')
                ->acceptJson()
                ->timeout(15)
                ->post('https://api.paymongo.com/v1/links', [
                    'data' => [
                        'attributes' => [
                            'amount' => $amountInCents, // Amount in cents
                            'description' => $description,
                            'remarks' => 'Payment for service',
                        ],
                    ],
                ]);
        } catch (ConnectionException|RequestException $e) {
            // DNS failure, timeout, refused connection, or a proxy answering with an error
            Log::error('PayMongo payment link failed: could not connect', ['message' => $e->getMessage()]);

            return $this->failed();
        }

        $paymentUrl = data_get($response->json(), 'data.attributes.checkout_url');

        if ($response->failed() || ! is_string($paymentUrl) || $paymentUrl === '') {
            // Log PayMongo's error body for us; don't show gateway internals to the visitor.
            Log::error('PayMongo payment link failed', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return $this->failed();
        }

        // Redirect to the payment link
        // Inertia::location also leaves the React pages cleanly (a plain redirect would be followed over XHR).
        return Inertia::location($paymentUrl);
    }

    private function failed()
    {
        return back()->withInput()->with('error', self::FAILURE_MESSAGE);
    }
}
