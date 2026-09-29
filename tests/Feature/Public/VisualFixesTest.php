<?php

namespace Tests\Feature\Public;

use App\Models\Cat;
use App\Models\NewsEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VisualFixesTest extends TestCase
{
    use RefreshDatabase;

    public function test_news_event_page_shows_the_event_once(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $event = NewsEvent::create([
            'title' => 'Adoption Day at the Park',
            'description' => 'Meet our cats at the city park.',
            'event_date' => '2026-10-09',
            'eventimage' => 'event.jpg',
        ]);

        $content = $this->actingAs($admin)
            ->get(route('news-events.show', $event->id))
            ->assertOk()
            ->assertSee('October 9, 2026')
            ->assertSee(route('news-events.index'), false)
            ->getContent();

        $this->assertSame(1, substr_count($content, 'Adoption Day at the Park'));
        $this->assertSame(1, substr_count($content, 'Meet our cats at the city park.'));
        $this->assertSame(1, substr_count($content, 'images/event.jpg'));
    }

    public function test_login_and_register_are_full_documents_with_a_viewport_and_title(): void
    {
        $pages = ['login' => '<title>Log in · AduCats</title>', 'register' => '<title>Register · AduCats</title>'];

        foreach ($pages as $route => $title) {
            $content = $this->get(route($route))->assertOk()->getContent();

            $this->assertStringStartsWith('<!DOCTYPE html>', ltrim($content));
            $this->assertStringContainsString('<meta name="viewport" content="width=device-width, initial-scale=1">', $content);
            $this->assertStringContainsString('<meta charset="utf-8">', $content);
            $this->assertStringContainsString($title, $content);
            $this->assertSame(1, substr_count($content, '<body>'));
            $this->assertStringEndsWith('</html>', rtrim($content));
        }
    }

    public function test_shared_head_no_longer_clears_bootstrap_button_backgrounds(): void
    {
        $partial = file_get_contents(resource_path('views/scripts.blade.php'));

        // The pasted Tailwind block made every [type=submit]/[type=button] transparent, including .btn-primary.
        $this->assertStringNotContainsString('tailwindcss v3.4.1', $partial);
        $this->assertStringNotContainsString('[type=button],[type=reset],[type=submit],button{-webkit-appearance:button;background-color:transparent', $partial);
        $this->assertStringContainsString(':not(.btn, .btn-close:empty)', $partial);

        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('tailwindcss v3.4.1', false);
    }

    public function test_gallery_grid_is_scoped_to_the_cat_gallery(): void
    {
        $partial = file_get_contents(resource_path('views/scripts.blade.php'));
        $this->assertDoesNotMatchRegularExpression('/(^|[\s}])\.row\s*\{[^}]*display:\s*grid/', $partial);

        Cat::create(['cat_name' => 'Mochi', 'cat_image' => 'mochi.png', 'age' => 2, 'color' => 'White', 'breed' => 'Puspin', 'sex' => 'Male']);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('<div class="cat-gallery" id="catGallery">', false)
            ->assertDontSee('col-md-4 cat-card', false);

        $this->actingAs(User::factory()->create())
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('<div class="cat-gallery" id="catGallery">', false)
            ->assertDontSee('col-md-4 cat-card', false);
    }

    public function test_cats_without_a_photo_show_the_generic_placeholder_in_the_gallery(): void
    {
        $this->assertNotSame(
            md5_file(public_path('images/1730882812.png')),
            md5_file(public_path('images/placeholder.png')),
            'placeholder.png must not be a copy of a real cat photo'
        );

        Cat::create(['cat_name' => 'Nophoto', 'age' => 2, 'color' => 'Grey', 'breed' => 'Puspin', 'sex' => 'Male']);

        foreach ([$this->get(route('home')), $this->actingAs(User::factory()->create())->get(route('dashboard'))] as $response) {
            $response->assertOk()
                ->assertSee('alt="No photo yet of Nophoto"', false)
                ->assertDontSee('No image available');
        }
    }

    public function test_breeze_layouts_load_the_tailwind_build_and_not_bootstrap(): void
    {
        $vite = file_get_contents(base_path('vite.config.js'));
        $this->assertStringContainsString("'resources/css/app.css'", $vite);
        $this->assertStringContainsString("'resources/css/profile.css'", $vite);

        foreach (['guest', 'app'] as $layout) {
            $source = file_get_contents(resource_path("views/layouts/{$layout}.blade.php"));
            $this->assertStringContainsString('resources/css/app.css', $source, "layouts/{$layout} must load Tailwind");
            $this->assertStringNotContainsString('resources/sass/app.scss', $source, "layouts/{$layout} must not load Bootstrap");
        }

        $this->assertStringContainsString("@vite('resources/css/profile.css')", file_get_contents(resource_path('views/profile/edit.blade.php')));

        $this->get(route('password.request'))
            ->assertOk()
            ->assertSee('w-20 h-20 fill-current text-gray-500', false);
    }
}
