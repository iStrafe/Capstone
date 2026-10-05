<?php

namespace Tests\Feature\NewsEvents;

use App\Models\NewsEvent;
use App\Models\User;
use App\Support\UploadLimit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class NewsEventUploadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Uploads go on the media disk; fake it so tests never touch real files.
        Storage::fake('public');
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
        $this->assertStringStartsWith('news/'.NewsEvent::sole()->id.'/images/', $image);
        Storage::disk('public')->assertExists($image);
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
        $this->assertSame([], Storage::disk('public')->allFiles());
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

    public function test_admin_list_opens_the_editor_for_a_new_post(): void
    {
        $this->event();

        $this->actingAs($this->admin())->get(route('news-events.create'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/News')
                ->where('editing', 'new')
                ->where('storeUrl', route('news-events.store'))
                ->has('posts.data', 1));

        $this->actingAs($this->admin())->get(route('news-events.index'))
            ->assertInertia(fn (Assert $page) => $page->where('editing', null));
    }

    public function test_admin_can_publish_a_post(): void
    {
        $this->actingAs($this->admin())
            ->post(route('news-events.store'), ['title' => 'Vaccine drive', 'description' => 'Free shots', 'event_date' => '2026-11-02', 'remove_image' => '0'])
            ->assertRedirect(route('news-events.index'))
            ->assertSessionHas('success', 'Post published. It shows on the public News & events page.');

        $this->assertDatabaseHas('news_events', ['title' => 'Vaccine drive', 'eventimage' => null]);
    }

    public function test_updating_can_remove_the_image(): void
    {
        $event = $this->event();
        $event->forceFill(['eventimage' => 'old.jpg'])->save();

        $this->actingAs($this->admin())
            ->post(route('news-events.update', $event), [
                '_method' => 'put',
                'title' => 'Adoption day',
                'description' => 'Meet the cats',
                'event_date' => '2026-10-01',
                'remove_image' => '1',
            ])
            ->assertRedirect(route('news-events.index'))
            ->assertSessionHas('success', 'Post updated.');

        $this->assertNull($event->fresh()->eventimage);
    }

    public function test_webp_images_are_accepted(): void
    {
        $this->actingAs($this->admin())
            ->post(route('news-events.store'), [
                'title' => 'Adoption day',
                'description' => 'Meet the cats',
                'event_date' => '2026-10-01',
                'eventimage' => UploadedFile::fake()->image('poster.webp'),
            ])
            ->assertSessionHasNoErrors();

        $this->assertStringEndsWith('.webp', NewsEvent::sole()->eventimage);
    }

    public function test_an_image_php_dropped_explains_the_server_limit(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'news');

        $this->actingAs($this->admin())
            ->post(route('news-events.store'), [
                'title' => 'Adoption day',
                'description' => 'Meet the cats',
                'event_date' => '2026-10-01',
                'eventimage' => new UploadedFile($path, 'poster.jpg', 'image/jpeg', UPLOAD_ERR_INI_SIZE, true),
            ])
            ->assertSessionHasErrors([
                'eventimage' => 'The image didn’t upload. This server takes files up to '.UploadLimit::label(UploadLimit::perFile()).', set by upload_max_filesize in php.ini.',
            ]);

        @unlink($path);
        $this->assertDatabaseCount('news_events', 0);
    }
}
