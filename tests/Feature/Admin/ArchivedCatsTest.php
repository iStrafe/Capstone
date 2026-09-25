<?php

namespace Tests\Feature\Admin;

use App\Enums\AdoptionStatus;
use App\Models\AdoptionRequest;
use App\Models\Cat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Archive with a reason, restore and delete (owner decision 4), and the admin inventory.
 */
class ArchivedCatsTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
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

    private function archived(array $overrides = [], ?string $reason = null): Cat
    {
        $cat = $this->createCat($overrides);
        $cat->forceFill(['archived_at' => now(), 'archive_reason' => $reason, 'status' => Cat::STATUS_ARCHIVED])->save();

        return $cat;
    }

    private function createRequest(Cat $cat, AdoptionStatus $status = AdoptionStatus::Pending): int
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
        ]);
    }

    public function test_archive_stores_the_optional_reason(): void
    {
        $cat = $this->createCat();

        $this->actingAs($this->admin())
            ->patch(route('admin.cats.archive', $cat), ['archive_reason' => 'Moved to a partner shelter'])
            ->assertRedirect(route('admin.cats.index'));

        $cat->refresh();
        $this->assertNotNull($cat->archived_at);
        $this->assertSame('Moved to a partner shelter', $cat->archive_reason);
    }

    public function test_archive_reason_is_at_most_255_characters(): void
    {
        $cat = $this->createCat();

        $this->actingAs($this->admin())
            ->patch(route('admin.cats.archive', $cat), ['archive_reason' => str_repeat('a', 256)])
            ->assertSessionHasErrors('archive_reason');

        $this->assertNull($cat->fresh()->archived_at);
    }

    public function test_inventory_asks_for_a_reason_before_archiving(): void
    {
        $this->createCat();

        $this->actingAs($this->admin())
            ->get(route('admin.cats.index'))
            ->assertOk()
            ->assertSee('data-bs-target="#archiveCatModal"', false)
            ->assertSee('id="archiveCatModal"', false)
            ->assertSee('name="archive_reason"', false)
            ->assertSee(json_encode(route('admin.cats.archive', '__CAT__')), false)
            // The Archive button sits in the card's actions, so it doesn't also open the View popup.
            ->assertSee("closest('.card .actions')", false);
    }

    public function test_inventory_keeps_inactive_and_adopted_cats_and_labels_adoptions(): void
    {
        $this->createCat(['cat_name' => 'Resting', 'status' => Cat::STATUS_INACTIVE]);
        $this->createRequest($this->createCat(['cat_name' => 'Gone Home']), AdoptionStatus::Released);
        $this->createRequest($this->createCat(['cat_name' => 'Almost Home']), AdoptionStatus::Approved);
        $this->archived(['cat_name' => 'Shelved Muning']);

        $this->actingAs($this->admin())
            ->get(route('admin.cats.index'))
            ->assertOk()
            ->assertSee('Resting')
            ->assertSee('Gone Home')
            ->assertSee('Almost Home')
            ->assertSee('>Adopted</span>', false)
            ->assertSee('>Adoption in progress</span>', false)
            ->assertDontSee('Shelved Muning');
    }

    public function test_archived_list_shows_the_reason_and_restore_and_delete_buttons(): void
    {
        $withReason = $this->archived(['cat_name' => 'Muning'], 'Moved to a partner shelter');
        $this->archived(['cat_name' => 'Tiger']);

        $this->actingAs($this->admin())
            ->get(route('admin.cats.archived'))
            ->assertOk()
            ->assertSee('Moved to a partner shelter')
            ->assertSee('No reason given')
            ->assertSee(route('admin.cats.restore', $withReason), false)
            ->assertSee(route('admin.cats.destroy', $withReason), false)
            ->assertSee("onsubmit=\"return confirm('Delete this cat for good?", false);
    }

    public function test_restore_brings_the_cat_back_and_clears_the_reason(): void
    {
        $cat = $this->archived([], 'Temporarily at the vet');

        $this->actingAs($this->admin())
            ->followingRedirects()
            ->patch(route('admin.cats.restore', $cat))
            ->assertOk()
            ->assertSee('Mingming was restored to the cat inventory.');

        $cat->refresh();
        $this->assertNull($cat->archived_at);
        $this->assertNull($cat->archive_reason);
        $this->assertSame(Cat::STATUS_ACTIVE, $cat->status);
        $this->assertTrue($cat->isAvailable());
        $this->get(route('adoptCat'))->assertSee('Mingming');
    }

    public function test_deleting_a_cat_keeps_its_adoption_requests(): void
    {
        $cat = $this->archived();
        $id = $this->createRequest($cat, AdoptionStatus::Rejected);

        $this->actingAs($this->admin())
            ->followingRedirects()
            ->delete(route('admin.cats.destroy', $cat))
            ->assertOk()
            ->assertSee('Mingming was deleted.');

        $this->assertModelMissing($cat);
        $request = AdoptionRequest::findOrFail($id);
        $this->assertNull($request->cat_id);
        $this->assertSame('Mingming', $request->name_of_cat);

        // The admin tables still list it by the saved cat name.
        $this->actingAs($this->admin())->get('/AdoptionRequest')->assertOk()->assertSee('Mingming');
    }
}
