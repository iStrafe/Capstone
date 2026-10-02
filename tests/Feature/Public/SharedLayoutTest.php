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

    public function test_no_page_loads_bootstrap_or_jquery_any_more(): void
    {
        // Every HTML page is React now; the adoption contract PDF is the only Blade view left.
        $this->assertSame(['adoptionRequestPDF.blade.php', 'app.blade.php'], collect(glob(resource_path('views/*')))->map(fn ($path) => basename($path))->sort()->values()->all());

        $content = $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->get('/analyzeImage')
            ->assertOk()
            ->getContent();

        foreach (['bootstrap', 'jquery', 'idleTime'] as $gone) {
            $this->assertStringNotContainsStringIgnoringCase($gone, $content);
        }
    }
}
