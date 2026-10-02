<?php

namespace Tests\Feature\Admin;

use App\Enums\AdoptionStatus;
use App\Models\Cat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/**
 * The React admin pages for adoption requests: the tabbed list, the request page, and the
 * rules for which status changes are allowed.
 */
class AdminRequestsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    private function createCat(array $overrides = []): Cat
    {
        return Cat::create(array_merge([
            'cat_name' => 'Mingming',
            'age' => 2,
            'color' => 'Orange',
            'breed' => 'Puspin',
            'sex' => 'Female',
        ], $overrides));
    }

    private function createRequest(Cat $cat, AdoptionStatus $status = AdoptionStatus::Pending, array $overrides = []): int
    {
        return DB::table('adoption_request')->insertGetId(array_merge([
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
        ], $overrides));
    }

    private function setStatus(int $id, string $status)
    {
        return $this->actingAs($this->admin)->from(route('admin.requests.show', $id))->post(route('adoption-request.status', $id), ['status' => $status]);
    }

    private function statusOf(int $id): string
    {
        return DB::table('adoption_request')->where('id', $id)->value('status');
    }

    public function test_list_has_a_tab_per_status_with_counts(): void
    {
        $cat = $this->createCat();
        $this->createRequest($cat, AdoptionStatus::Pending, ['name' => 'Waiting One']);
        $this->createRequest($cat, AdoptionStatus::Pending, ['name' => 'Waiting Two']);
        $this->createRequest($cat, AdoptionStatus::Rejected, ['name' => 'Turned Down']);
        $this->createRequest($this->createCat(['cat_name' => 'Oreo']), AdoptionStatus::Released, ['name' => 'Took Oreo']);

        $this->actingAs($this->admin)->get(route('admin.requests.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Requests/Index')
                ->where('filters', ['status' => 'pending', 'q' => '', 'sort' => 'oldest'])
                ->where('counts', ['pending' => 2, 'approved' => 0, 'rejected' => 1, 'released' => 1, 'all' => 4])
                ->has('requests.data', 2)
                ->where('requests.data.0.catPendingCount', 2)
                ->where('requests.data.0.catPendingLimit', Cat::MAX_PENDING_REQUESTS)
                ->where('requests.data.0.url', fn ($url) => str_contains($url, '/admin/requests/')));

        $this->actingAs($this->admin)->get(route('admin.requests.index', ['status' => 'released']))
            ->assertInertia(fn (Assert $page) => $page
                ->where('filters.sort', 'newest')
                ->has('requests.data', 1)
                ->where('requests.data.0.applicant.name', 'Took Oreo'));

        $this->actingAs($this->admin)->get(route('admin.requests.index', ['status' => 'nonsense']))
            ->assertInertia(fn (Assert $page) => $page->where('filters.status', 'pending'));
    }

    public function test_list_searches_applicant_email_and_cat_without_caring_about_case(): void
    {
        $this->createRequest($this->createCat(), AdoptionStatus::Pending, ['name' => 'Maria Clara']);
        $this->createRequest($this->createCat(['cat_name' => 'Oreo']), AdoptionStatus::Pending, ['name' => 'Jose Rizal', 'email' => 'pepe@example.com']);

        $search = fn (string $q) => $this->actingAs($this->admin)->get(route('admin.requests.index', ['status' => 'all', 'q' => $q]));

        $search('MARIA')->assertInertia(fn (Assert $page) => $page->has('requests.data', 1)->where('requests.data.0.applicant.name', 'Maria Clara'));
        $search('pepe@')->assertInertia(fn (Assert $page) => $page->has('requests.data', 1)->where('requests.data.0.applicant.name', 'Jose Rizal'));
        $search('oreo')->assertInertia(fn (Assert $page) => $page->has('requests.data', 1)->where('requests.data.0.catName', 'Oreo'));
        $search('nobody')->assertInertia(fn (Assert $page) => $page->has('requests.data', 0)->where('filters.q', 'nobody'));
    }

    public function test_old_released_page_redirects_to_the_released_tab(): void
    {
        $this->actingAs($this->admin)->get('/ReleasedRequest')->assertRedirect(route('admin.requests.index', ['status' => 'released']));
    }

    public function test_request_page_shows_the_applicant_ids_and_other_requests(): void
    {
        $cat = $this->createCat();
        $user = User::factory()->create();
        $id = $this->createRequest($cat, AdoptionStatus::Pending, [
            'user_id' => $user->id,
            'valid_id' => json_encode(['valid-ids/front.jpg', 'valid-ids/back.jpg']),
        ]);
        $this->createRequest($cat, AdoptionStatus::Pending, ['name' => 'Jose Rizal']);
        $this->createRequest($this->createCat(['cat_name' => 'Oreo']), AdoptionStatus::Rejected, ['user_id' => $user->id]);

        $this->actingAs($this->admin)->get(route('admin.requests.show', $id))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Requests/Show')
                ->where('request.id', $id)
                ->where('request.applicant.name', 'Maria Clara')
                ->where('validIds', [
                    ['label' => 'ID 1', 'url' => route('validIdFile', ['filename' => 'front.jpg'])],
                    ['label' => 'ID 2', 'url' => route('validIdFile', ['filename' => 'back.jpg'])],
                ])
                ->where('account.otherRequests', 1)
                ->has('otherRequests', 1)
                ->where('otherRequests.0.name', 'Jose Rizal')
                ->where('otherPendingCount', 1)
                ->where('actions', [
                    ['status' => 'Approved', 'refusal' => null],
                    ['status' => 'Rejected', 'refusal' => null],
                ])
                ->where('statusUrl', route('adoption-request.status', $id))
                ->where('listUrl', route('admin.requests.index', ['status' => 'pending'])));
    }

    public function test_request_page_explains_why_a_second_approval_is_blocked(): void
    {
        $cat = $this->createCat();
        $this->createRequest($cat, AdoptionStatus::Approved, ['name' => 'Jose Rizal']);
        $id = $this->createRequest($cat);

        $this->actingAs($this->admin)->get(route('admin.requests.show', $id))
            ->assertInertia(fn (Assert $page) => $page
                ->where('actions.0', ['status' => 'Approved', 'refusal' => 'Mingming already has an approved adopter (Jose Rizal). Reject that request first.'])
                ->where('actions.1', ['status' => 'Rejected', 'refusal' => null]));
    }

    public function test_status_change_redirects_back_with_a_message(): void
    {
        $id = $this->createRequest($this->createCat());

        $this->setStatus($id, 'Approved')
            ->assertRedirect(route('admin.requests.show', $id))
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success', 'Approved Maria Clara to adopt Mingming.');

        $this->assertSame('Approved', $this->statusOf($id));
        $this->assertNotNull(DB::table('adoption_request')->where('id', $id)->value('approval_date'));
    }

    public function test_a_cat_cannot_be_approved_for_two_adopters(): void
    {
        $cat = $this->createCat();
        $this->createRequest($cat, AdoptionStatus::Approved, ['name' => 'Jose Rizal']);
        $id = $this->createRequest($cat);

        $this->setStatus($id, 'Approved')
            ->assertRedirect(route('admin.requests.show', $id))
            ->assertSessionHasErrors(['status' => 'Mingming already has an approved adopter (Jose Rizal). Reject that request first.']);
        $this->assertSame('Pending', $this->statusOf($id));

        // JSON callers get the same answer as a 422.
        $this->actingAs($this->admin)->postJson(route('adoption-request.status', $id), ['status' => 'Approved'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('status');
    }

    public function test_a_released_request_is_final(): void
    {
        $id = $this->createRequest($this->createCat(), AdoptionStatus::Released);

        foreach (['Pending', 'Approved', 'Rejected'] as $status) {
            $this->setStatus($id, $status)->assertSessionHasErrors(['status' => "This cat already went home. A released request can't be changed."]);
        }
        $this->assertSame('Released', $this->statusOf($id));

        $this->actingAs($this->admin)->get(route('admin.requests.show', $id))
            ->assertInertia(fn (Assert $page) => $page->where('actions', []));
    }

    public function test_a_pending_request_must_be_approved_before_it_is_released(): void
    {
        $id = $this->createRequest($this->createCat());

        $this->setStatus($id, 'Released')->assertSessionHasErrors(['status' => "A pending request can't be marked released."]);
        $this->assertSame('Pending', $this->statusOf($id));
    }

    public function test_approving_a_request_for_an_archived_cat_is_blocked(): void
    {
        $cat = $this->createCat();
        $cat->forceFill(['archived_at' => now()])->save();
        $id = $this->createRequest($cat);

        $this->setStatus($id, 'Approved')->assertSessionHasErrors(['status' => 'Mingming is archived. Restore the cat before changing this request.']);

        // Rejecting is always allowed, so old requests can still be closed.
        $this->setStatus($id, 'Rejected')->assertSessionHasNoErrors();
        $this->assertSame('Rejected', $this->statusOf($id));
    }

    public function test_rejecting_an_approved_request_puts_the_cat_back_and_reopening_works(): void
    {
        $cat = $this->createCat();
        $id = $this->createRequest($cat, AdoptionStatus::Approved, ['approval_date' => now()]);

        $this->setStatus($id, 'Rejected')->assertSessionHas('success', "Rejected Maria Clara's request for Mingming.");
        $this->assertTrue(Cat::available()->whereKey($cat->id)->exists());

        $this->setStatus($id, 'Pending')->assertSessionHas('success', "Reopened Maria Clara's request for Mingming. It's pending again.");
        $this->assertSame('Pending', $this->statusOf($id));
        $this->assertNull(DB::table('adoption_request')->where('id', $id)->value('approval_date'));
    }

    public function test_a_request_cannot_be_reopened_after_the_cat_went_home_with_someone_else(): void
    {
        $cat = $this->createCat();
        $this->createRequest($cat, AdoptionStatus::Released, ['name' => 'Jose Rizal']);
        $id = $this->createRequest($cat, AdoptionStatus::Rejected);

        $this->setStatus($id, 'Pending')->assertSessionHasErrors(['status' => 'Mingming already went home with another adopter.']);
        $this->assertSame('Rejected', $this->statusOf($id));
    }

    public function test_a_request_cannot_be_reopened_when_the_applicant_already_has_an_open_one(): void
    {
        $cat = $this->createCat();
        $user = User::factory()->create();
        $this->createRequest($cat, AdoptionStatus::Pending, ['user_id' => $user->id]);
        $id = $this->createRequest($cat, AdoptionStatus::Rejected, ['user_id' => $user->id]);

        $this->setStatus($id, 'Pending')->assertSessionHasErrors(['status' => 'This applicant already has another open request for Mingming.']);
    }

    public function test_admin_layout_gets_the_sidebar_links_and_counts(): void
    {
        $this->createRequest($this->createCat());

        $this->actingAs($this->admin)->get(route('admin.requests.index'))
            ->assertInertia(fn (Assert $page) => $page
                ->where('admin.pendingRequests', 1)
                ->where('admin.unreadMessages', 0)
                ->where('admin.links.requests', route('admin.requests.index'))
                ->where('admin.links.messages', route('admin.messages.index'))
                ->where('admin.links.cats', route('admin.cats.index')));
    }

    public function test_visitors_who_are_not_admins_never_get_the_admin_block(): void
    {
        $this->actingAs(User::factory()->create())->get('/')
            ->assertInertia(fn (Assert $page) => $page->missing('admin'));
    }

    public function test_regular_users_cannot_open_the_request_pages(): void
    {
        $id = $this->createRequest($this->createCat());
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('admin.requests.index'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.requests.show', $id))->assertForbidden();
        $this->actingAs($user)->post(route('adoption-request.status', $id), ['status' => 'Approved'])->assertForbidden();
        $this->assertSame('Pending', $this->statusOf($id));
    }
}
