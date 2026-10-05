<?php

namespace Tests\Feature\Admin;

use App\Models\Cat;
use App\Models\User;
use App\Support\UploadLimit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class CatInventoryTest extends TestCase
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

    private function createCat(array $overrides = []): Cat
    {
        return Cat::create(array_merge([
            'cat_name' => 'Mingming',
            'age' => 2,
            'color' => 'Orange',
            'breed' => 'Puspin',
            'sex' => 'Female',
        ], $overrides));
    }

    private function updatePayload(array $overrides = []): array
    {
        return array_merge([
            'cat_name' => 'Mingming',
            'age' => '3',
            'color' => 'Orange',
            'breed' => 'Puspin',
            'sex' => 'Female',
            'status' => 'Active',
        ], $overrides);
    }

    public function test_inventory_renders_when_there_are_no_cats(): void
    {
        $this->actingAs($this->admin())->get(route('admin.cats.index'))->assertOk();
    }

    public function test_editor_gets_the_cat_with_public_image_and_clip_urls(): void
    {
        $cat = $this->createCat(['cat_clip' => 'clip.mp4', 'cat_image' => 'photo.jpg', 'Medical_Record' => 'Vaccinated', 'age' => 0]);

        $this->actingAs($this->admin())
            ->get(route('admin.cats.edit', $cat))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Cats/Edit')
                ->where('cat.clip', asset('storage/images/clip.mp4'))
                ->where('cat.image', asset('storage/images/photo.jpg'))
                ->where('cat.medicalRecord', 'Vaccinated')
                ->where('cat.ageLabel', 'Under 1 year')
                ->where('cat.updateUrl', route('admin.cats.update', $cat))
                ->where('cat.archiveUrl', route('admin.cats.archive', $cat)));
    }

    public function test_breed_helper_guess_fills_in_a_new_cat(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.cats.create', ['breed' => 'Puspin', 'color' => 'Orange']))
            ->assertInertia(fn (Assert $page) => $page->where('defaults', ['breed' => 'Puspin', 'color' => 'Orange']));
    }

    public function test_an_update_sent_as_a_form_post_with_the_put_override_works(): void
    {
        // The React editor sends files, so it posts with _method=put.
        $cat = $this->createCat();

        $this->actingAs($this->admin())
            ->post(route('admin.cats.update', $cat), $this->updatePayload(['_method' => 'put', 'cat_name' => 'Mingming Jr.', 'status' => 'Inactive']))
            ->assertRedirect(route('admin.cats.index'))
            ->assertSessionHas('success', 'Saved the changes to Mingming Jr..');

        $this->assertSame('Inactive', $cat->fresh()->status);
    }

    public function test_an_archived_cat_can_be_edited_and_stays_archived(): void
    {
        $cat = $this->createCat();
        $cat->forceFill(['archived_at' => now(), 'status' => Cat::STATUS_ARCHIVED])->save();

        $this->actingAs($this->admin())
            ->put(route('admin.cats.update', $cat), Arr::except($this->updatePayload(['color' => 'Calico']), 'status'))
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('admin.cats.index', ['status' => 'archived']));

        $cat->refresh();
        $this->assertSame('Calico', $cat->color);
        $this->assertSame(Cat::STATUS_ARCHIVED, $cat->status);
        $this->assertNotNull($cat->archived_at);
    }

    public function test_updating_a_cat_saves_the_new_video(): void
    {
        $cat = $this->createCat();

        $this->actingAs($this->admin())
            ->put(route('admin.cats.update', $cat), $this->updatePayload([
                'cat_clip' => UploadedFile::fake()->create('clip.mp4', 100, 'video/mp4'),
            ]))
            ->assertRedirect(route('admin.cats.index'));

        $clip = $cat->fresh()->cat_clip;
        $this->assertNotNull($clip);
        Storage::disk('public')->assertExists('images/'.$clip);
    }

    public function test_uploaded_images_get_random_names(): void
    {
        $admin = $this->admin();

        foreach (['One', 'Two'] as $name) {
            $this->actingAs($admin)->post(route('admin.cats.store'), [
                'cat_name' => $name,
                'age' => '2',
                'color' => 'Black',
                'breed' => 'Puspin',
                'sex' => 'Male',
                'cat_image' => UploadedFile::fake()->image('photo.jpg'),
            ])->assertRedirect(route('admin.cats.index'));
        }

        $images = Cat::pluck('cat_image');
        $this->assertCount(2, $images->unique());
        $images->each(fn ($image) => Storage::disk('public')->assertExists('images/'.$image));
    }

    public function test_webp_photos_are_accepted(): void
    {
        $this->actingAs($this->admin())->post(route('admin.cats.store'), [
            'cat_name' => 'Mochi',
            'sex' => 'Female',
            'cat_image' => UploadedFile::fake()->image('mochi.webp'),
        ])->assertRedirect(route('admin.cats.index'));

        $this->assertStringEndsWith('.webp', Cat::sole()->cat_image);
    }

    public function test_photos_over_10_mb_are_refused(): void
    {
        $this->actingAs($this->admin())->post(route('admin.cats.store'), [
            'cat_name' => 'Mochi',
            'sex' => 'Female',
            'cat_image' => UploadedFile::fake()->image('huge.jpg')->size(10241),
        ])->assertSessionHasErrors(['cat_image' => 'The photo can be up to 10 MB.']);

        $this->assertDatabaseCount('cats', 0);
    }

    public function test_a_photo_php_dropped_explains_the_server_limit(): void
    {
        // What PHP hands over when a file is bigger than upload_max_filesize.
        $path = tempnam(sys_get_temp_dir(), 'cat');
        $dropped = new UploadedFile($path, 'phone-photo.jpg', 'image/jpeg', UPLOAD_ERR_INI_SIZE, true);

        $this->actingAs($this->admin())->post(route('admin.cats.store'), [
            'cat_name' => 'Mochi',
            'sex' => 'Female',
            'cat_image' => $dropped,
        ])->assertSessionHasErrors([
            'cat_image' => 'The photo didn’t upload. This server takes files up to '.UploadLimit::label(UploadLimit::perFile()).', set by upload_max_filesize in php.ini.',
        ]);

        @unlink($path);
        $this->assertDatabaseCount('cats', 0);
    }

    public function test_a_photo_php_could_not_save_explains_the_temporary_folder(): void
    {
        // What PHP hands over when it has no writable temporary folder, as on Windows without TEMP.
        $path = tempnam(sys_get_temp_dir(), 'cat');
        $dropped = new UploadedFile($path, 'small.jpg', 'image/jpeg', UPLOAD_ERR_CANT_WRITE, true);

        $this->actingAs($this->admin())->post(route('admin.cats.store'), [
            'cat_name' => 'Mochi',
            'sex' => 'Female',
            'cat_image' => $dropped,
        ])->assertSessionHasErrors([
            'cat_image' => 'The photo didn’t upload because PHP couldn’t save it in its temporary folder. Set upload_tmp_dir in php.ini to a folder PHP can write to.',
        ]);

        @unlink($path);
        $this->assertDatabaseCount('cats', 0);
    }

    public function test_the_editor_gets_the_server_upload_limit(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.cats.create'))
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Cats/Edit')
                ->where('admin.uploadLimit', UploadLimit::toArray()));
    }

    public function test_missing_cats_return_404(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->get(route('admin.cats.show', 999))->assertNotFound();
        $this->actingAs($admin)->get(route('admin.cats.edit', 999))->assertNotFound();
        $this->actingAs($admin)->delete(route('admin.cats.destroy', 999))->assertNotFound();
        $this->get(route('cats.show', 999))->assertNotFound();
    }

    public function test_age_is_optional_and_stored_as_whole_years(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.cats.store'), ['cat_name' => 'NoAge', 'sex' => 'Male'])
            ->assertRedirect(route('admin.cats.index'));
        $this->actingAs($admin)->post(route('admin.cats.store'), ['cat_name' => 'Tom', 'sex' => 'Male', 'age' => '3'])
            ->assertRedirect(route('admin.cats.index'));

        $this->assertNull(Cat::where('cat_name', 'NoAge')->sole()->age);
        $this->assertSame(3, Cat::where('cat_name', 'Tom')->sole()->age);
        $this->assertSame('Active', Cat::where('cat_name', 'Tom')->sole()->fresh()->status);
    }

    public function test_age_must_be_a_whole_number(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.cats.store'), ['cat_name' => 'Kit', 'sex' => 'Female', 'age' => '8 months'])
            ->assertSessionHasErrors('age');

        $this->assertDatabaseCount('cats', 0);
    }
}
