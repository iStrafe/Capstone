<?php

namespace Tests\Feature\Adoption;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class AdoptionRequestTest extends TestCase
{
    use RefreshDatabase;

    /** @var array<int, string>|null */
    private ?array $existingImages = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->existingImages = File::glob(public_path('images/*'));
    }

    protected function tearDown(): void
    {
        // The controller moves uploads straight into public/images, so remove anything a test added.
        // Skip when setUp failed before taking the snapshot, or every existing image would be deleted.
        if ($this->existingImages !== null) {
            File::delete(array_diff(File::glob(public_path('images/*')), $this->existingImages));
        }

        parent::tearDown();
    }

    private function validPayload(array $overrides = []): array
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

    public function test_adoption_form_can_be_rendered(): void
    {
        $this->get('/AdoptionForm')->assertOk();
    }

    public function test_adoption_request_can_be_submitted(): void
    {
        $response = $this->post('/AdoptionForm', $this->validPayload());

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('success');

        $request = DB::table('adoption_request')->first();
        $this->assertNotNull($request);
        $this->assertSame('juan@example.com', $request->email);
        $this->assertSame('Mingming', $request->name_of_cat);
        $this->assertSame('pending', $request->status);
        $this->assertSame([], json_decode($request->valid_id));
        $this->assertNull($request->approval_date);
    }

    public function test_adoption_request_stores_uploaded_valid_ids(): void
    {
        $response = $this->post('/AdoptionForm', $this->validPayload([
            'valid_id' => [UploadedFile::fake()->image('front.jpg'), UploadedFile::fake()->image('back.png')],
        ]));

        $response->assertRedirect(route('home'));

        $validIds = json_decode(DB::table('adoption_request')->value('valid_id'));

        $this->assertCount(2, $validIds);
        foreach ($validIds as $file) {
            $this->assertFileExists(public_path('images/'.$file));
        }
    }

    public function test_adoption_request_requires_name_address_and_email(): void
    {
        $response = $this->post('/AdoptionForm', $this->validPayload([
            'name' => '',
            'address' => '',
            'email' => 'not-an-email',
        ]));

        $response->assertSessionHasErrors(['name', 'address', 'email']);
        $this->assertSame(0, DB::table('adoption_request')->count());
    }

    public function test_valid_id_must_be_an_image(): void
    {
        $response = $this->post('/AdoptionForm', $this->validPayload([
            'valid_id' => [UploadedFile::fake()->create('id.pdf', 10, 'application/pdf')],
        ]));

        $response->assertSessionHasErrors('valid_id.0');
        $this->assertSame(0, DB::table('adoption_request')->count());
    }
}
