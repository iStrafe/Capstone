<?php

namespace Tests\Feature\Resources;

use App\Http\Resources\NewsEventResource;
use App\Models\NewsEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class NewsEventResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_event_date_is_cast_to_a_date(): void
    {
        $event = NewsEvent::create(['title' => 'Adoption day', 'description' => 'Meet the cats', 'event_date' => '2026-10-09']);

        $this->assertInstanceOf(Carbon::class, $event->fresh()->event_date);
        $this->assertSame('2026-10-09', $event->fresh()->event_date->format('Y-m-d'));
    }

    public function test_admin_editor_gets_the_date_for_the_date_input(): void
    {
        $event = NewsEvent::create(['title' => 'Adoption day', 'description' => 'Meet the cats', 'event_date' => '2026-10-09']);
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get(route('news-events.edit', $event->id))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/News')
                ->where('editing.isoDate', '2026-10-09')
                ->where('editing.updateUrl', route('news-events.update', $event->id))
                ->where('posts.data.0.date', 'Oct 9, 2026'));
    }

    public function test_resource_shape(): void
    {
        Carbon::setTestNow('2026-10-09 15:00:00');

        $today = NewsEvent::create(['title' => 'Adoption day', 'description' => 'Meet the cats', 'event_date' => '2026-10-09', 'eventimage' => 'event.jpg']);
        $past = NewsEvent::create(['title' => 'Vaccine drive', 'description' => 'Free shots', 'event_date' => '2026-10-08']);

        $this->assertSame([
            'id' => $today->id,
            'title' => 'Adoption day',
            'description' => 'Meet the cats',
            'date' => 'Oct 9, 2026',
            'isoDate' => '2026-10-09',
            'image' => asset('images/event.jpg'),
            'isUpcoming' => true,
        ], (new NewsEventResource($today->fresh()))->resolve());

        $resolved = (new NewsEventResource($past->fresh()))->resolve();
        $this->assertNull($resolved['image']);
        $this->assertFalse($resolved['isUpcoming']);
    }

    public function test_home_page_events_keep_their_fields_and_gain_the_new_ones(): void
    {
        NewsEvent::create(['title' => 'Adoption day', 'description' => 'Meet the cats', 'event_date' => '2026-10-09', 'eventimage' => 'event.jpg']);

        $this->get(route('home'))
            ->assertInertia(fn (Assert $page) => $page
                ->has('events', 1)
                ->has('events.0', fn (Assert $event) => $event
                    ->whereType('id', 'integer')
                    ->where('title', 'Adoption day')
                    ->where('description', 'Meet the cats')
                    ->where('date', 'Oct 9, 2026')
                    ->where('isoDate', '2026-10-09')
                    ->where('image', asset('images/event.jpg'))
                    ->has('isUpcoming')));
    }
}
