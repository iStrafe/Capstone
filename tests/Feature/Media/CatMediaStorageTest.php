<?php

namespace Tests\Feature\Media;

use App\Models\Cat;
use App\Models\User;
use App\Support\PublicMedia;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use League\Flysystem\UnableToCheckExistence;
use RuntimeException;
use Tests\TestCase;

/**
 * Upload -> database -> storage -> retrieval for cat photos and clips, on the default local
 * media disk. S3 itself is covered by S3MediaTest against an S3-compatible endpoint.
 */
class CatMediaStorageTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    private function payload(array $overrides = []): array
    {
        return array_merge(['cat_name' => 'Mochi', 'sex' => 'Female', 'status' => 'Active'], $overrides);
    }

    private function files(): array
    {
        return Storage::disk('public')->allFiles();
    }

    public function test_a_valid_photo_is_stored_under_the_cats_key_and_shown(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.cats.store'), $this->payload(['cat_image' => UploadedFile::fake()->image('My Cat.JPG', 800, 600)]))
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('admin.cats.index'));

        $cat = Cat::sole();
        $this->assertMatchesRegularExpression('#^cats/'.$cat->id.'/images/[A-Za-z0-9]{40}\.jpg$#', $cat->cat_image);
        $this->assertSame([$cat->cat_image], $this->files());

        $this->get(route('cats.show', $cat))
            ->assertInertia(fn (Assert $page) => $page->where('cat.image', asset('storage/'.$cat->cat_image)));
    }

    public function test_a_file_that_is_not_an_image_is_refused_and_nothing_is_stored(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.cats.store'), $this->payload(['cat_image' => UploadedFile::fake()->create('cat.pdf', 50, 'application/pdf')]))
            ->assertSessionHasErrors('cat_image');

        $this->actingAs($this->admin)
            ->post(route('admin.cats.store'), $this->payload(['cat_image' => UploadedFile::fake()->createWithContent('cat.svg', '<svg xmlns="http://www.w3.org/2000/svg"/>')]))
            ->assertSessionHasErrors('cat_image');

        $this->assertDatabaseCount('cats', 0);
        $this->assertSame([], $this->files());
    }

    public function test_a_photo_over_10_mb_is_refused(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.cats.store'), $this->payload(['cat_image' => UploadedFile::fake()->image('big.jpg')->size(10241)]))
            ->assertSessionHasErrors(['cat_image' => 'The photo can be up to 10 MB.']);

        $this->assertDatabaseCount('cats', 0);
        $this->assertSame([], $this->files());
    }

    public function test_a_valid_clip_is_stored_under_the_videos_key_and_shown(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.cats.store'), $this->payload(['cat_clip' => UploadedFile::fake()->create('clip.mp4', 2048, 'video/mp4')]))
            ->assertSessionHasNoErrors();

        $cat = Cat::sole();
        $this->assertMatchesRegularExpression('#^cats/'.$cat->id.'/videos/[A-Za-z0-9]{40}\.mp4$#', $cat->cat_clip);
        Storage::disk('public')->assertExists($cat->cat_clip);

        $this->get(route('cats.show', $cat))
            ->assertInertia(fn (Assert $page) => $page->where('cat.clip', asset('storage/'.$cat->cat_clip)));
    }

    public function test_a_clip_of_the_wrong_type_is_refused(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.cats.store'), $this->payload(['cat_clip' => UploadedFile::fake()->create('clip.exe', 100, 'application/x-msdownload')]))
            ->assertSessionHasErrors('cat_clip');

        $this->assertDatabaseCount('cats', 0);
        $this->assertSame([], $this->files());
    }

    public function test_a_clip_over_25_mb_is_refused(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.cats.store'), $this->payload(['cat_clip' => UploadedFile::fake()->create('clip.mp4', 25601, 'video/mp4')]))
            ->assertSessionHasErrors(['cat_clip' => 'The clip can be up to 25 MB.']);

        $this->assertDatabaseCount('cats', 0);
        $this->assertSame([], $this->files());
    }

    public function test_editing_details_keeps_the_media_and_replacing_it_deletes_the_old_files(): void
    {
        $this->actingAs($this->admin)->post(route('admin.cats.store'), $this->payload([
            'cat_image' => UploadedFile::fake()->image('a.jpg'),
            'cat_clip' => UploadedFile::fake()->create('a.mp4', 100, 'video/mp4'),
        ]));
        $cat = Cat::sole();
        [$photo, $clip] = [$cat->cat_image, $cat->cat_clip];

        // Details only: same files.
        $this->put(route('admin.cats.update', $cat), $this->payload(['cat_name' => 'Mochi Jr', 'color' => 'White']))->assertSessionHasNoErrors();
        $this->assertSame([$photo, $clip, 'Mochi Jr'], [$cat->fresh()->cat_image, $cat->fresh()->cat_clip, $cat->fresh()->cat_name]);

        // New photo only: the old photo goes, the clip stays.
        $this->put(route('admin.cats.update', $cat), $this->payload(['cat_image' => UploadedFile::fake()->image('b.png')]))->assertSessionHasNoErrors();
        $newPhoto = $cat->fresh()->cat_image;
        $this->assertStringStartsWith('cats/'.$cat->id.'/images/', $newPhoto);
        $this->assertNotSame($photo, $newPhoto);
        $this->assertEqualsCanonicalizing([$newPhoto, $clip], $this->files());

        // Deleting the cat deletes its files.
        $this->delete(route('admin.cats.destroy', $cat))->assertRedirect();
        $this->assertSame([], $this->files());
    }

    public function test_existing_media_from_before_object_keys_is_still_shown(): void
    {
        Storage::disk('public')->put('images/1730882812.png', 'photo');
        Storage::disk('public')->put('images/cats/1727080976.jpg', 'photo');
        $old = Cat::create(['cat_name' => 'Old', 'sex' => 'Male', 'cat_image' => '1730882812.png']);
        $older = Cat::create(['cat_name' => 'Older', 'sex' => 'Male', 'cat_image' => 'cats/1727080976.jpg']);

        $this->get(route('cats.show', $old))->assertInertia(fn (Assert $page) => $page->where('cat.image', asset('storage/images/1730882812.png')));
        // "cats/..." from the early photos is a file name under images/, not an object key.
        $this->get(route('cats.show', $older))->assertInertia(fn (Assert $page) => $page->where('cat.image', asset('storage/images/cats/1727080976.jpg')));
    }

    public function test_a_missing_file_still_renders_and_the_page_falls_back_on_the_placeholder(): void
    {
        $cat = Cat::create(['cat_name' => 'Ghost', 'sex' => 'Male', 'cat_image' => 'cats/1/images/'.str_repeat('a', 40).'.jpg']);

        // The page renders; the browser falls back on the placeholder when the image fails (CatPhoto).
        $this->get(route('cats.show', $cat))->assertOk()
            ->assertInertia(fn (Assert $page) => $page->where('cat.placeholder', asset('images/placeholder.png')));
        $this->assertFalse(PublicMedia::exists($cat->cat_image));

        // Saving the cat again doesn't fail over the missing file.
        $this->actingAs($this->admin)->put(route('admin.cats.update', $cat), $this->payload(['cat_name' => 'Ghost']))->assertSessionHasNoErrors();
        $this->delete(route('admin.cats.destroy', $cat))->assertRedirect();
        $this->assertDatabaseCount('cats', 0);
    }

    public function test_guests_and_non_admins_cannot_upload_or_read_applicant_ids(): void
    {
        $upload = fn () => $this->post(route('admin.cats.store'), $this->payload(['cat_image' => UploadedFile::fake()->image('a.jpg')]));

        $upload()->assertRedirect(route('login'));
        $this->actingAs(User::factory()->create())->post(route('admin.cats.store'), $this->payload(['cat_image' => UploadedFile::fake()->image('a.jpg')]))->assertForbidden();

        $this->assertDatabaseCount('cats', 0);
        $this->assertSame([], $this->files());

        Storage::fake('local')->put('valid-ids/front.jpg', 'id');
        $this->get(route('validIdFile', 'front.jpg'))->assertForbidden();
        $this->actingAs($this->admin)->get(route('validIdFile', 'front.jpg'))->assertOk();
    }

    public function test_when_storage_refuses_a_file_no_row_and_no_file_is_left(): void
    {
        $fake = Storage::disk('public');
        // A disk that takes photos but refuses clips, as S3 does on a permissions or network error.
        Storage::set('public', new class($fake->getDriver(), $fake->getAdapter(), $fake->getConfig()) extends FilesystemAdapter
        {
            public function putFileAs($path, $file, $name = null, $options = [])
            {
                return str_contains($path, '/videos') ? false : parent::putFileAs($path, $file, $name, $options);
            }
        });

        $this->actingAs($this->admin)->post(route('admin.cats.store'), $this->payload([
            'cat_image' => UploadedFile::fake()->image('a.jpg'),
            'cat_clip' => UploadedFile::fake()->create('a.mp4', 100, 'video/mp4'),
        ]))->assertSessionHasErrors(['cat_clip' => 'The clip could not be saved. Please try again.']);

        $this->assertDatabaseCount('cats', 0);
        $this->assertSame([], $this->files());

        // Editing: the record keeps its old files and the new photo is removed again.
        Storage::disk('public')->put('cats/9/images/'.str_repeat('b', 40).'.jpg', 'old');
        $cat = Cat::create(['cat_name' => 'Mochi', 'sex' => 'Female', 'status' => 'Active', 'cat_image' => 'cats/9/images/'.str_repeat('b', 40).'.jpg']);
        $this->put(route('admin.cats.update', $cat), $this->payload([
            'cat_image' => UploadedFile::fake()->image('b.jpg'),
            'cat_clip' => UploadedFile::fake()->create('b.mp4', 100, 'video/mp4'),
        ]))->assertSessionHasErrors('cat_clip');

        $this->assertSame('cats/9/images/'.str_repeat('b', 40).'.jpg', $cat->fresh()->cat_image);
        $this->assertSame(['cats/9/images/'.str_repeat('b', 40).'.jpg'], $this->files());
    }

    public function test_pages_still_render_when_storage_cannot_be_reached(): void
    {
        $fake = Storage::disk('public');
        Storage::set('public', new class($fake->getDriver(), $fake->getAdapter(), $fake->getConfig()) extends FilesystemAdapter
        {
            public function exists($path)
            {
                throw UnableToCheckExistence::forLocation($path);
            }
        });
        $cat = Cat::create(['cat_name' => 'Mochi', 'sex' => 'Female', 'status' => 'Active', 'cat_image' => 'cats/1/images/'.str_repeat('c', 40).'.jpg']);

        // The log in page shows a random cat with a photo, checking that the photo exists.
        $this->get(route('login'))->assertOk()->assertInertia(fn (Assert $page) => $page->where('panelCat', null));
        $this->get(route('cats.show', $cat))->assertOk();
    }

    public function test_signed_urls_never_ask_s3_for_more_than_seven_days(): void
    {
        // Presigning happens locally; no request reaches this endpoint.
        config([
            'filesystems.disks.s3' => array_merge(config('filesystems.disks.s3'), [
                'key' => 'test', 'secret' => 'test', 'region' => 'ap-southeast-1', 'bucket' => 'aducats',
                'endpoint' => 'http://127.0.0.1:1', 'use_path_style_endpoint' => true,
            ]),
            'filesystems.media_disk' => 's3',
            'filesystems.media_url_minutes' => 99999,
        ]);

        $url = PublicMedia::url('cats/1/images/'.str_repeat('d', 40).'.jpg');

        $this->assertStringContainsString('X-Amz-Expires=604800', $url);
    }

    public function test_when_the_database_refuses_the_row_the_stored_files_are_removed(): void
    {
        Cat::updating(fn () => throw new RuntimeException('database refused the row'));

        try {
            $this->withoutExceptionHandling()->actingAs($this->admin)->post(route('admin.cats.store'), $this->payload([
                'cat_image' => UploadedFile::fake()->image('a.jpg'),
            ]));
            $this->fail('The database error should not be swallowed.');
        } catch (RuntimeException $e) {
            $this->assertSame('database refused the row', $e->getMessage());
        }

        $this->assertDatabaseCount('cats', 0);
        $this->assertSame([], $this->files());
    }

    public function test_every_stored_value_is_an_object_key_on_the_disk_and_every_file_has_a_row(): void
    {
        $this->actingAs($this->admin);

        foreach (['One', 'Two', 'Three'] as $name) {
            $this->post(route('admin.cats.store'), $this->payload([
                'cat_name' => $name,
                'cat_image' => UploadedFile::fake()->image($name.'.jpg'),
                'cat_clip' => UploadedFile::fake()->create($name.'.mp4', 100, 'video/mp4'),
            ]))->assertSessionHasNoErrors();
        }

        $two = Cat::where('cat_name', 'Two')->sole();
        $this->put(route('admin.cats.update', $two), $this->payload(['cat_name' => 'Two', 'cat_clip' => UploadedFile::fake()->create('again.mp4', 100, 'video/mp4')]));
        $this->delete(route('admin.cats.destroy', Cat::where('cat_name', 'Three')->sole()));

        $values = Cat::all()->flatMap(fn (Cat $cat) => [$cat->cat_image, $cat->cat_clip])->filter()->values();

        $values->each(fn (string $key) => $this->assertTrue(PublicMedia::isKey($key), $key.' is not an object key'));
        $this->assertEqualsCanonicalizing($values->all(), $this->files());
    }
}
