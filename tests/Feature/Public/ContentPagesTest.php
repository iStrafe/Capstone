<?php

namespace Tests\Feature\Public;

use App\Models\Cat;
use App\Models\NewsEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ContentPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_about_page_shows_up_to_three_available_cats_with_photos(): void
    {
        $withPhoto = fn (string $name, array $extra = []) => Cat::create([
            'cat_name' => $name, 'age' => 2, 'color' => 'Orange', 'breed' => 'Puspin', 'sex' => 'Female', 'cat_image' => strtolower($name).'.png', ...$extra,
        ]);

        foreach (['Snow', 'Mochi', 'Oreo', 'Luna'] as $name) {
            $withPhoto($name);
        }
        $withPhoto('Hidden', ['status' => 'Inactive']);
        Cat::create(['cat_name' => 'Nophoto', 'age' => 1, 'color' => 'Black', 'breed' => 'Puspin', 'sex' => 'Male']);

        $this->get(route('aboutus'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('About')
                ->has('photos', 3)
                ->where('photos', fn ($photos) => collect($photos)->every(
                    fn ($cat) => $cat['image'] !== null && ! in_array($cat['name'], ['Hidden', 'Nophoto'])
                )));
    }

    public function test_about_page_works_without_any_cats(): void
    {
        $this->get(route('aboutus'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('About')->has('photos', 0));
    }

    public function test_contact_page_is_a_react_page(): void
    {
        $this->get(route('contactus'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Contact'));
    }

    public function test_events_page_lists_every_post_newest_first(): void
    {
        Carbon::setTestNow('2026-10-14 09:00');

        NewsEvent::create(['title' => 'Feeding day', 'description' => 'Past', 'event_date' => '2026-09-05']);
        NewsEvent::create(['title' => 'Adoption drive', 'description' => 'Soon', 'event_date' => '2026-10-24']);
        NewsEvent::create(['title' => 'Vaccination', 'description' => 'Today', 'event_date' => '2026-10-14']);

        foreach (['news-events.events', 'news-events.index3'] as $route) {
            $this->get(route($route))
                ->assertOk()
                ->assertInertia(fn (Assert $page) => $page
                    ->component('Events')
                    ->has('events', 3)
                    ->where('events.0.title', 'Adoption drive')
                    ->where('events.0.isUpcoming', true)
                    ->where('events.1.title', 'Vaccination')
                    ->where('events.1.isUpcoming', true)
                    ->where('events.2.title', 'Feeding day')
                    ->where('events.2.isUpcoming', false)
                    ->where('events.2.date', 'Sep 5, 2026'));
        }
    }

    public function test_events_page_works_without_any_posts(): void
    {
        $this->get(route('news-events.events'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Events')->has('events', 0));
    }
}
