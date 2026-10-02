<?php

namespace Tests\Feature\Admin;

use App\Models\Cat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AdminPagesTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function createAdoptionRequest(array $overrides = []): int
    {
        return DB::table('adoption_request')->insertGetId(array_merge([
            'name' => 'Juan Dela Cruz',
            'email' => 'juan@example.com',
            'address' => '123 Rizal St, Manila',
            'mobile_phone' => '09171234567',
            'name_of_cat' => 'Mingming',
            'sex' => 'female',
            'color' => 'Orange',
            'date_of_adoption' => '2026-10-01',
            'valid_id' => json_encode([]),
            'created_at' => now(),
            'updated_at' => now(),
        ], $overrides));
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

    public static function adminOnlyPages(): array
    {
        return [
            'adoption requests' => ['/admin/requests'],
            'released requests' => ['/admin/requests?status=released'],
        ];
    }

    #[DataProvider('adminOnlyPages')]
    public function test_admin_can_view_admin_pages(string $uri): void
    {
        $this->createAdoptionRequest(['status' => 'Released']);

        $this->actingAs($this->admin())->get($uri)->assertOk();
    }

    public function test_admin_can_view_uploaded_valid_ids(): void
    {
        Storage::fake('local');
        $path = UploadedFile::fake()->image('front.jpg')->store('valid-ids', 'local');
        $id = $this->createAdoptionRequest(['valid_id' => json_encode([basename($path)])]);
        $admin = $this->admin();

        // The old page now redirects to the request, which shows the IDs.
        $this->actingAs($admin)->get('/view-valid-ids/'.$id)->assertRedirect(route('admin.requests.show', $id));
        $this->actingAs($admin)->get(route('admin.requests.show', $id))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Requests/Show')
                ->where('validIds.0.url', route('validIdFile', ['filename' => basename($path)])));

        $this->actingAs($admin)->get('/valid-ids/'.basename($path))->assertOk();
        $this->actingAs($admin)->get('/valid-ids/missing.jpg')->assertNotFound();
    }

    public function test_adoption_requests_page_lists_requests(): void
    {
        $this->createAdoptionRequest(['name' => 'Maria Clara']);

        $this->actingAs($this->admin())
            ->get('/AdoptionRequest')
            ->assertRedirect(route('admin.requests.index'));

        $this->actingAs($this->admin())
            ->get(route('admin.requests.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Requests/Index')
                ->where('requests.data.0.applicant.name', 'Maria Clara')
                ->where('counts.pending', 1));
    }

    public function test_admin_can_approve_a_request(): void
    {
        $id = $this->createAdoptionRequest();

        $this->actingAs($this->admin())
            ->post("/update-status/{$id}", [
                'name' => 'Juan Dela Cruz',
                'address' => '123 Rizal St, Manila',
                'mobile_phone' => '09171234567',
                'name_of_cat' => 'Mingming',
                'status' => 'Approved',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success', 'Approved Juan Dela Cruz to adopt Mingming.');

        $request = DB::table('adoption_request')->find($id);
        $this->assertSame('Approved', $request->status);
        $this->assertNotNull($request->approval_date);
    }

    public function test_admin_can_release_a_request(): void
    {
        $id = $this->createAdoptionRequest(['status' => 'Approved']);

        $this->actingAs($this->admin())
            ->post("/update-status/{$id}", [
                'name' => 'Juan Dela Cruz',
                'address' => '123 Rizal St, Manila',
                'mobile_phone' => '09171234567',
                'name_of_cat' => 'Mingming',
                'status' => 'Released',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success', 'Mingming went home with Juan Dela Cruz.');

        $request = DB::table('adoption_request')->find($id);
        $this->assertSame('Released', $request->status);
        $this->assertNotNull($request->Release_date);
    }

    public function test_admin_cat_list_shows_only_unarchived_cats(): void
    {
        $this->createCat(['cat_name' => 'Mingming']);
        $this->createCat(['cat_name' => 'Muning'])->forceFill(['archived_at' => now()])->save();

        $this->actingAs($this->admin())
            ->get(route('admin.cats.index'))
            ->assertOk()
            ->assertSee('Mingming')
            ->assertDontSee('Muning');
    }

    public function test_direct_create_show_and_edit_cat_urls_redirect_to_the_inventory(): void
    {
        $cat = $this->createCat();
        $admin = $this->admin();

        // These are modals on the index page; the bare fragments are not pages.
        $this->actingAs($admin)->get(route('admin.cats.create'))->assertRedirect(route('admin.cats.index'));
        $this->actingAs($admin)->get(route('admin.cats.show', $cat))->assertRedirect(route('admin.cats.index'));
        $this->actingAs($admin)->get(route('admin.cats.edit', $cat))->assertRedirect(route('admin.cats.index'));
    }

    public function test_admin_can_add_a_cat(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.cats.store'), [
                'cat_name' => 'Garfield',
                'age' => '3',
                'color' => 'Orange',
                'breed' => 'Persian',
                'sex' => 'Male',
            ])
            ->assertRedirect(route('admin.cats.index'));

        $this->assertDatabaseHas('cats', ['cat_name' => 'Garfield', 'status' => 'Active']);
    }

    public function test_admin_can_update_a_cat(): void
    {
        $cat = $this->createCat();

        $this->actingAs($this->admin())
            ->put(route('admin.cats.update', $cat), [
                'cat_name' => 'Renamed',
                'age' => '3',
                'color' => 'Black',
                'breed' => 'Puspin',
                'sex' => 'Female',
                'status' => 'Inactive',
            ])
            ->assertRedirect(route('admin.cats.index'));

        $this->assertDatabaseHas('cats', ['id' => $cat->id, 'cat_name' => 'Renamed', 'status' => 'Inactive']);
    }

    public function test_admin_can_archive_a_cat(): void
    {
        $cat = $this->createCat();
        $admin = $this->admin();

        $this->actingAs($admin)
            ->patch(route('admin.cats.archive', $cat))
            ->assertRedirect(route('admin.cats.index'));

        $cat->refresh();
        $this->assertNotNull($cat->archived_at);
        $this->assertSame('ARCHIVED', $cat->status);

        $this->actingAs($admin)
            ->get(route('admin.cats.archived'))
            ->assertOk()
            ->assertSee($cat->cat_name);
    }
}
