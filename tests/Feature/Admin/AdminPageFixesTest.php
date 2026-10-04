<?php

namespace Tests\Feature\Admin;

use App\Models\Cat;
use App\Models\NewsEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AdminPageFixesTest extends TestCase
{
    use RefreshDatabase;

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

    private function createAdoptionRequest(array $overrides = []): int
    {
        return DB::table('adoption_request')->insertGetId(array_merge([
            'name' => 'Juan Dela Cruz',
            'email' => 'juan@example.com',
            'address' => '123 Rizal St, Manila',
            'mobile_phone' => '09171234567',
            'name_of_cat' => 'Mingming',
            'sex' => 'female',
            'color' => 'Orange',
            'date_of_adoption' => '2026-10-01',
            'valid_id' => json_encode([]),
            'created_at' => now(),
            'updated_at' => now(),
        ], $overrides));
    }

    public function test_released_tab_shows_the_release_date_not_the_approval_date(): void
    {
        $this->createAdoptionRequest([
            'status' => 'Released',
            'approval_date' => '2026-01-01 10:00:00',
            'Release_date' => '2026-02-01 10:00:00',
        ]);

        $this->actingAs($this->admin())
            ->get(route('admin.requests.index', ['status' => 'released']))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('requests.data.0.releasedAt', 'Feb 1, 2026')
                ->where('requests.data.0.approvedAt', 'Jan 1, 2026'));
    }

    public function test_adoption_requests_sort_by_the_date_they_were_sent(): void
    {
        $this->createAdoptionRequest(['name' => 'Earlier', 'created_at' => '2026-03-04 08:00:00']);
        $this->createAdoptionRequest(['name' => 'Later', 'created_at' => '2026-03-05 08:00:00']);
        $admin = $this->admin();

        // Pending requests are worked first come, first served.
        $this->actingAs($admin)->get(route('admin.requests.index'))
            ->assertInertia(fn (Assert $page) => $page
                ->where('filters.sort', 'oldest')
                ->where('requests.data.0.applicant.name', 'Earlier')
                ->where('requests.data.0.sentAt', 'Mar 4, 2026'));

        $this->actingAs($admin)->get(route('admin.requests.index', ['sort' => 'newest']))
            ->assertInertia(fn (Assert $page) => $page->where('requests.data.0.applicant.name', 'Later'));
    }

    public function test_admin_sees_cat_created_message(): void
    {
        $this->withoutPublicImageWrites();

        $this->actingAs($this->admin())
            ->post(route('admin.cats.store'), [
                'cat_name' => 'Garfield',
                'sex' => 'Male',
            ])
            ->assertRedirect(route('admin.cats.index'))
            ->assertSessionHas('success', 'Garfield was added. The profile is live on the adoption list.');
    }

    public function test_admin_sees_why_a_cat_upload_was_rejected(): void
    {
        $this->withoutPublicImageWrites();
        $svg = UploadedFile::fake()->createWithContent('cat.svg', '<svg xmlns="http://www.w3.org/2000/svg"></svg>');

        // The editor shows the error under the photo field.
        $this->actingAs($this->admin())
            ->from(route('admin.cats.create'))
            ->post(route('admin.cats.store'), [
                'cat_name' => 'Garfield',
                'sex' => 'Male',
                'cat_image' => $svg,
            ])
            ->assertRedirect(route('admin.cats.create'))
            ->assertSessionHasErrors(['cat_image' => __('validation.image', ['attribute' => 'photo'])]);

        $this->assertDatabaseMissing('cats', ['cat_name' => 'Garfield']);
    }

    public function test_news_event_delete_reports_success(): void
    {
        $event = NewsEvent::create(['title' => 'Adoption day', 'description' => 'Meet the cats', 'event_date' => '2026-10-01']);

        $this->actingAs($this->admin())
            ->delete(route('news-events.destroy', $event))
            ->assertRedirect(route('news-events.index'))
            ->assertSessionHas('success', 'Post deleted.');

        $this->assertModelMissing($event);
    }

    // Uploads are moved into public/images; keep tests away from the real folder.
    private function withoutPublicImageWrites(): void
    {
        $path = sys_get_temp_dir().'/aducats-public-'.uniqid();
        File::ensureDirectoryExists($path.'/images');
        $this->app->usePublicPath($path);
        $this->beforeApplicationDestroyed(fn () => File::deleteDirectory($path));
    }
}
