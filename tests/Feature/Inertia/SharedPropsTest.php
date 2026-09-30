<?php

namespace Tests\Feature\Inertia;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class SharedPropsTest extends TestCase
{
    use RefreshDatabase;

    public function test_flash_status_is_shared(): void
    {
        $this->withSession(['status' => 'profile-updated'])
            ->get(route('home'))
            ->assertInertia(fn (Assert $page) => $page
                ->where('flash.status', 'profile-updated')
                ->where('flash.success', null)
                ->where('flash.error', null));

        $this->flushSession()
            ->get(route('home'))
            ->assertInertia(fn (Assert $page) => $page->where('flash.status', null));
    }

    public function test_admins_get_the_unread_message_count(): void
    {
        Contact::create(['full_name' => 'A', 'mobile_number' => '0917', 'message' => 'Hi']);
        Contact::create(['full_name' => 'B', 'mobile_number' => '0918', 'message' => 'Hello']);
        Contact::create(['full_name' => 'C', 'mobile_number' => '0919', 'message' => 'Done'])
            ->forceFill(['handled_at' => now()])->save();

        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->get(route('home'))
            ->assertInertia(fn (Assert $page) => $page
                ->where('auth.user.isAdmin', true)
                ->where('admin.unreadMessages', 2));
    }

    public function test_guests_and_regular_users_do_not_get_the_admin_block(): void
    {
        Contact::create(['full_name' => 'A', 'mobile_number' => '0917', 'message' => 'Hi']);

        $this->get(route('home'))
            ->assertInertia(fn (Assert $page) => $page->missing('admin'));

        $this->actingAs(User::factory()->create())
            ->get(route('home'))
            ->assertInertia(fn (Assert $page) => $page
                ->where('auth.user.isAdmin', false)
                ->missing('admin'));
    }

    public function test_partial_reloads_can_skip_or_fetch_the_unread_count(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // A partial reload for other props leaves the admin block out.
        $this->actingAs($admin)
            ->get(route('home'))
            ->assertInertia(fn (Assert $page) => $page
                ->reloadOnly('cats', fn (Assert $reload) => $reload->missing('admin'))
                ->reloadOnly('admin', fn (Assert $reload) => $reload->where('admin.unreadMessages', 0)));
    }
}
