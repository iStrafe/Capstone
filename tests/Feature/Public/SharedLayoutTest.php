<?php

namespace Tests\Feature\Public;

use App\Models\Cat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class SharedLayoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_and_about_pages_show_donation_errors(): void
    {
        foreach (['home', 'aboutus'] as $page) {
            $this->withSession(['error' => 'We could not create the payment link.'])
                ->get(route($page))
                ->assertOk()
                ->assertSee('We could not create the payment link.');
        }
    }

    public function test_banner_and_missing_cat_photos_use_the_local_placeholder(): void
    {
        $this->assertFileExists(public_path('images/placeholder.png'));

        Cat::create(['cat_name' => 'Tiger', 'age' => 3, 'color' => 'Black', 'breed' => 'Puspin', 'sex' => 'Male']);

        $home = $this->get(route('home'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->where('cats.0.placeholder', asset('images/placeholder.png')));
        $this->assertStringNotContainsString('fbcdn.net', $home->getContent());

        $this->get(route('adoptCat'))
            ->assertOk()
            ->assertSee(asset('images/placeholder.png'), false)
            ->assertDontSee('default_cat.png', false);

        $dashboard = $this->actingAs(User::factory()->create())->get(route('dashboard'))->assertOk();
        $dashboard->assertSee(asset('images/placeholder.png'), false);
        $this->assertStringNotContainsString('fbcdn.net', $dashboard->getContent());
    }

    public function test_user_dashboard_includes_the_adoption_contract_once(): void
    {
        $content = $this->actingAs(User::factory()->create())
            ->get(route('dashboard'))
            ->assertOk()
            ->getContent();

        $this->assertSame(1, substr_count($content, 'id="modalId2"'));
    }

    public function test_idle_logout_timer_is_only_on_pages_for_signed_in_users(): void
    {
        $this->get(route('aboutus'))
            ->assertOk()
            ->assertDontSee('timerIncrement', false)
            ->assertDontSee('css/styles.css', false);

        $this->actingAs(User::factory()->create())
            ->get(route('aboutus'))
            ->assertOk()
            ->assertSee('timerIncrement', false)
            ->assertSee('id="logout-form"', false);
    }
}
