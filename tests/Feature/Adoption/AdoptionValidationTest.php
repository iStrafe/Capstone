<?php

namespace Tests\Feature\Adoption;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdoptionValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Juan Dela Cruz',
            'address' => '123 Rizal St, Manila',
            'email' => 'juan@example.com',
            'phone' => '09171234567',
            'name_of_cat' => 'Mingming',
            'breed' => 'Puspin',
            'approximate_age' => 2,
            'sex' => 'female',
            'color' => 'Orange',
            'date_of_adoption' => '2026-10-01',
        ], $overrides);
    }

    private function createRequest(): int
    {
        return DB::table('adoption_request')->insertGetId([
            'name' => 'Juan Dela Cruz',
            'email' => 'juan@example.com',
            'address' => '123 Rizal St, Manila',
            'name_of_cat' => 'Mingming',
            'sex' => 'female',
            'color' => 'Orange',
            'date_of_adoption' => '2026-10-01',
            'valid_id' => json_encode([]),
        ]);
    }

    public function test_sex_prefilled_from_the_cat_record_is_accepted(): void
    {
        // The Adopt button copies the cat's "Male"/"Female" into the form; the column only takes lowercase.
        $this->post('/AdoptionForm', $this->payload(['sex' => 'Male']))->assertRedirect(route('home'));

        $this->assertDatabaseHas('adoption_request', ['name' => 'Juan Dela Cruz', 'sex' => 'male']);
    }

    public function test_missing_cat_details_show_validation_errors(): void
    {
        $this->post('/AdoptionForm', $this->payload([
            'name_of_cat' => '',
            'sex' => 'unknown',
            'color' => '',
            'date_of_adoption' => '',
            'approximate_age' => 'two years',
        ]))->assertSessionHasErrors(['name_of_cat', 'sex', 'color', 'date_of_adoption', 'approximate_age']);

        $this->assertDatabaseCount('adoption_request', 0);
    }

    public function test_status_update_for_a_missing_request_returns_404(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->postJson('/update-status/999', ['name' => 'A', 'address' => 'B', 'name_of_cat' => 'C', 'status' => 'Approved'])
            ->assertNotFound();
    }

    public function test_status_update_without_applicant_fields_is_rejected_instead_of_blanking_them(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $id = $this->createRequest();

        $this->actingAs($admin)
            ->postJson("/update-status/{$id}", ['status' => 'Approved'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'address', 'name_of_cat']);

        $this->assertDatabaseHas('adoption_request', ['id' => $id, 'name' => 'Juan Dela Cruz']);
    }

    public function test_pdf_for_a_missing_request_returns_404(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get(route('adoption-request.pdf', 999))->assertNotFound();
    }

    public function test_request_tables_page_independently(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get('/AdoptionRequest?approved_page=2')
            ->assertOk()
            ->assertViewHas('adoption_request', fn ($paginator) => $paginator->currentPage() === 1)
            ->assertViewHas('approved_requests', fn ($paginator) => $paginator->currentPage() === 2);
    }
}
