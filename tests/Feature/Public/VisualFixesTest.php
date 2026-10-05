<?php

namespace Tests\Feature\Public;

use App\Models\Cat;
use App\Models\NewsEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class VisualFixesTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_news_post_link_opens_it_in_the_admin_editor(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $event = NewsEvent::create([
            'title' => 'Adoption Day at the Park',
            'description' => 'Meet our cats at the city park.',
            'event_date' => '2026-10-09',
            'eventimage' => 'event.jpg',
        ]);

        $this->actingAs($admin)->get(route('news-events.show', $event->id))->assertRedirect(route('news-events.edit', $event->id));
        $this->actingAs($admin)->get(route('news-events.edit', $event->id))
            ->assertInertia(fn (Assert $page) => $page
                ->where('editing.title', 'Adoption Day at the Park')
                ->where('editing.image', asset('storage/images/event.jpg'))
                ->has('posts.data', 1));
    }

    public function test_login_and_register_are_full_documents_with_a_viewport(): void
    {
        // React pages now; the tab title ("Log in · AduCats") is set in the browser by <Head>.
        $pages = ['login' => 'Auth/Login', 'register' => 'Auth/Register'];

        foreach ($pages as $route => $component) {
            $content = $this->get(route($route))
                ->assertOk()
                ->assertInertia(fn (Assert $page) => $page->component($component))
                ->getContent();

            $this->assertStringStartsWith('<!DOCTYPE html>', ltrim($content));
            $this->assertStringContainsString('<meta name="viewport" content="width=device-width, initial-scale=1">', $content);
            $this->assertStringContainsString('<meta charset="utf-8">', $content);
            $this->assertSame(1, substr_count($content, '<body>'));
            $this->assertStringEndsWith('</html>', rtrim($content));
        }
    }

    public function test_cats_without_a_photo_show_the_generic_placeholder_in_the_gallery(): void
    {
        // 8c2edf…: the old cat photo 1730882812.png, which used to be in public/images.
        $this->assertNotSame(
            '8c2edf85cc8958474af4cb778977818a',
            md5_file(public_path('images/placeholder.png')),
            'placeholder.png must not be a copy of a real cat photo'
        );

        Cat::create(['cat_name' => 'Nophoto', 'age' => 2, 'color' => 'Grey', 'breed' => 'Puspin', 'sex' => 'Male']);

        // React home page: no image URL, so CatPhoto shows the placeholder with "No photo yet of Nophoto".
        $this->get(route('home'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('cats.0.name', 'Nophoto')
                ->where('cats.0.image', null)
                ->where('cats.0.placeholder', asset('images/placeholder.png')));
    }

    public function test_the_breeze_tailwind_3_build_is_gone(): void
    {
        // Log in, register, the password pages and the profile are React pages styled by site.css.
        $vite = file_get_contents(base_path('vite.config.js'));
        $this->assertStringNotContainsString('resources/css/app.css', $vite);
        $this->assertStringNotContainsString('resources/css/profile.css', $vite);
        $this->assertStringNotContainsString('breeze.js', $vite, 'the dead Breeze app layout script is gone');

        foreach (['tailwind.config.js', 'tailwind.profile.config.js', 'resources/css/app.css', 'resources/css/profile.css', 'resources/views/layouts/guest.blade.php', 'resources/views/components', 'resources/views/auth', 'resources/views/profile'] as $path) {
            $this->assertFileDoesNotExist(base_path($path));
        }

        $packages = json_decode(file_get_contents(base_path('package.json')), true)['devDependencies'];
        $this->assertArrayNotHasKey('alpinejs', $packages);
        $this->assertArrayNotHasKey('@tailwindcss/forms', $packages);

        $this->get(route('password.request'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Auth/ForgotPassword'));
    }
}
