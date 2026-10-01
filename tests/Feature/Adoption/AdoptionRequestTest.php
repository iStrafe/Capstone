<?php

namespace Tests\Feature\Adoption;

use App\Enums\AdoptionStatus;
use App\Models\AdoptionRequest;
use App\Models\Cat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AdoptionRequestTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Cat $cat;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        $this->user = User::factory()->create();
        $this->cat = Cat::create([
            'cat_name' => 'Mingming',
            'age' => 2,
            'color' => 'Orange',
            'breed' => 'Puspin',
            'sex' => 'Female',
        ]);
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'cat_id' => $this->cat->id,
            'name' => 'Juan Dela Cruz',
            'address' => '123 Rizal St, Manila',
            'email' => 'juan@example.com',
            'phone' => '09171234567',
            // Must be today or later, so keep it relative.
            'date_of_adoption' => now()->addWeek()->toDateString(),
            'valid_id' => [UploadedFile::fake()->image('school-id.jpg')],
            'terms' => '1',
        ], $overrides);
    }

    public function test_guests_must_log_in_to_request_an_adoption(): void
    {
        $this->post('/AdoptionForm', $this->validPayload())->assertRedirect(route('login'));

        $this->assertDatabaseCount('adoption_request', 0);
    }

    public function test_guests_who_start_a_request_log_in_and_come_back_to_the_cat(): void
    {
        $this->get(route('adoption.start', $this->cat))->assertRedirect(route('login'));

        $this->post('/login', ['email' => $this->user->email, 'password' => 'password'])
            ->assertRedirect(route('adoption.start', $this->cat));
    }

    public function test_guests_who_choose_to_sign_up_come_back_to_the_cat(): void
    {
        $url = route('adoption.start', ['cat' => $this->cat, 'new' => 1]);
        $this->get($url)->assertRedirect(route('register'));

        $this->post('/register', [
            'name' => 'New Adopter',
            'email' => 'new@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertRedirect($url);

        $this->get($url)->assertOk()->assertInertia(fn (Assert $page) => $page->component('Adoption/Request'));
    }

    public function test_the_request_page_is_filled_in_for_the_cat_and_the_applicant(): void
    {
        $this->actingAs($this->user)->get(route('adoption.start', $this->cat))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Adoption/Request')
                ->where('cat.id', $this->cat->id)
                ->where('cat.name', 'Mingming')
                ->where('applicant.name', $this->user->name)
                ->where('applicant.email', $this->user->email)
                ->where('today', today()->toDateString())
                ->where('links.adoptionSend', route('adoption.request')));
    }

    public function test_cats_that_cannot_take_the_request_send_the_visitor_back_to_their_profile(): void
    {
        $this->actingAs($this->user)->post('/AdoptionForm', $this->validPayload());

        // Already asked: the profile says so and links to My requests.
        $this->actingAs($this->user)->get(route('adoption.start', $this->cat))->assertRedirect(route('cats.show', $this->cat));

        $this->cat->forceFill(['archived_at' => now()])->save();
        $other = User::factory()->create();
        $this->actingAs($other)->get(route('adoption.start', $this->cat))->assertRedirect(route('cats.show', $this->cat));

        $this->actingAs($other)->get('/cat/999/adopt')->assertNotFound();
    }

    public function test_adoption_request_is_linked_to_the_cat_and_the_applicant(): void
    {
        $response = $this->actingAs($this->user)->post('/AdoptionForm', $this->validPayload());

        $response->assertRedirect(route('myRequest'));
        $response->assertSessionHas('success', 'Your request to adopt Mingming was sent. A volunteer will review it and the answer will show up here.');

        $request = AdoptionRequest::sole();
        $this->assertSame($this->cat->id, $request->cat_id);
        $this->assertSame($this->user->id, $request->user_id);
        $this->assertSame('juan@example.com', $request->email);
        $this->assertSame(AdoptionStatus::Pending, $request->fresh()->status);
        $this->assertCount(1, $request->valid_id);
        $this->assertNull($request->approval_date);
    }

    public function test_cat_details_come_from_the_cat_record_not_the_form(): void
    {
        $this->actingAs($this->user)->post('/AdoptionForm', $this->validPayload([
            'name_of_cat' => 'Someone else',
            'sex' => 'Male',
            'approximate_age' => 'two years',
        ]))->assertRedirect(route('myRequest'));

        $this->assertDatabaseHas('adoption_request', [
            'name_of_cat' => 'Mingming',
            'sex' => 'female',
            'approximate_age' => 2,
            'color' => 'Orange',
            'breed' => 'Puspin',
        ]);
    }

    public function test_cats_without_optional_details_can_be_requested(): void
    {
        $cat = Cat::create(['cat_name' => 'Tiger', 'sex' => 'Male']);

        $this->actingAs($this->user)
            ->post('/AdoptionForm', $this->validPayload(['cat_id' => $cat->id]))
            ->assertRedirect(route('myRequest'));

        $this->assertDatabaseHas('adoption_request', ['cat_id' => $cat->id, 'sex' => 'male', 'color' => null]);
    }

    public function test_archived_or_missing_cats_cannot_be_requested(): void
    {
        $this->cat->forceFill(['archived_at' => now()])->save();

        $this->actingAs($this->user)->post('/AdoptionForm', $this->validPayload())->assertSessionHasErrors('cat_id');
        $this->actingAs($this->user)->post('/AdoptionForm', $this->validPayload(['cat_id' => 999]))->assertSessionHasErrors('cat_id');
        $this->actingAs($this->user)->post('/AdoptionForm', $this->validPayload(['cat_id' => null]))->assertSessionHasErrors('cat_id');

        $this->assertDatabaseCount('adoption_request', 0);
    }

    public function test_adoption_request_stores_uploaded_valid_ids(): void
    {
        $this->actingAs($this->user)->post('/AdoptionForm', $this->validPayload([
            'valid_id' => [UploadedFile::fake()->image('front.jpg'), UploadedFile::fake()->image('back.png')],
        ]))->assertRedirect(route('myRequest'));

        $validIds = AdoptionRequest::sole()->valid_id;

        $this->assertCount(2, $validIds);
        foreach ($validIds as $file) {
            Storage::disk('local')->assertExists('valid-ids/'.$file);
            $this->assertFileDoesNotExist(public_path('images/'.$file));
            $this->assertStringNotContainsString('front', $file);
        }
    }

    public function test_adoption_request_requires_applicant_details(): void
    {
        $this->actingAs($this->user)->post('/AdoptionForm', $this->validPayload([
            'name' => '',
            'address' => '',
            'email' => 'not-an-email',
            'date_of_adoption' => '',
        ]))->assertSessionHasErrors(['name', 'address', 'email', 'date_of_adoption']);

        $this->assertDatabaseCount('adoption_request', 0);
    }

    public function test_valid_id_must_be_an_image(): void
    {
        $this->actingAs($this->user)->post('/AdoptionForm', $this->validPayload([
            'valid_id' => [UploadedFile::fake()->create('id.pdf', 10, 'application/pdf')],
        ]))->assertSessionHasErrors('valid_id.0');

        $this->assertDatabaseCount('adoption_request', 0);
    }

    public function test_valid_id_cannot_be_an_svg(): void
    {
        $this->actingAs($this->user)->post('/AdoptionForm', $this->validPayload([
            'valid_id' => [UploadedFile::fake()->createWithContent('id.svg', '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>')],
        ]))->assertSessionHasErrors('valid_id.0');

        $this->assertDatabaseCount('adoption_request', 0);
    }

    public function test_a_valid_id_is_required(): void
    {
        $this->actingAs($this->user)->post('/AdoptionForm', $this->validPayload(['valid_id' => []]))
            ->assertSessionHasErrors(['valid_id' => 'Add a photo of at least one valid ID.']);

        $payload = $this->validPayload();
        unset($payload['valid_id']);
        $this->actingAs($this->user)->post('/AdoptionForm', $payload)->assertSessionHasErrors('valid_id');

        $this->assertDatabaseCount('adoption_request', 0);
    }

    public function test_valid_ids_larger_than_2_mb_are_refused(): void
    {
        $this->actingAs($this->user)->post('/AdoptionForm', $this->validPayload([
            'valid_id' => [UploadedFile::fake()->image('big.jpg')->size(2049)],
        ]))->assertSessionHasErrors(['valid_id.0' => 'Each ID photo must be 2 MB or smaller.']);
    }

    public function test_the_applicant_must_agree_to_the_adoption_terms(): void
    {
        foreach ([null, '0', ''] as $terms) {
            $this->actingAs($this->user)->post('/AdoptionForm', $this->validPayload(['terms' => $terms]))
                ->assertSessionHasErrors(['terms' => 'Please read the adoption terms and tick the box to agree.']);
        }

        $this->assertDatabaseCount('adoption_request', 0);
    }

    public function test_at_most_two_valid_ids(): void
    {
        $this->actingAs($this->user)->post('/AdoptionForm', $this->validPayload([
            'valid_id' => [UploadedFile::fake()->image('a.jpg'), UploadedFile::fake()->image('b.jpg'), UploadedFile::fake()->image('c.jpg')],
        ]))->assertSessionHasErrors('valid_id');
    }

    public function test_applicants_see_only_their_own_requests(): void
    {
        $this->actingAs($this->user)->post('/AdoptionForm', $this->validPayload());

        $other = User::factory()->create();
        $tiger = Cat::create(['cat_name' => 'Tiger', 'sex' => 'Male']);
        $this->actingAs($other)->post('/AdoptionForm', $this->validPayload(['cat_id' => $tiger->id]));

        $this->actingAs($this->user)->get(route('myRequest'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Requests/Mine')
                ->has('requests', 1)
                ->where('requests.0.catName', 'Mingming')
                ->where('requests.0.statusKey', 'pending')
                ->where('requests.0.cat.url', route('cats.show', $this->cat))
                ->where('requests.0.cat.reserved', false)
                ->missing('requests.0.validId'));
    }

    public function test_my_requests_explains_when_another_applicant_was_approved_for_the_cat(): void
    {
        $this->actingAs($this->user)->post('/AdoptionForm', $this->validPayload());

        $winner = User::factory()->create();
        $this->actingAs($winner)->post('/AdoptionForm', $this->validPayload());
        AdoptionRequest::where('user_id', $winner->id)->update(['status' => AdoptionStatus::Approved]);

        $this->actingAs($this->user)->get(route('myRequest'))
            ->assertInertia(fn (Assert $page) => $page
                ->where('requests.0.statusKey', 'pending')
                ->where('requests.0.cat.reserved', true));
    }

    public function test_my_requests_keeps_requests_for_deleted_cats(): void
    {
        $this->actingAs($this->user)->post('/AdoptionForm', $this->validPayload());
        $this->cat->delete();

        $this->actingAs($this->user)->get(route('myRequest'))
            ->assertInertia(fn (Assert $page) => $page
                ->where('requests.0.catName', 'Mingming')
                ->where('requests.0.cat', null));
    }

    public function test_my_requests_needs_a_login(): void
    {
        $this->get(route('myRequest'))->assertRedirect(route('login'));
    }
}
