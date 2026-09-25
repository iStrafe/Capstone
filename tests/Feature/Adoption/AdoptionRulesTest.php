<?php

namespace Tests\Feature\Adoption;

use App\Enums\AdoptionStatus;
use App\Models\AdoptionRequest;
use App\Models\Cat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Which cats can be requested, and by whom (owner decisions 1, 2, 3 and 7).
 */
class AdoptionRulesTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        $this->user = User::factory()->create();
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

    private function payload(Cat $cat, array $overrides = []): array
    {
        return array_merge([
            'cat_id' => $cat->id,
            'name' => 'Juan Dela Cruz',
            'address' => '123 Rizal St, Manila',
            'email' => 'juan@example.com',
            'date_of_adoption' => now()->addWeek()->toDateString(),
        ], $overrides);
    }

    private function createRequest(Cat $cat, AdoptionStatus $status = AdoptionStatus::Pending, ?User $user = null): int
    {
        return DB::table('adoption_request')->insertGetId([
            'cat_id' => $cat->id,
            'user_id' => ($user ?? User::factory()->create())->id,
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

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function assertListedEverywhere(string $name, bool $listed): void
    {
        foreach ([route('adoptCat'), route('home')] as $url) {
            $response = $this->get($url)->assertOk();
            $listed ? $response->assertSee($name) : $response->assertDontSee($name);
        }

        $response = $this->actingAs($this->user)->get(route('dashboard'))->assertOk();
        $listed ? $response->assertSee($name) : $response->assertDontSee($name);
    }

    public function test_inactive_cats_are_hidden_and_cannot_be_requested(): void
    {
        $cat = $this->createCat(['cat_name' => 'Sleepy', 'status' => Cat::STATUS_INACTIVE]);
        $this->createCat(['cat_name' => 'Awake']);

        $this->assertListedEverywhere('Sleepy', false);
        $this->assertListedEverywhere('Awake', true);

        $this->actingAs($this->user)->post('/AdoptionForm', $this->payload($cat))
            ->assertSessionHasErrors(['cat_id' => 'That cat is no longer available for adoption.']);
        $this->assertDatabaseCount('adoption_request', 0);
    }

    public function test_cats_with_an_approved_or_released_request_leave_the_adoption_list(): void
    {
        $approved = $this->createCat(['cat_name' => 'Reserved Cat']);
        $released = $this->createCat(['cat_name' => 'Adopted Cat']);
        $pending = $this->createCat(['cat_name' => 'Waiting Cat']);
        $this->createRequest($approved, AdoptionStatus::Approved);
        $this->createRequest($released, AdoptionStatus::Released);
        $this->createRequest($pending, AdoptionStatus::Pending);

        $this->assertListedEverywhere('Reserved Cat', false);
        $this->assertListedEverywhere('Adopted Cat', false);
        $this->assertListedEverywhere('Waiting Cat', true);

        foreach ([$approved, $released] as $cat) {
            $this->actingAs($this->user)->post('/AdoptionForm', $this->payload($cat))
                ->assertSessionHasErrors(['cat_id' => 'That cat is no longer available for adoption.']);
        }
        $this->assertSame(0, AdoptionRequest::where('user_id', $this->user->id)->count());
    }

    public function test_rejecting_the_approved_request_makes_the_cat_available_again(): void
    {
        $cat = $this->createCat();
        $id = $this->createRequest($cat, AdoptionStatus::Approved);
        $this->assertFalse($cat->isAvailable());

        $this->actingAs($this->admin())->postJson("/update-status/{$id}", ['status' => 'Rejected'])->assertOk();

        $this->assertTrue($cat->isAvailable());
        $this->assertListedEverywhere('Mingming', true);
        $this->actingAs($this->user)->post('/AdoptionForm', $this->payload($cat))->assertRedirect(route('myRequest'));
    }

    public function test_releasing_a_cat_rejects_the_other_pending_requests_for_it(): void
    {
        $cat = $this->createCat();
        $otherCat = $this->createCat(['cat_name' => 'Tiger']);
        $winner = $this->createRequest($cat, AdoptionStatus::Approved);
        $waitingA = $this->createRequest($cat);
        $waitingB = $this->createRequest($cat);
        $alreadyRejected = $this->createRequest($cat, AdoptionStatus::Rejected);
        $otherCatRequest = $this->createRequest($otherCat);

        $this->actingAs($this->admin())
            ->postJson("/update-status/{$winner}", ['status' => 'Released'])
            ->assertOk()
            ->assertJson([
                'success' => true,
                'auto_rejected' => 2,
                'message' => 'Entry updated successfully. The other 2 pending requests for this cat were rejected automatically.',
            ]);

        $this->assertSame(AdoptionStatus::Released, AdoptionRequest::find($winner)->status);
        $this->assertSame(AdoptionStatus::Rejected, AdoptionRequest::find($waitingA)->status);
        $this->assertSame(AdoptionStatus::Rejected, AdoptionRequest::find($waitingB)->status);
        $this->assertSame(AdoptionStatus::Rejected, AdoptionRequest::find($alreadyRejected)->status);
        $this->assertSame(AdoptionStatus::Pending, AdoptionRequest::find($otherCatRequest)->status);
    }

    public function test_approving_does_not_reject_the_other_pending_requests(): void
    {
        $cat = $this->createCat();
        $first = $this->createRequest($cat);
        $second = $this->createRequest($cat);

        $this->actingAs($this->admin())
            ->postJson("/update-status/{$first}", ['status' => 'Approved'])
            ->assertOk()
            ->assertJson(['auto_rejected' => 0, 'message' => 'Entry updated successfully.']);

        $this->assertSame(AdoptionStatus::Pending, AdoptionRequest::find($second)->status);
    }

    public function test_a_user_cannot_send_a_second_open_request_for_the_same_cat(): void
    {
        $cat = $this->createCat();

        $this->actingAs($this->user)->post('/AdoptionForm', $this->payload($cat))->assertRedirect(route('myRequest'));
        $this->actingAs($this->user)->post('/AdoptionForm', $this->payload($cat))
            ->assertSessionHasErrors(['cat_id' => 'You already have an open adoption request for Mingming. You can follow it on My Requests.']);

        $this->assertSame(1, AdoptionRequest::where('user_id', $this->user->id)->count());

        // The listing shows the request is already in.
        $this->actingAs($this->user)->get(route('adoptCat'))->assertOk()->assertSee('Request sent');
    }

    public function test_a_user_can_ask_again_after_a_rejection(): void
    {
        $cat = $this->createCat();
        $this->createRequest($cat, AdoptionStatus::Rejected, $this->user);

        $this->actingAs($this->user)->post('/AdoptionForm', $this->payload($cat))->assertRedirect(route('myRequest'));

        $this->assertSame(2, AdoptionRequest::where('user_id', $this->user->id)->count());
    }

    public function test_a_user_can_request_different_cats_and_several_users_the_same_cat(): void
    {
        $mingming = $this->createCat();
        $tiger = $this->createCat(['cat_name' => 'Tiger']);
        $other = User::factory()->create();

        $this->actingAs($this->user)->post('/AdoptionForm', $this->payload($mingming))->assertRedirect(route('myRequest'));
        $this->actingAs($this->user)->post('/AdoptionForm', $this->payload($tiger))->assertRedirect(route('myRequest'));
        $this->actingAs($other)->post('/AdoptionForm', $this->payload($mingming))->assertRedirect(route('myRequest'));

        $this->assertSame(2, AdoptionRequest::where('cat_id', $mingming->id)->count());
        $this->assertSame(2, AdoptionRequest::where('user_id', $this->user->id)->count());
    }

    public function test_a_cat_takes_at_most_ten_pending_requests(): void
    {
        $cat = $this->createCat();
        for ($i = 0; $i < 9; $i++) {
            $this->createRequest($cat);
        }
        // Decided requests don't count toward the limit.
        $this->createRequest($cat, AdoptionStatus::Rejected);

        $tenth = User::factory()->create();
        $this->actingAs($tenth)->post('/AdoptionForm', $this->payload($cat))->assertRedirect(route('myRequest'));

        $this->actingAs($this->user)->post('/AdoptionForm', $this->payload($cat))
            ->assertSessionHasErrors(['cat_id' => 'Mingming already has 10 adoption requests waiting for a decision. Please choose another cat or try again later.']);

        $this->assertSame(10, AdoptionRequest::where('cat_id', $cat->id)->where('status', AdoptionStatus::Pending)->count());
        $this->actingAs($this->user)->get(route('adoptCat'))->assertOk()->assertSee('Requests full')->assertDontSee('Proceed to Adopt');
    }

    public function test_guests_also_see_when_a_cat_is_full(): void
    {
        $full = $this->createCat(['cat_name' => 'Popular']);
        for ($i = 0; $i < Cat::MAX_PENDING_REQUESTS; $i++) {
            $this->createRequest($full);
        }
        $this->createCat(['cat_name' => 'Quiet']);

        $this->get(route('adoptCat'))
            ->assertOk()
            ->assertSee('Requests full')
            ->assertSee('Log in to adopt');
    }

    public function test_adoption_date_cannot_be_in_the_past(): void
    {
        $cat = $this->createCat();

        $this->actingAs($this->user)->post('/AdoptionForm', $this->payload($cat, ['date_of_adoption' => now()->subDay()->toDateString()]))
            ->assertSessionHasErrors(['date_of_adoption' => 'Choose today or a later date for the adoption.']);
        $this->assertDatabaseCount('adoption_request', 0);

        $this->actingAs($this->user)->post('/AdoptionForm', $this->payload($cat, ['date_of_adoption' => now()->toDateString()]))
            ->assertRedirect(route('myRequest'));
        $this->assertDatabaseCount('adoption_request', 1);
    }

    public function test_date_picker_greys_out_past_days(): void
    {
        $this->createCat();

        $this->actingAs($this->user)->get(route('adoptCat'))
            ->assertOk()
            ->assertSee('name="date_of_adoption" min="'.today()->toDateString().'"', false);
    }
}
