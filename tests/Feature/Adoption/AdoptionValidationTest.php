<?php

namespace Tests\Feature\Adoption;

use App\Enums\AdoptionStatus;
use App\Models\AdoptionRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AdoptionValidationTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function createRequest(array $overrides = []): int
    {
        return DB::table('adoption_request')->insertGetId(array_merge([
            'name' => 'Juan Dela Cruz',
            'email' => 'juan@example.com',
            'address' => '123 Rizal St, Manila',
            'name_of_cat' => 'Mingming',
            'sex' => 'female',
            'color' => 'Orange',
            'date_of_adoption' => '2026-10-01',
            'valid_id' => json_encode([]),
        ], $overrides));
    }

    public function test_status_update_for_a_missing_request_returns_404(): void
    {
        $this->actingAs($this->admin())
            ->postJson('/update-status/999', ['status' => 'Approved'])
            ->assertNotFound();
    }

    public function test_status_update_changes_only_the_status(): void
    {
        $id = $this->createRequest();

        // Extra fields are ignored: admins can't rewrite what the applicant submitted.
        $this->actingAs($this->admin())
            ->postJson("/update-status/{$id}", ['status' => 'Approved', 'name' => 'Changed', 'address' => ''])
            ->assertOk()
            ->assertJson(['success' => true]);

        $request = AdoptionRequest::findOrFail($id);
        $this->assertSame(AdoptionStatus::Approved, $request->status);
        $this->assertSame('Juan Dela Cruz', $request->name);
        $this->assertSame('123 Rizal St, Manila', $request->address);
        $this->assertNotNull($request->approval_date);
    }

    public function test_releasing_a_request_records_the_release_date(): void
    {
        $id = $this->createRequest(['status' => 'Approved']);

        $this->actingAs($this->admin())->postJson("/update-status/{$id}", ['status' => 'Released'])->assertOk();

        $this->assertNotNull(AdoptionRequest::findOrFail($id)->Release_date);
    }

    public function test_status_must_be_one_of_the_known_values(): void
    {
        $id = $this->createRequest();

        foreach (['Not approved', 'approved', 'Anything', ''] as $status) {
            $this->actingAs($this->admin())
                ->postJson("/update-status/{$id}", ['status' => $status])
                ->assertUnprocessable()
                ->assertJsonValidationErrors('status');
        }

        $this->assertDatabaseHas('adoption_request', ['id' => $id, 'status' => 'Pending']);
    }

    public function test_rejected_requests_appear_in_the_rejected_tab(): void
    {
        $id = $this->createRequest();
        $this->actingAs($this->admin())->postJson("/update-status/{$id}", ['status' => 'Rejected'])->assertOk();

        $this->actingAs($this->admin())
            ->get(route('admin.requests.index', ['status' => 'rejected']))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('counts.rejected', 1)
                ->where('counts.approved', 0)
                ->where('requests.data.0.id', $id));
    }

    public function test_pdf_for_a_missing_request_returns_404(): void
    {
        $this->actingAs($this->admin())->get(route('adoption-request.pdf', 999))->assertNotFound();
    }

    public function test_pdf_downloads_for_an_existing_request(): void
    {
        $id = $this->createRequest(['status' => 'Approved', 'approval_date' => now()]);

        $this->actingAs($this->admin())->get(route('adoption-request.pdf', $id))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_request_list_pages_keep_the_tab_and_search(): void
    {
        foreach (range(1, 16) as $i) {
            $this->createRequest(['name' => "Applicant {$i}", 'created_at' => now()->subMinutes(100 - $i)]);
        }

        $this->actingAs($this->admin())
            ->get(route('admin.requests.index', ['status' => 'pending', 'q' => 'applicant']))
            ->assertInertia(fn (Assert $page) => $page
                ->where('requests.meta.total', 16)
                ->where('requests.meta.lastPage', 2)
                ->where('requests.links.next', fn ($url) => str_contains($url, 'status=pending') && str_contains($url, 'q=applicant') && str_contains($url, 'page=2')));
    }
}
