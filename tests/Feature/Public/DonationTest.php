<?php

namespace Tests\Feature\Public;

use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DonationTest extends TestCase
{
    private const FAILURE_MESSAGE = 'We could not create the payment link. Please try again later.';

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.paymongo.secret_key' => 'sk_test_dummy']);
        Http::preventStrayRequests();
        $this->withoutMiddleware(ValidateCsrfToken::class);
    }

    private function donate(array $data = [])
    {
        return $this->from('/')->post('/payment', array_merge([
            'amount' => '150.50',
            'description' => 'For cat food',
        ], $data));
    }

    public function test_a_successful_link_redirects_to_paymongo_checkout(): void
    {
        Http::fake([
            'api.paymongo.com/*' => Http::response([
                'data' => ['attributes' => ['checkout_url' => 'https://pm.link/aducats/test/abc']],
            ]),
        ]);

        $this->donate()->assertRedirect('https://pm.link/aducats/test/abc');

        Http::assertSent(fn (Request $request) => $request->url() === 'https://api.paymongo.com/v1/links'
            && $request['data']['attributes']['amount'] === 15050
            && $request->hasHeader('Authorization', 'Basic '.base64_encode('sk_test_dummy:')));
    }

    public function test_an_unreachable_gateway_shows_a_friendly_error(): void
    {
        Http::fake(fn () => throw new ConnectionException('down'));

        $this->donate()
            ->assertRedirect('/')
            ->assertSessionHas('error', self::FAILURE_MESSAGE)
            ->assertSessionHasInput('amount', '150.50');
    }

    public function test_a_rejected_request_shows_a_friendly_error(): void
    {
        Http::fake(['api.paymongo.com/*' => Http::response(['errors' => [['detail' => 'bad key']]], 401)]);

        $this->donate()
            ->assertRedirect('/')
            ->assertSessionHas('error', self::FAILURE_MESSAGE);
    }

    public function test_a_response_without_a_checkout_url_shows_a_friendly_error(): void
    {
        Http::fake(['api.paymongo.com/*' => Http::response(['data' => []])]);

        $this->donate()
            ->assertRedirect('/')
            ->assertSessionHas('error', self::FAILURE_MESSAGE);
    }

    public function test_a_missing_secret_key_makes_no_outbound_call(): void
    {
        config(['services.paymongo.secret_key' => '']);
        Http::fake();

        $this->donate()
            ->assertRedirect('/')
            ->assertSessionHas('error', self::FAILURE_MESSAGE);

        Http::assertNothingSent();
    }

    public function test_the_amount_must_be_at_least_one_peso(): void
    {
        Http::fake();

        $this->donate(['amount' => '0'])->assertSessionHasErrors('amount');

        Http::assertNothingSent();
    }

    public function test_the_standalone_payment_page_is_gone(): void
    {
        $this->get('/payment')->assertMethodNotAllowed();
    }
}
