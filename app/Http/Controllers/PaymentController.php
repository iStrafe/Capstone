<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
     public function paymentView(){
        return view('payment');
    }

    public function createPayment(Request $request)
    {
        // Validate the request inputs
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'description' => 'required|string|max:255',
        ]);

        // Get the user's input
        $amount = $request->input('amount');
        $description = $request->input('description');

        // Convert amount to cents (PayMongo requires the amount in cents)
        $amountInCents = $amount * 100;

        // Load the PayMongo API key (read through config so it still works when config is cached)
        $secretKey = config('services.paymongo.secret_key');

        // Correctly format the Authorization header with base64 encoding
        $encodedKey = base64_encode($secretKey);

        $client = new Client();

        try {
            // Send the POST request to PayMongo API
            $response = $client->request('POST', 'https://api.paymongo.com/v1/links', [
                'json' => [
                    'data' => [
                        'attributes' => [
                            'amount' => $amountInCents, // Amount in cents
                            'description' => $description,
                            'remarks' => 'Payment for service',
                        ]
                    ]
                ],
                'headers' => [
                    'Authorization' => 'Basic ' . $encodedKey, // Correct Authorization header
                    'accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
            ]);

            // Get the payment link
            $responseBody = json_decode($response->getBody(), true);
            $paymentUrl = $responseBody['data']['attributes']['checkout_url'];

            // Redirect to the payment link
            return redirect($paymentUrl);

        } catch (RequestException $e) {
            // Handle API errors
            // Log PayMongo's error body for us; don't show gateway internals to the visitor.
            Log::error('PayMongo payment link failed', [
                'response' => $e->hasResponse() ? (string) $e->getResponse()->getBody() : $e->getMessage(),
            ]);

            return back()->with('error', 'We could not create the payment link. Please try again later.');
        }
    }
   
}
