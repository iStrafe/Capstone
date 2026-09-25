<?php

namespace Tests\Feature\Public;

use App\Enums\AdoptionStatus;
use App\Models\Cat;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * /cat/{id} explains why a cat is off the adoption list instead of returning 404 (owner decision 4).
 */
class CatStatusPageTest extends TestCase
{
    use RefreshDatabase;

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

    private function archive(Cat $cat, ?string $reason): Cat
    {
        $cat->forceFill(['archived_at' => now(), 'archive_reason' => $reason, 'status' => Cat::STATUS_ARCHIVED])->save();

        return $cat;
    }

    private function createRequest(Cat $cat, AdoptionStatus $status): void
    {
        DB::table('adoption_request')->insert([
            'cat_id' => $cat->id,
            'name' => 'Maria Clara',
            'email' => 'maria@example.com',
            'address' => '1 Luna St, Manila',
            'name_of_cat' => $cat->cat_name,
            'sex' => 'female',
            'date_of_adoption' => now()->addWeek()->toDateString(),
            'valid_id' => json_encode([]),
            'status' => $status->value,
        ]);
    }

    public function test_available_cats_show_their_details(): void
    {
        $cat = $this->createCat(['Medical_Record' => 'Vaccinated']);

        $this->get(route('cats.show', $cat))
            ->assertOk()
            ->assertViewIs('cats.show')
            ->assertSee('Vaccinated');
    }

    public function test_archived_cat_page_explains_why_it_was_archived(): void
    {
        $cat = $this->archive($this->createCat(), 'Moved to a partner shelter in Cebu');

        $this->get(route('cats.show', $cat))
            ->assertOk()
            ->assertViewIs('cats.unavailable')
            ->assertSee('This cat&#039;s profile has been archived', false)
            ->assertSee('Moved to a partner shelter in Cebu')
            ->assertSee('Archived on '.now()->format('M j, Y'))
            ->assertSee(route('adoptCat'), false);
    }

    public function test_archived_cat_page_has_fallback_text_without_a_reason(): void
    {
        $cat = $this->archive($this->createCat(), null);

        $this->get(route('cats.show', $cat))
            ->assertOk()
            ->assertSee('The shelter has not given a reason for archiving this profile.');
    }

    public function test_cat_without_a_photo_uses_the_placeholder_image(): void
    {
        $cat = $this->archive($this->createCat(), null);

        $this->get(route('cats.show', $cat))->assertOk()->assertSee(asset('images/placeholder.png'), false);
    }

    public function test_adopted_reserved_and_inactive_cats_get_their_own_message(): void
    {
        $adopted = $this->createCat(['cat_name' => 'Adopted']);
        $this->createRequest($adopted, AdoptionStatus::Released);
        $reserved = $this->createCat(['cat_name' => 'Reserved']);
        $this->createRequest($reserved, AdoptionStatus::Approved);
        $inactive = $this->createCat(['cat_name' => 'Resting', 'status' => Cat::STATUS_INACTIVE]);

        $this->get(route('cats.show', $adopted))->assertOk()->assertSee('Adopted has been adopted')->assertDontSee('Why:');
        $this->get(route('cats.show', $reserved))->assertOk()->assertSee('Adoption in progress');
        $this->get(route('cats.show', $inactive))->assertOk()->assertSee('Resting is not available right now');
    }

    public function test_a_rejected_request_does_not_make_the_cat_unavailable(): void
    {
        $cat = $this->createCat();
        $this->createRequest($cat, AdoptionStatus::Rejected);
        $this->createRequest($cat, AdoptionStatus::Pending);

        $this->get(route('cats.show', $cat))->assertOk()->assertViewIs('cats.show');
    }
}
