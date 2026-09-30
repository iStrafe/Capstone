<?php

namespace Tests\Feature\Resources;

use App\Enums\AdoptionStatus;
use App\Http\Resources\AdoptionRequestResource;
use App\Models\AdoptionRequest;
use App\Models\Cat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdoptionRequestResourceTest extends TestCase
{
    use RefreshDatabase;

    private function createRequest(?Cat $cat, array $overrides = []): AdoptionRequest
    {
        $request = new AdoptionRequest(array_merge([
            'cat_id' => $cat?->id,
            'user_id' => User::factory()->create()->id,
            'name' => 'Juan Dela Cruz',
            'address' => '1 Luna St, Manila',
            'email' => 'juan@example.com',
            'home_phone' => '09171234567',
            'mobile_phone' => '09171234567',
            'valid_id' => ['a1b2c3.jpg', 'd4e5f6.png'],
            'name_of_cat' => 'Mingming',
            'sex' => 'female',
            'date_of_adoption' => '2026-10-20',
        ], $overrides));
        $request->status = AdoptionStatus::Approved;
        $request->approval_date = '2026-10-12 09:30:00';
        $request->save();

        return $request;
    }

    public function test_resource_shape_with_the_cat_loaded(): void
    {
        $cat = Cat::create(['cat_name' => 'Mingming', 'cat_image' => 'ming.png', 'age' => 2, 'color' => 'Orange', 'breed' => 'Puspin', 'sex' => 'Female']);
        $request = $this->createRequest($cat);

        $resolved = (new AdoptionRequestResource(AdoptionRequest::with('cat')->find($request->id)))->resolve();

        $this->assertSame($request->id, $resolved['id']);
        $this->assertSame('Approved', $resolved['status']);
        $this->assertSame('approved', $resolved['statusKey']);
        $this->assertSame('Mingming', $resolved['catName']);
        $this->assertSame([
            'id' => $cat->id,
            'name' => 'Mingming',
            'image' => asset('images/ming.png'),
            'url' => route('cats.show', $cat),
        ], $resolved['cat']);
        $this->assertSame([
            'name' => 'Juan Dela Cruz',
            'email' => 'juan@example.com',
            'phone' => '09171234567',
            'address' => '1 Luna St, Manila',
        ], $resolved['applicant']);
        $this->assertSame('Oct 20, 2026', $resolved['pickupDate']);
        $this->assertSame('Oct 12, 2026', $resolved['approvedAt']);
        $this->assertNull($resolved['releasedAt']);
        $this->assertNotNull($resolved['sentAt']);
        $this->assertSame(2, $resolved['validIdCount']);
    }

    public function test_valid_id_files_are_never_exposed(): void
    {
        $request = $this->createRequest(null);

        $json = json_encode((new AdoptionRequestResource(AdoptionRequest::with('cat')->find($request->id)))->resolve());

        $this->assertStringNotContainsString('a1b2c3', $json);
        $this->assertStringNotContainsString('valid-ids', $json);
        $this->assertStringNotContainsString('valid_id', $json);
    }

    public function test_cat_block_is_null_for_a_deleted_cat_and_absent_when_not_loaded(): void
    {
        $request = $this->createRequest(null, ['valid_id' => []]);

        $loaded = (new AdoptionRequestResource(AdoptionRequest::with('cat')->find($request->id)))->resolve();
        $this->assertArrayHasKey('cat', $loaded);
        $this->assertNull($loaded['cat']);
        $this->assertSame('Mingming', $loaded['catName']);
        $this->assertSame(0, $loaded['validIdCount']);

        $notLoaded = (new AdoptionRequestResource(AdoptionRequest::find($request->id)))->resolve();
        $this->assertArrayNotHasKey('cat', $notLoaded);
    }
}
