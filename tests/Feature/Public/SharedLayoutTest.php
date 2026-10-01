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
            ->assertInertia(fn (Assert $page) => $page->where('cats.0.placeholder', asset('images/placeholder.png')))
            ->assertDontSee('default_cat.png', false);
    }

    public function test_the_adoption_contract_pop_up_is_gone_from_the_blade_navbar(): void
    {
        // Its 12 terms now sit on the adoption request page, where every applicant agrees to them.
        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->get('/analyzeImage')
            ->assertOk()
            ->assertDontSee('id="modalId2"', false);
    }

    public function test_blade_pages_no_longer_log_idle_users_out_after_five_minutes(): void
    {
        // Signing out after inactivity is left to the normal session lifetime (SESSION_LIFETIME).
        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->get('/analyzeImage')
            ->assertOk()
            ->assertDontSee('css/styles.css', false)
            ->assertDontSee('timerIncrement', false)
            ->assertDontSee('idleTime', false)
            ->assertSee('id="logout-form"', false);
    }
}
