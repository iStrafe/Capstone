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
    }

    public function test_blade_pages_include_the_adoption_contract_once(): void
    {
        // The contract modal still comes with the shared Blade navbar until its terms move to the adoption page.
        $content = $this->actingAs(User::factory()->create())
            ->get(route('myRequest'))
            ->assertOk()
            ->getContent();

        $this->assertSame(1, substr_count($content, 'id="modalId2"'));
    }

    public function test_blade_pages_no_longer_log_idle_users_out_after_five_minutes(): void
    {
        // Signing out after inactivity is left to the normal session lifetime (SESSION_LIFETIME).
        $this->get(route('aboutus'))
            ->assertOk()
            ->assertDontSee('timerIncrement', false)
            ->assertDontSee('css/styles.css', false);

        $this->actingAs(User::factory()->create())
            ->get(route('aboutus'))
            ->assertOk()
            ->assertDontSee('timerIncrement', false)
            ->assertDontSee('idleTime', false)
            ->assertSee('id="logout-form"', false);
    }
}
