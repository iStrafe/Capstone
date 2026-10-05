<?php

namespace Tests\Feature\Console;

use App\Models\Cat;
use App\Models\NewsEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MoveUploadsTest extends TestCase
{
    use RefreshDatabase;

    private string $publicPath;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        Storage::fake('local');

        // A scratch public/ folder holding uploads the way the old code saved them.
        $this->publicPath = sys_get_temp_dir().'/aducats-public-'.uniqid();
        File::ensureDirectoryExists($this->publicPath.'/images/cats');
        File::ensureDirectoryExists($this->publicPath.'/videos');
        $this->app->usePublicPath($this->publicPath);

        foreach (['placeholder.png', 'cat.jpg', 'clip.mp4', 'event.png', '1732401140_id1.png', 'stray.png', 'cats/old.jpg'] as $file) {
            File::put($this->publicPath.'/images/'.$file, 'contents of '.$file);
        }
        File::put($this->publicPath.'/videos/unused.mp4', 'video');
        File::link(storage_path('framework/testing'), $this->publicPath.'/storage');
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->publicPath);

        parent::tearDown();
    }

    private function seedRecords(): void
    {
        Cat::create(['cat_name' => 'Snow', 'age' => 2, 'color' => 'White', 'breed' => 'Puspin', 'sex' => 'Female', 'status' => Cat::STATUS_ACTIVE, 'cat_image' => 'cat.jpg', 'cat_clip' => 'clip.mp4']);
        Cat::create(['cat_name' => 'Old', 'age' => 3, 'color' => 'Grey', 'breed' => 'Puspin', 'sex' => 'Male', 'status' => Cat::STATUS_ARCHIVED, 'cat_image' => 'cats/old.jpg']);
        NewsEvent::create(['title' => 'Drive', 'description' => 'Shots', 'event_date' => '2026-10-10', 'eventimage' => 'event.png']);
        DB::table('adoption_request')->insert([
            'cat_id' => Cat::first()->id,
            'user_id' => User::factory()->create()->id,
            'name' => 'Juan',
            'email' => 'juan@example.com',
            'address' => '1 Luna St, Manila',
            'name_of_cat' => 'Snow',
            'sex' => 'female',
            'date_of_adoption' => now()->addWeek()->toDateString(),
            'valid_id' => json_encode(['1732401140_id1.png']),
            'status' => 'Pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_an_old_request_holding_a_bare_file_name_still_counts_as_an_id(): void
    {
        $this->seedRecords();
        DB::table('adoption_request')->update(['valid_id' => '1732401140_id1.png']);

        $this->artisan('app:move-uploads')->assertSuccessful();

        Storage::disk('local')->assertExists('valid-ids/1732401140_id1.png');
    }

    public function test_uploads_are_sorted_into_public_media_and_private_storage(): void
    {
        $this->seedRecords();

        $this->artisan('app:move-uploads')->assertSuccessful();

        Storage::disk('public')->assertExists(['images/cat.jpg', 'images/clip.mp4', 'images/event.png', 'images/cats/old.jpg']);
        $this->assertSame('contents of cat.jpg', Storage::disk('public')->get('images/cat.jpg'));

        // The applicant ID and files nothing uses are private, never on the public disk.
        Storage::disk('local')->assertExists('valid-ids/1732401140_id1.png');
        Storage::disk('public')->assertMissing('images/1732401140_id1.png');
        Storage::disk('local')->assertExists(['legacy-uploads/images/stray.png', 'legacy-uploads/videos/unused.mp4']);
        Storage::disk('public')->assertMissing('images/stray.png');

        // Only the placeholder stays in public/images.
        $this->assertSame(['placeholder.png'], array_map(fn ($f) => $f->getFilename(), File::allFiles($this->publicPath.'/images')));
        $this->assertDirectoryDoesNotExist($this->publicPath.'/videos');
        $this->assertDirectoryDoesNotExist($this->publicPath.'/images/cats');
    }

    public function test_the_moved_id_is_still_shown_to_admins(): void
    {
        $this->seedRecords();
        $this->artisan('app:move-uploads')->assertSuccessful();

        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->get('/valid-ids/1732401140_id1.png')
            ->assertOk();
    }

    public function test_a_dry_run_moves_nothing(): void
    {
        $this->seedRecords();

        $this->artisan('app:move-uploads', ['--dry-run' => true])->assertSuccessful();

        $this->assertFileExists($this->publicPath.'/images/cat.jpg');
        $this->assertSame([], Storage::disk('public')->allFiles());
        $this->assertSame([], Storage::disk('local')->allFiles());
    }

    public function test_keep_copies_without_removing_the_originals(): void
    {
        $this->seedRecords();

        $this->artisan('app:move-uploads', ['--keep' => true])->assertSuccessful();

        Storage::disk('public')->assertExists('images/cat.jpg');
        $this->assertFileExists($this->publicPath.'/images/cat.jpg');
    }

    public function test_files_can_be_moved_from_a_backup_folder(): void
    {
        $this->seedRecords();
        $backup = $this->publicPath.'/backup';
        File::ensureDirectoryExists($backup);
        File::move($this->publicPath.'/images/cat.jpg', $backup.'/cat.jpg');

        $this->artisan('app:move-uploads', ['--from' => [$backup]])->assertSuccessful();

        Storage::disk('public')->assertExists('images/cat.jpg');
        $this->assertFileExists($this->publicPath.'/images/clip.mp4', 'only the named folder is touched');
    }

    public function test_running_it_twice_is_safe_but_a_clashing_file_is_not_overwritten(): void
    {
        $this->seedRecords();
        Storage::disk('public')->put('images/cat.jpg', 'a different photo');

        $this->artisan('app:move-uploads')->assertFailed();

        $this->assertSame('a different photo', Storage::disk('public')->get('images/cat.jpg'));
        $this->assertFileExists($this->publicPath.'/images/cat.jpg');

        $this->artisan('app:move-uploads')->assertFailed();
        Storage::disk('public')->assertExists('images/clip.mp4');
    }

    public function test_photo_urls_point_at_the_media_disk(): void
    {
        $this->seedRecords();

        $this->get('/')->assertInertia(fn ($page) => $page->where('cats.0.image', asset('storage/images/cat.jpg')));
    }
}
