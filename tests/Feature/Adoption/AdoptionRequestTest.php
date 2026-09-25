<?php

namespace Tests\Feature\Adoption;

use App\Enums\AdoptionStatus;
use App\Models\AdoptionRequest;
use App\Models\Cat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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
        ], $overrides);
    }

    public function test_guests_must_log_in_to_request_an_adoption(): void
    {
        $this->post('/AdoptionForm', $this->validPayload())->assertRedirect(route('login'));

        $this->assertDatabaseCount('adoption_request', 0);
    }

    public function test_adoption_page_asks_guests_to_log_in(): void
    {
        $this->get(route('adoptCat'))->assertOk()->assertSee('Log in to adopt');
    }

    public function test_adoption_request_is_linked_to_the_cat_and_the_applicant(): void
    {
        $response = $this->actingAs($this->user)->post('/AdoptionForm', $this->validPayload());

        $response->assertRedirect(route('myRequest'));
        $response->assertSessionHas('success');

        $request = AdoptionRequest::sole();
        $this->assertSame($this->cat->id, $request->cat_id);
        $this->assertSame($this->user->id, $request->user_id);
        $this->assertSame('juan@example.com', $request->email);
        $this->assertSame(AdoptionStatus::Pending, $request->fresh()->status);
        $this->assertSame([], $request->valid_id);
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
            ->assertSee('Mingming')
            ->assertSee('Pending')
            ->assertDontSee('Tiger');
    }

    public function test_my_requests_needs_a_login(): void
    {
        $this->get(route('myRequest'))->assertRedirect(route('login'));
    }
}
