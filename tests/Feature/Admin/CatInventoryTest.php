<?php

namespace Tests\Feature\Admin;

use App\Models\Cat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class CatInventoryTest extends TestCase
{
    use RefreshDatabase;

    private string $publicPath;

    protected function setUp(): void
    {
        parent::setUp();

        // Uploads are moved into public/images; point that at a scratch folder so tests never touch real site files.
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
                ->where('cat.clip', asset('images/clip.mp4'))
                ->where('cat.image', asset('images/photo.jpg'))
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
        $this->assertFileExists($this->publicPath.'/images/'.$clip);
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
        $images->each(fn ($image) => $this->assertFileExists($this->publicPath.'/images/'.$image));
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
