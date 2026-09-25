<?php

namespace Tests\Feature\Admin;

use App\Models\Cat;
use App\Models\NewsEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
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

    public function test_donate_modal_is_rendered_outside_the_fixed_sidebar(): void
    {
        $html = $this->actingAs($this->admin())->get('/adminDashboard')->assertOk()->getContent();

        $sidebarEnd = strpos($html, '</div>', strpos($html, 'id="sidebar"'));
        $sidebar = substr($html, strpos($html, 'id="sidebar"'), $sidebarEnd - strpos($html, 'id="sidebar"'));

        // The Donate button stays in the sidebar, the modal it opens does not.
        $this->assertStringContainsString('data-bs-target="#modalId"', $html);
        $this->assertStringNotContainsString('id="modalId"', $sidebar);
        $this->assertStringContainsString('id="modalId"', $html);
        // The modal can always be dismissed and no longer builds its own instance.
        $this->assertStringNotContainsString('data-bs-backdrop="static"', $html);
        $this->assertStringNotContainsString('new bootstrap.Modal', $html);
        $this->assertStringContainsString('for="description">Description', $html);
    }

    public function test_admin_logo_uses_an_absolute_url_on_nested_pages(): void
    {
        $this->actingAs($this->admin())
            ->get('/news-events/create')
            ->assertOk()
            ->assertSee('src="'.asset('images/adu logo.png').'"', false);
    }

    public function test_each_cat_card_carries_its_own_details_for_the_view_popup(): void
    {
        $this->createCat(['cat_name' => 'Mingming', 'color' => 'Orange', 'Medical_Record' => 'Vaccinated']);
        $this->createCat(['cat_name' => 'Muning', 'color' => 'Black', 'age' => null, 'cat_image' => 'muning.jpg']);

        $this->actingAs($this->admin())
            ->get(route('admin.cats.index'))
            ->assertOk()
            ->assertSee('data-cat-name="Mingming"', false)
            ->assertSee('data-cat-name="Muning"', false)
            ->assertSee('data-cat-medical-record="Vaccinated"', false)
            ->assertSee('data-cat-age="Unknown"', false)
            ->assertSee('data-cat-image-url="'.asset('images/muning.jpg').'"', false)
            ->assertSee("showCatModal.addEventListener('show.bs.modal'", false)
            // Clicks on Edit/Archive must not also open the View popup.
            ->assertSee("closest('.card .actions')", false);
    }

    public function test_released_page_shows_the_release_date_not_the_approval_date(): void
    {
        $this->createAdoptionRequest([
            'status' => 'Released',
            'approval_date' => '2026-01-01 10:00:00',
            'Release_date' => '2026-02-01 10:00:00',
        ]);

        $this->actingAs($this->admin())
            ->get('/ReleasedRequest')
            ->assertOk()
            ->assertSee('value="2026-02-01"', false)
            ->assertDontSee('value="2026-01-01"', false);
    }

    public function test_adoption_requests_page_sorts_by_request_date_without_missing_helpers(): void
    {
        $this->createAdoptionRequest(['created_at' => '2026-03-04 08:00:00']);

        $this->actingAs($this->admin())
            ->get('/AdoptionRequest')
            ->assertOk()
            ->assertSee('Requested On')
            ->assertSee('data-requested-at="2026-03-04T08:00:00', false)
            ->assertSee('value="2026-03-04"', false)
            ->assertDontSee('displayTable', false);
    }

    public function test_admin_sees_cat_created_message(): void
    {
        $this->withoutPublicImageWrites();

        $this->actingAs($this->admin())
            ->followingRedirects()
            ->post(route('admin.cats.store'), [
                'cat_name' => 'Garfield',
                'sex' => 'Male',
            ])
            ->assertOk()
            ->assertSee('Pet created successfully.');
    }

    public function test_admin_sees_why_a_cat_upload_was_rejected(): void
    {
        $this->withoutPublicImageWrites();
        $svg = UploadedFile::fake()->createWithContent('cat.svg', '<svg xmlns="http://www.w3.org/2000/svg"></svg>');

        $html = $this->actingAs($this->admin())
            ->from(route('admin.cats.index'))
            ->followingRedirects()
            ->post(route('admin.cats.store'), [
                'cat_name' => 'Garfield',
                'sex' => 'Male',
                'cat_image' => $svg,
            ])
            ->assertOk()
            ->getContent();

        // Shown by the flash partial at the top of the page, not only inside the closed Add Cat modal.
        $message = e(__('validation.image', ['attribute' => 'cat image']));
        $flash = strpos($html, 'alert alert-danger');
        $this->assertNotFalse($flash);
        $this->assertLessThan(strpos($html, 'id="addCatModal"'), $flash);
        $this->assertStringContainsString($message, substr($html, $flash, 500));
    }

    public function test_news_event_delete_asks_for_confirmation_and_reports_success(): void
    {
        $event = NewsEvent::create(['title' => 'Adoption day', 'description' => 'Meet the cats', 'event_date' => '2026-10-01']);
        $admin = $this->admin();

        $this->actingAs($admin)
            ->get(route('news-events.index'))
            ->assertOk()
            ->assertSee("onsubmit=\"return confirm('Delete this event?')\"", false);

        $this->actingAs($admin)
            ->followingRedirects()
            ->delete(route('news-events.destroy', $event))
            ->assertOk()
            ->assertSee('News Event deleted successfully.');
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
