<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AnalyzeImageTest extends TestCase
{
    private const FAILURE_MESSAGE = 'Image analysis is unavailable right now. Please try again later.';

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        config(['services.openai.key' => 'sk-test-dummy']);
        Http::preventStrayRequests();
        $this->withoutMiddleware(ValidateCsrfToken::class);
    }

    // Unsaved admin keeps these tests off the database; the middleware only reads the role.
    private function analyze()
    {
        $admin = User::factory()->make()->forceFill(['id' => 1, 'role' => 'admin']);

        return $this->actingAs($admin)
            ->from('/analyzeImage')
            ->post('/analyzeImage', ['image' => UploadedFile::fake()->image('cat.png')]);
    }

    public function test_a_successful_analysis_is_shown(): void
    {
        Http::fake([
            'api.openai.com/*' => Http::response([
                'choices' => [['message' => ['content' => "Color: Orange\nBreed: Puspin\n- short coat"]]],
            ]),
        ]);

        $this->analyze()
            ->assertOk()
            ->assertSee('Analysis Result:')
            ->assertSee('Breed: Puspin')
            ->assertSee('id="aiResponse"', false);

        Storage::disk('public')->assertDirectoryEmpty('uploads');
    }

    public function test_an_api_error_shows_a_friendly_message_and_removes_the_upload(): void
    {
        Http::fake(['api.openai.com/*' => Http::response(['error' => ['message' => 'bad key']], 401)]);

        $this->analyze()
            ->assertRedirect('/analyzeImage')
            ->assertSessionHas('error', self::FAILURE_MESSAGE);

        Storage::disk('public')->assertDirectoryEmpty('uploads');
    }

    public function test_a_connection_failure_shows_a_friendly_message_and_removes_the_upload(): void
    {
        Http::fake(fn () => throw new ConnectionException('down'));

        $this->analyze()
            ->assertRedirect('/analyzeImage')
            ->assertSessionHas('error', self::FAILURE_MESSAGE);

        Storage::disk('public')->assertDirectoryEmpty('uploads');
    }

    public function test_a_missing_api_key_makes_no_outbound_call(): void
    {
        config(['services.openai.key' => null]);
        Http::fake();

        $this->analyze()
            ->assertRedirect('/analyzeImage')
            ->assertSessionHas('error', self::FAILURE_MESSAGE);

        Http::assertNothingSent();
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_the_error_is_shown_on_the_page(): void
    {
        $admin = User::factory()->make()->forceFill(['id' => 1, 'role' => 'admin']);

        $this->actingAs($admin)
            ->withSession(['error' => self::FAILURE_MESSAGE])
            ->get('/analyzeImage')
            ->assertOk()
            ->assertSee(self::FAILURE_MESSAGE)
            ->assertDontSee('id="aiResponse"', false);
    }
}
