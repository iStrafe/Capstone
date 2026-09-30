<?php

namespace Tests\Feature\Resources;

use App\Http\Resources\NewsEventResource;
use App\Models\NewsEvent;
use App\Support\Paginated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class PaginatedTest extends TestCase
{
    use RefreshDatabase;

    private function paginateEventsFor(string $uri, int $perPage = 10, string $pageName = 'page'): array
    {
        $this->app->instance('request', Request::create($uri));

        return Paginated::make(NewsEvent::orderBy('id')->paginate($perPage, pageName: $pageName), NewsEventResource::class);
    }

    private function createEvents(int $count): void
    {
        for ($i = 1; $i <= $count; $i++) {
            NewsEvent::create(['title' => 'Event '.$i, 'description' => 'Details', 'event_date' => '2026-10-01']);
        }
    }

    public function test_shape_of_a_middle_page(): void
    {
        $this->createEvents(25);

        $result = $this->paginateEventsFor('http://localhost/events?sort=date&page=2');

        $this->assertSame(['data', 'meta', 'links'], array_keys($result));
        $this->assertCount(10, $result['data']);
        $this->assertSame('Event 11', $result['data'][0]['title']);
        $this->assertSame('Oct 1, 2026', $result['data'][0]['date'], 'items are resolved through the resource');
        $this->assertSame([
            'currentPage' => 2,
            'lastPage' => 3,
            'perPage' => 10,
            'total' => 25,
            'from' => 11,
            'to' => 20,
        ], $result['meta']);

        // Page URLs keep the rest of the query string.
        $this->assertSame('http://localhost/events?sort=date&page=1', $result['links']['prev']);
        $this->assertSame('http://localhost/events?sort=date&page=3', $result['links']['next']);
        $this->assertSame([
            ['url' => 'http://localhost/events?sort=date&page=1', 'label' => '1', 'page' => 1, 'active' => false],
            ['url' => 'http://localhost/events?sort=date&page=2', 'label' => '2', 'page' => 2, 'active' => true],
            ['url' => 'http://localhost/events?sort=date&page=3', 'label' => '3', 'page' => 3, 'active' => false],
        ], $result['links']['pages']);
    }

    public function test_named_page_parameters_keep_other_paginators_state(): void
    {
        $this->createEvents(12);

        $result = $this->paginateEventsFor('http://localhost/AdoptionRequest?page=3&approved_page=2', 5, 'approved_page');

        $this->assertSame(2, $result['meta']['currentPage']);
        $this->assertSame('http://localhost/AdoptionRequest?page=3&approved_page=3', $result['links']['next']);
    }

    public function test_long_page_lists_have_gaps(): void
    {
        $this->createEvents(30);

        $result = $this->paginateEventsFor('http://localhost/events?page=1', 1);

        $labels = array_column($result['links']['pages'], 'label');
        $this->assertContains('...', $labels);
        $this->assertSame('30', end($labels));
        $gap = $result['links']['pages'][array_search('...', $labels, true)];
        $this->assertNull($gap['url']);
        $this->assertNull($gap['page']);
    }

    public function test_empty_results(): void
    {
        $result = $this->paginateEventsFor('http://localhost/events');

        $this->assertSame([], $result['data']);
        $this->assertSame(['currentPage' => 1, 'lastPage' => 1, 'perPage' => 10, 'total' => 0, 'from' => null, 'to' => null], $result['meta']);
        $this->assertNull($result['links']['prev']);
        $this->assertNull($result['links']['next']);
    }
}
