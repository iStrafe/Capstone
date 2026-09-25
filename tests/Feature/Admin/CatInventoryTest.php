<?php

namespace Tests\Feature\Admin;

use App\Models\Cat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
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

    public function test_edit_modal_submits_to_the_update_route(): void
    {
        $cat = $this->createCat();

        // The modal is shared by every card, so the script builds the action from this template.
        $this->actingAs($this->admin())
            ->get(route('admin.cats.index'))
            ->assertOk()
            ->assertSee(json_encode(route('admin.cats.update', '__CAT__')), false)
            ->assertDontSee("'/admin/cats/' + catId", false);

        $this->assertNotNull($cat);
    }

    public function test_card_video_uses_the_public_images_url(): void
    {
        $this->createCat(['cat_clip' => 'clip.mp4']);

        $this->actingAs($this->admin())
            ->get(route('admin.cats.index'))
            ->assertSee(asset('images/clip.mp4'), false)
            ->assertDontSee('public\\images', false);
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
}
