<?php

namespace Tests\Feature\Admin;

use App\Enums\AdoptionStatus;
use App\Models\AdoptionRequest;
use App\Models\Cat;
use App\Models\Contact;
use App\Models\NewsEvent;
use App\Models\User;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response as ClientResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/**
 * Fixes from the end-to-end test of 2026-10-02: tampered URLs, search wildcards, pages past
 * the end, deleted cats, repeated archive/restore, and uploads left behind.
 */
class AdminHardeningTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        // Uploads go on the media disk; fake it so tests never touch real files.
        Storage::fake('public');
    }

    private function cat(array $overrides = []): Cat
    {
        return Cat::create(array_merge(['cat_name' => 'Mingming', 'age' => 2, 'color' => 'Orange', 'breed' => 'Puspin', 'sex' => 'Female'], $overrides));
    }

    private function request(Cat $cat, AdoptionStatus $status = AdoptionStatus::Pending): int
    {
        return DB::table('adoption_request')->insertGetId([
            'cat_id' => $cat->id,
            'user_id' => User::factory()->create()->id,
            'name' => 'Maria Clara',
            'email' => 'maria@example.com',
            'address' => '1 Luna St, Manila',
            'name_of_cat' => $cat->cat_name,
            'sex' => 'female',
            'date_of_adoption' => now()->addWeek()->toDateString(),
            'valid_id' => json_encode([]),
            'status' => $status->value,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_ids_that_are_not_numbers_are_not_found(): void
    {
        $this->actingAs($this->admin);

        $this->get('/cat/abc')->assertNotFound();
        $this->get('/adminDashboard/cats/abc/edit')->assertNotFound();
        $this->patch('/admin/cats/1.5/archive')->assertNotFound();
        $this->get('/admin/requests/abc')->assertNotFound();
        $this->post('/update-status/abc', ['status' => 'Approved'])->assertNotFound();
        $this->get('/news-events/abc')->assertNotFound();
        $this->delete('/admin/messages/abc')->assertNotFound();
    }

    public function test_array_query_strings_are_ignored(): void
    {
        $this->actingAs($this->admin);

        $this->get('/adminDashboard/cats?q[]=x')->assertOk();
        $this->get('/admin/requests?q[]=x')->assertOk();
        $this->get('/adminDashboard/cats/create?breed[]=x&color[]=y')
            ->assertInertia(fn (Assert $page) => $page->where('defaults', ['breed' => '', 'color' => '']));
    }

    public function test_percent_and_underscore_are_searched_literally(): void
    {
        $this->cat(['cat_name' => 'Mingming']);
        $this->cat(['cat_name' => '100% sweet']);

        $this->actingAs($this->admin)
            ->get('/adminDashboard/cats?q=%25')
            ->assertInertia(fn (Assert $page) => $page->has('cats.data', 1)->where('cats.data.0.name', '100% sweet'));

        $this->get('/adminDashboard/cats?q=_')
            ->assertInertia(fn (Assert $page) => $page->has('cats.data', 0));

        // The escape character itself is searched literally too.
        $this->cat(['cat_name' => 'Hi! Kitty']);
        $this->get('/adminDashboard/cats?q=i!')
            ->assertInertia(fn (Assert $page) => $page->has('cats.data', 1)->where('cats.data.0.name', 'Hi! Kitty'));
        $this->get('/adminDashboard/cats?q=%5C')
            ->assertInertia(fn (Assert $page) => $page->has('cats.data', 0));
    }

    public function test_a_page_past_the_end_goes_to_the_last_page(): void
    {
        $this->cat();
        Contact::create(['full_name' => 'A', 'mobile_number' => '0917', 'message' => 'Hi']);
        $this->actingAs($this->admin);

        $this->get('/adminDashboard/cats?page=99999')->assertRedirect('/adminDashboard/cats?page=1');
        $this->get('/admin/messages?page=99999')->assertRedirect('/admin/messages?page=1');
        $this->get('/adminDashboard/cats?status=archived&page=5')->assertOk();
    }

    public function test_deleting_a_cat_rejects_its_open_requests(): void
    {
        $cat = $this->cat();
        $pending = $this->request($cat);
        $approved = $this->request($cat, AdoptionStatus::Approved);

        $this->actingAs($this->admin)->delete(route('admin.cats.destroy', $cat))->assertRedirect();

        $this->assertSame('Rejected', DB::table('adoption_request')->find($pending)->status);
        $this->assertSame('Rejected', DB::table('adoption_request')->find($approved)->status);
        // And they stay rejected: a request can't be reopened for a cat that's gone.
        $this->post("/update-status/{$pending}", ['status' => 'Pending'])
            ->assertSessionHasErrors(['status' => "Mingming is no longer on the site, so this request can't be reopened."]);
        $this->assertSame('Rejected', DB::table('adoption_request')->find($pending)->status);
    }

    public function test_archiving_twice_or_restoring_an_unarchived_cat_is_refused(): void
    {
        $cat = $this->cat(['status' => Cat::STATUS_INACTIVE]);
        $this->actingAs($this->admin);

        $this->from(route('admin.cats.index'))->patch(route('admin.cats.restore', $cat))
            ->assertSessionHas('error', 'Mingming isn\'t archived.');
        $this->assertSame(Cat::STATUS_INACTIVE, $cat->fresh()->status);

        $this->patch(route('admin.cats.archive', $cat), ['archive_reason' => 'Adopted offline']);
        $archivedAt = $cat->fresh()->archived_at;
        $this->travel(1)->hour();

        $this->from(route('admin.cats.index'))->patch(route('admin.cats.archive', $cat), ['archive_reason' => 'Changed'])
            ->assertSessionHas('error', 'Mingming is already archived.');
        $this->assertSame('Adopted offline', $cat->fresh()->archive_reason);
        $this->assertEquals($archivedAt, $cat->fresh()->archived_at);
    }

    public function test_replaced_and_deleted_uploads_are_removed_from_disk(): void
    {
        $this->actingAs($this->admin)->post(route('admin.cats.store'), [
            'cat_name' => 'Mochi', 'sex' => 'Female', 'cat_image' => UploadedFile::fake()->image('a.jpg'),
        ]);
        $cat = Cat::sole();
        $first = $cat->cat_image;

        $this->post(route('admin.cats.update', $cat), [
            '_method' => 'put', 'cat_name' => 'Mochi', 'sex' => 'Female', 'status' => 'Active', 'cat_image' => UploadedFile::fake()->image('b.jpg'),
        ])->assertSessionHasNoErrors();
        $second = $cat->fresh()->cat_image;

        Storage::disk('public')->assertMissing($first);
        Storage::disk('public')->assertExists($second);

        $this->delete(route('admin.cats.destroy', $cat));
        Storage::disk('public')->assertMissing($second);
    }

    public function test_images_that_came_with_the_site_are_never_deleted(): void
    {
        Storage::disk('public')->put('images/1730882812.png', 'old');
        $cat = $this->cat(['cat_image' => '1730882812.png']);

        $this->actingAs($this->admin)->delete(route('admin.cats.destroy', $cat));

        Storage::disk('public')->assertExists('images/1730882812.png');
    }

    public function test_news_images_are_removed_when_replaced_or_the_post_is_deleted(): void
    {
        $this->actingAs($this->admin)->post(route('news-events.store'), [
            'title' => 'Adoption day', 'description' => 'Meet the cats', 'event_date' => '2026-10-10', 'eventimage' => UploadedFile::fake()->image('a.jpg'),
        ]);
        $post = NewsEvent::sole();
        $first = $post->eventimage;

        Storage::disk('public')->assertExists($first);
        $this->post(route('news-events.update', $post), ['_method' => 'put', 'title' => 'Adoption day', 'description' => 'Meet the cats', 'event_date' => '2026-10-10', 'remove_image' => 1]);
        Storage::disk('public')->assertMissing($first);
    }

    public function test_event_dates_must_be_real_calendar_dates(): void
    {
        $this->actingAs($this->admin);
        $post = fn (string $date) => $this->post(route('news-events.store'), ['title' => 'Drive', 'description' => 'Shots', 'event_date' => $date]);

        $post('99999-01-01')->assertSessionHasErrors(['event_date' => 'Enter a date like 2026-10-31.']);
        $post('10/31/2026')->assertSessionHasErrors('event_date');
        $post('1999-12-31')->assertSessionHasErrors(['event_date' => 'Enter a date in 2000 or later.']);
        $post('2026-10-31')->assertSessionHasNoErrors();
    }

    public function test_event_descriptions_have_a_limit(): void
    {
        $this->actingAs($this->admin)
            ->post(route('news-events.store'), ['title' => 'Drive', 'description' => str_repeat('a', 10001), 'event_date' => '2026-10-31'])
            ->assertSessionHasErrors('description');
    }

    public function test_cat_validation_messages_use_the_editor_labels(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.cats.store'), ['sex' => 'Female', 'Medical_Record' => str_repeat('a', 256)])
            ->assertSessionHasErrors([
                'cat_name' => 'The name field is required.',
                'Medical_Record' => 'The health notes field must not be greater than 255 characters.',
            ]);
    }

    public function test_breed_helper_shows_the_friendly_message_when_a_proxy_refuses(): void
    {
        Storage::fake('public');
        config(['services.openai.key' => 'sk-test-dummy']);
        Http::fake(fn () => throw new RequestException(new ClientResponse(new Response(403))));

        $this->actingAs($this->admin)
            ->from('/analyzeImage')
            ->post('/analyzeImage', ['image' => UploadedFile::fake()->image('cat.png')])
            ->assertRedirect('/analyzeImage')
            ->assertSessionHas('analysis_error');
    }

    public function test_the_pdf_shows_the_request_status_and_is_named_after_the_cat(): void
    {
        $id = $this->request($this->cat());

        $response = $this->actingAs($this->admin)->get(route('adoption-request.pdf', $id));

        $response->assertOk();
        $this->assertStringContainsString('adoption-request-'.$id.'-mingming.pdf', $response->headers->get('content-disposition'));
        $this->assertStringContainsString('Adoption request: Pending', view('adoptionRequestPDF', ['request' => AdoptionRequest::find($id)])->render());
    }

    public function test_status_messages_use_the_right_article(): void
    {
        $id = $this->request($this->cat(), AdoptionStatus::Approved);

        $this->actingAs($this->admin)->post("/update-status/{$id}", ['status' => 'Pending'])
            ->assertSessionHasErrors(['status' => "An approved request can't be marked pending."]);
    }
}
