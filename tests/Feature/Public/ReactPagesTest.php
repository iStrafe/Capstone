<?php

namespace Tests\Feature\Public;

use App\Models\Cat;
use App\Models\NewsEvent;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/**
 * Pages rebuilt in React and served through Inertia.
 */
class ReactPagesTest extends TestCase
{
    use RefreshDatabase;

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

    public function test_home_page_lists_only_available_cats(): void
    {
        $this->createCat(['cat_name' => 'Awake', 'cat_image' => 'awake.png', 'age' => 0]);
        $this->createCat(['cat_name' => 'Sleepy', 'status' => Cat::STATUS_INACTIVE]);

        $this->get(route('home'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Home')
                ->has('cats', 1)
                ->where('cats.0.name', 'Awake')
                ->where('cats.0.ageLabel', 'Under 1 year')
                ->where('cats.0.image', asset('storage/images/awake.png'))
                ->where('cats.0.url', route('cats.show', Cat::firstWhere('cat_name', 'Awake'))));
    }

    public function test_home_page_shows_the_three_latest_events(): void
    {
        foreach (['2026-01-10', '2026-03-05', '2026-02-01', '2025-12-24'] as $date) {
            NewsEvent::create(['title' => 'Event '.$date, 'description' => 'Details', 'event_date' => $date]);
        }

        $this->get(route('home'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->has('events', 3)
                ->where('events.0.title', 'Event 2026-03-05')
                ->where('events.0.date', 'Mar 5, 2026')
                ->where('events.2.title', 'Event 2026-01-10'));
    }

    public function test_react_pages_share_the_signed_in_user_links_and_flash_messages(): void
    {
        $this->withSession(['error' => 'We could not create the payment link.'])
            ->get(route('home'))
            ->assertInertia(fn (Assert $page) => $page
                ->where('auth.user', null)
                ->where('flash.error', 'We could not create the payment link.')
                ->where('links.adopt', route('adoptCat'))
                ->where('links.donate', route('paymongo.create')));

        $admin = User::factory()->create(['role' => 'admin', 'name' => 'Admin Ana']);

        $this->actingAs($admin)
            ->get(route('home'))
            ->assertInertia(fn (Assert $page) => $page
                ->where('auth.user.name', 'Admin Ana')
                ->where('auth.user.isAdmin', true)
                ->missing('auth.user.email'));
    }

    public function test_donating_from_a_react_page_leaves_for_paymongo(): void
    {
        config(['services.paymongo.secret_key' => 'sk_test_dummy']);
        Http::fake([
            'api.paymongo.com/*' => Http::response(['data' => ['attributes' => ['checkout_url' => 'https://pm.link/aducats/test/abc']]]),
        ]);

        // An Inertia (XHR) visit can't follow a redirect to another site, so it gets a 409 with the target.
        $this->withoutMiddleware(ValidateCsrfToken::class)
            ->from(route('home'))
            ->post(route('paymongo.create'), ['amount' => '250', 'description' => 'Cat food'], ['X-Inertia' => 'true'])
            ->assertStatus(409)
            ->assertHeader('X-Inertia-Location', 'https://pm.link/aducats/test/abc');
    }

    public function test_the_cat_profile_links_to_the_adoption_page_for_that_cat(): void
    {
        $cat = Cat::create(['cat_name' => 'Mingming', 'sex' => 'Female']);

        $this->get(route('cats.show', $cat))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->where('cat.adoptUrl', route('adoption.start', $cat)));

        $page = file_get_contents(resource_path('js/pages/Cats/Show.jsx'));
        $this->assertStringContainsString('href={cat.adoptUrl}', $page);
        $this->assertStringContainsString('${cat.adoptUrl}?new=1', $page);
    }
}
