<?php

namespace Tests\Feature\Admin;

use App\Http\Controllers\OpenAIController;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AnalyzeImageTest extends TestCase
{
    private const FAILURE_MESSAGE = 'Image analysis is unavailable right now. Please try again later.';

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        Storage::fake('local');
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
            ->assertRedirect('/analyzeImage')
            ->assertSessionHas('analysis', "Color: Orange\nBreed: Puspin\n- short coat");

        Storage::disk('public')->assertDirectoryEmpty('/');
        Storage::disk('local')->assertDirectoryEmpty('/');
    }

    public function test_the_photo_is_sent_with_its_own_type(): void
    {
        Http::fake(['api.openai.com/*' => Http::response(['choices' => [['message' => ['content' => 'Breed: Puspin']]]])]);

        $this->analyze()->assertSessionHas('analysis');

        Http::assertSent(fn ($request) => str_starts_with(data_get($request->data(), 'messages.0.content.3.image_url.url'), 'data:image/png;base64,'));
    }

    public function test_the_result_is_shown_on_the_page_as_breed_color_and_traits(): void
    {
        $admin = User::factory()->make()->forceFill(['id' => 1, 'role' => 'admin']);

        $this->actingAs($admin)
            ->withSession(['analysis' => "**Color:** Orange and white\n**Breed:** Puspin\n**List of 5 characteristics:**\n1. **Short coat** that lies flat\n2. Lean build\n- Almond eyes"])
            ->get('/analyzeImage')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/BreedHelper')
                ->where('result.breed', 'Puspin')
                ->where('result.color', 'Orange and white')
                ->where('result.traits', ['Short coat that lies flat', 'Lean build', 'Almond eyes'])
                ->where('error', null)
                ->where('addCatUrl', route('admin.cats.create')));
    }

    public function test_an_answer_without_the_format_is_shown_as_text(): void
    {
        $this->assertSame(
            ['breed' => null, 'color' => null, 'traits' => [], 'text' => "Sorry, this doesn't look like a cat."],
            OpenAIController::parse("Sorry, this doesn't look like a cat.\n"),
        );
    }

    public function test_an_api_error_shows_a_friendly_message_and_removes_the_upload(): void
    {
        Http::fake(['api.openai.com/*' => Http::response(['error' => ['message' => 'bad key']], 401)]);

        $this->analyze()
            ->assertRedirect('/analyzeImage')
            ->assertSessionHas('analysis_error', self::FAILURE_MESSAGE);

        Storage::disk('public')->assertDirectoryEmpty('/');
        Storage::disk('local')->assertDirectoryEmpty('/');
    }

    public function test_a_connection_failure_shows_a_friendly_message_and_removes_the_upload(): void
    {
        Http::fake(fn () => throw new ConnectionException('down'));

        $this->analyze()
            ->assertRedirect('/analyzeImage')
            ->assertSessionHas('analysis_error', self::FAILURE_MESSAGE);

        Storage::disk('public')->assertDirectoryEmpty('/');
        Storage::disk('local')->assertDirectoryEmpty('/');
    }

    public function test_a_missing_api_key_makes_no_outbound_call(): void
    {
        config(['services.openai.key' => null]);
        Http::fake();

        $this->analyze()
            ->assertRedirect('/analyzeImage')
            ->assertSessionHas('analysis_error', self::FAILURE_MESSAGE);

        Http::assertNothingSent();
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_the_error_is_shown_on_the_page(): void
    {
        $admin = User::factory()->make()->forceFill(['id' => 1, 'role' => 'admin']);

        $this->actingAs($admin)
            ->withSession(['analysis_error' => self::FAILURE_MESSAGE])
            ->get('/analyzeImage')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->where('error', self::FAILURE_MESSAGE)->where('result', null));
    }
}
