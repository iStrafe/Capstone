<?php

namespace Tests\Feature\Console;

use App\Models\Cat;
use App\Models\NewsEvent;
use App\Support\PublicMedia;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MigrateMediaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        Storage::fake('local');
        // Plays the bucket: MEDIA_DISK=s3 in production.
        Storage::fake('bucket');
        config(['filesystems.media_disk' => 'bucket']);
    }

    private function manifest(): string
    {
        return Storage::disk('local')->files('media-migrations')[0];
    }

    public function test_it_copies_old_files_to_object_keys_and_keeps_the_originals(): void
    {
        Storage::disk('public')->put('images/1730882812.png', 'photo');
        Storage::disk('public')->put('images/1733385022.mp4', 'clip');
        Storage::disk('public')->put('images/cats/1727080976.jpg', 'early photo');
        Storage::disk('public')->put('images/aducat1.jpg', 'shared');
        $cat = Cat::create(['cat_name' => 'Mingming', 'sex' => 'Female', 'cat_image' => '1730882812.png', 'cat_clip' => '1733385022.mp4']);
        $early = Cat::create(['cat_name' => 'Early', 'sex' => 'Male', 'cat_image' => 'cats/1727080976.jpg']);
        $shared = Cat::create(['cat_name' => 'Shared', 'sex' => 'Male', 'cat_image' => 'aducat1.jpg']);
        $post = NewsEvent::create(['title' => 'Drive', 'description' => 'Shots', 'event_date' => '2026-10-10', 'eventimage' => 'aducat1.jpg']);

        $this->artisan('app:migrate-media', ['--from' => 'public'])->assertSuccessful();

        $cat->refresh();
        $this->assertMatchesRegularExpression('#^cats/'.$cat->id.'/images/[A-Za-z0-9]{40}\.png$#', $cat->cat_image);
        $this->assertMatchesRegularExpression('#^cats/'.$cat->id.'/videos/[A-Za-z0-9]{40}\.mp4$#', $cat->cat_clip);
        $this->assertStringStartsWith('cats/'.$early->id.'/images/', $early->fresh()->cat_image);
        $this->assertStringStartsWith('news/'.$post->id.'/images/', $post->fresh()->eventimage);
        $this->assertSame('photo', Storage::disk('bucket')->get($cat->cat_image));
        $this->assertSame('clip', Storage::disk('bucket')->get($cat->cat_clip));
        // A file two records shared gets a copy for each.
        $this->assertSame('shared', Storage::disk('bucket')->get($shared->fresh()->cat_image));
        $this->assertSame('shared', Storage::disk('bucket')->get($post->fresh()->eventimage));

        // Nothing deleted yet.
        $this->assertCount(4, Storage::disk('public')->allFiles());

        // Running it again changes nothing.
        $keys = Cat::pluck('cat_image', 'id')->all();
        $this->artisan('app:migrate-media', ['--from' => 'public'])->assertSuccessful();
        $this->assertSame($keys, Cat::pluck('cat_image', 'id')->all());
    }

    public function test_a_dry_run_changes_nothing(): void
    {
        Storage::disk('public')->put('images/1730882812.png', 'photo');
        Cat::create(['cat_name' => 'Mingming', 'sex' => 'Female', 'cat_image' => '1730882812.png']);

        $this->artisan('app:migrate-media', ['--from' => 'public', '--dry-run' => true])->assertSuccessful();

        $this->assertSame('1730882812.png', Cat::sole()->cat_image);
        $this->assertSame([], Storage::disk('bucket')->allFiles());
        $this->assertSame([], Storage::disk('local')->allFiles());
    }

    public function test_a_record_whose_file_is_missing_is_left_as_it_is(): void
    {
        Cat::create(['cat_name' => 'Ghost', 'sex' => 'Male', 'cat_image' => 'gone.jpg']);

        $this->artisan('app:migrate-media', ['--from' => 'public'])
            ->expectsOutputToContain('Missing: Cat')
            ->assertSuccessful();

        $this->assertSame('gone.jpg', Cat::sole()->cat_image);
    }

    public function test_files_already_on_the_media_disk_are_re_keyed_in_place(): void
    {
        Storage::disk('bucket')->put('images/1730882812.png', 'photo');
        $cat = Cat::create(['cat_name' => 'Mingming', 'sex' => 'Female', 'cat_image' => '1730882812.png']);

        $this->artisan('app:migrate-media')->assertSuccessful();

        $this->assertStringStartsWith('cats/'.$cat->id.'/images/', $cat->fresh()->cat_image);
        Storage::disk('bucket')->assertExists(['images/1730882812.png', $cat->fresh()->cat_image]);
    }

    public function test_revert_points_the_records_back_at_the_old_files(): void
    {
        Storage::disk('public')->put('images/1730882812.png', 'photo');
        $cat = Cat::create(['cat_name' => 'Mingming', 'sex' => 'Female', 'cat_image' => '1730882812.png']);
        $this->artisan('app:migrate-media', ['--from' => 'public'])->assertSuccessful();
        $key = $cat->fresh()->cat_image;

        $this->artisan('app:migrate-media', ['--revert' => $this->manifest()])->assertSuccessful();

        $this->assertSame('1730882812.png', $cat->fresh()->cat_image);
        Storage::disk('bucket')->assertExists($key);
    }

    public function test_prune_deletes_old_files_only_once_nothing_uses_them(): void
    {
        Storage::disk('public')->put('images/1730882812.png', 'photo');
        Storage::disk('public')->put('images/aducat1.jpg', 'shared');
        Cat::create(['cat_name' => 'Mingming', 'sex' => 'Female', 'cat_image' => '1730882812.png']);
        Cat::create(['cat_name' => 'Shared', 'sex' => 'Male', 'cat_image' => 'aducat1.jpg']);
        $this->artisan('app:migrate-media', ['--from' => 'public'])->assertSuccessful();

        // A record added later with the old name (e.g. from a database restore) keeps that file.
        Cat::create(['cat_name' => 'Restored', 'sex' => 'Male', 'cat_image' => 'aducat1.jpg']);

        $this->artisan('app:migrate-media', ['--prune' => $this->manifest()])->assertFailed();

        Storage::disk('public')->assertMissing('images/1730882812.png');
        Storage::disk('public')->assertExists('images/aducat1.jpg');
        Cat::all()->each(fn (Cat $cat) => $this->assertTrue(PublicMedia::exists($cat->cat_image) || Storage::disk('public')->exists('images/'.$cat->cat_image)));
    }

    public function test_an_unknown_manifest_is_refused(): void
    {
        $this->artisan('app:migrate-media', ['--revert' => 'media-migrations/nope.json'])->assertFailed();
    }
}
