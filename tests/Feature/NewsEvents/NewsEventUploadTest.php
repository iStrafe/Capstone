<?php

namespace Tests\Feature\NewsEvents;

use App\Models\NewsEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class NewsEventUploadTest extends TestCase
{
    use RefreshDatabase;

    private string $publicPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->publicPath = sys_get_temp_dir().'/aducats-public-'.uniqid();
        File::ensureDirectoryExists($this->publicPath.'/images');
        $this->app->usePublicPath($this->publicPath);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->publicPath);

        parent::tearDown();
    }

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function event(): NewsEvent
    {
        return NewsEvent::create(['title' => 'Adoption day', 'description' => 'Meet the cats', 'event_date' => '2026-10-01']);
    }

    public function test_creating_an_event_without_a_date_shows_a_validation_error(): void
    {
        $this->actingAs($this->admin())
            ->post(route('news-events.store'), ['title' => 'Adoption day', 'description' => 'Meet the cats'])
            ->assertSessionHasErrors('event_date');

        $this->assertDatabaseCount('news_events', 0);
    }

    public function test_event_image_is_stored_under_a_server_chosen_name(): void
    {
        // A GIF named poster.html passes image validation; keeping the client's extension would publish an HTML page.
        $this->actingAs($this->admin())
            ->post(route('news-events.store'), [
                'title' => 'Adoption day',
                'description' => 'Meet the cats',
                'event_date' => '2026-10-01',
                'eventimage' => UploadedFile::fake()->createWithContent('poster.html', base64_decode('R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw=='))->mimeType('image/gif'),
            ])
            ->assertRedirect(route('news-events.index'));

        $image = NewsEvent::sole()->eventimage;
        $this->assertStringEndsNotWith('.html', $image);
        $this->assertStringEndsWith('.gif', $image);
        $this->assertFileExists($this->publicPath.'/images/'.$image);
    }

    public function test_updating_an_event_rejects_files_that_are_not_images(): void
    {
        $event = $this->event();

        $this->actingAs($this->admin())
            ->put(route('news-events.update', $event), [
                'title' => 'Adoption day',
                'description' => 'Meet the cats',
                'event_date' => '2026-10-01',
                'eventimage' => UploadedFile::fake()->createWithContent('shell.php', '<?php echo 1;'),
            ])
            ->assertSessionHasErrors('eventimage');

        $this->assertNull($event->fresh()->eventimage);
        $this->assertSame([], File::files($this->publicPath.'/images'));
    }

    public function test_updating_a_missing_event_returns_404(): void
    {
        $this->actingAs($this->admin())
            ->put(route('news-events.update', 999), [
                'title' => 'Adoption day',
                'description' => 'Meet the cats',
                'event_date' => '2026-10-01',
            ])
            ->assertNotFound();
    }
}
