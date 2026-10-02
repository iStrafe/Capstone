<?php

namespace Tests\Feature\Admin;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/**
 * The admin inbox for Contact Us messages (owner decision 5).
 */
class ContactInboxTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function message(array $overrides = []): Contact
    {
        $contact = Contact::create(array_merge([
            'full_name' => 'Juan Dela Cruz',
            'email' => 'juan@example.com',
            'mobile_number' => '09171234567',
            'message' => 'Is Mingming still available?',
        ], $overrides));

        foreach (['created_at', 'handled_at'] as $column) {
            if (array_key_exists($column, $overrides)) {
                $contact->forceFill([$column => $overrides[$column]])->save();
            }
        }

        return $contact;
    }

    public function test_inbox_lists_messages_newest_first_with_their_details(): void
    {
        $this->message(['full_name' => 'Older Sender', 'created_at' => now()->subDays(2)]);
        $newer = $this->message([
            'full_name' => 'Newer Sender',
            'email' => 'newer@example.com',
            'mobile_number' => '09998887777',
            'message' => 'Can I volunteer on weekends?',
            'created_at' => now()->subDay(),
        ]);

        $this->actingAs($this->admin())
            ->get(route('admin.messages.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Messages')
                ->where('filter', 'open')
                ->where('counts.open', 2)
                ->where('messages.data.0.name', 'Newer Sender')
                ->where('messages.data.1.name', 'Older Sender')
                // The newest message opens by default.
                ->where('selected.id', $newer->id)
                ->where('selected.email', 'newer@example.com')
                ->where('selected.phone', '09998887777')
                ->where('selected.message', 'Can I volunteer on weekends?')
                ->where('selected.receivedAtFull', fn ($date) => str_starts_with($date, now()->subDay()->format('M j, Y'))));
    }

    public function test_admin_can_open_a_message_from_the_list(): void
    {
        $first = $this->message(['full_name' => 'First Sender', 'created_at' => now()->subDays(2)]);
        $this->message(['full_name' => 'Second Sender', 'created_at' => now()->subDay()]);

        $this->actingAs($this->admin())
            ->get(route('admin.messages.index', ['message' => $first->id]))
            ->assertInertia(fn (Assert $page) => $page->where('selected.name', 'First Sender'));
    }

    public function test_inbox_is_paginated(): void
    {
        for ($i = 1; $i <= 16; $i++) {
            $this->message(['full_name' => "Sender {$i}", 'created_at' => now()->subMinutes(100 - $i)]);
        }

        $admin = $this->admin();
        $this->actingAs($admin)->get(route('admin.messages.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->has('messages.data', 15)
                ->where('messages.data.0.name', 'Sender 16')
                ->where('messages.meta.total', 16)
                ->where('messages.links.next', fn ($url) => str_contains($url, 'page=2')));

        $this->actingAs($admin)->get(route('admin.messages.index', ['page' => 2]))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->has('messages.data', 1)
                ->where('messages.data.0.name', 'Sender 1'));
    }

    public function test_inbox_filters_handled_messages(): void
    {
        $this->message(['full_name' => 'Waiting']);
        $this->message(['full_name' => 'Answered', 'handled_at' => now()]);
        $admin = $this->admin();

        $this->actingAs($admin)->get(route('admin.messages.index'))
            ->assertInertia(fn (Assert $page) => $page
                ->has('messages.data', 1)
                ->where('messages.data.0.name', 'Waiting')
                ->where('counts', ['open' => 1, 'handled' => 1]));

        $this->actingAs($admin)->get(route('admin.messages.index', ['filter' => 'handled']))
            ->assertInertia(fn (Assert $page) => $page
                ->has('messages.data', 1)
                ->where('messages.data.0.name', 'Answered')
                ->where('messages.data.0.handled', true));

        $this->actingAs($admin)->get(route('admin.messages.index', ['filter' => 'all']))
            ->assertInertia(fn (Assert $page) => $page->has('messages.data', 2));
    }

    public function test_inbox_shows_when_no_email_was_given(): void
    {
        $this->message(['email' => null]);

        $this->actingAs($this->admin())->get(route('admin.messages.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->where('selected.email', null));
    }

    public function test_admin_can_mark_a_message_handled_and_back(): void
    {
        $contact = $this->message();
        $admin = $this->admin();

        $this->actingAs($admin)
            ->from(route('admin.messages.index'))
            ->patch(route('admin.messages.handled', $contact), ['handled' => 1])
            ->assertRedirect(route('admin.messages.index', ['message' => $contact->id]))
            ->assertSessionHas('success', 'Message marked as handled.');
        $this->assertNotNull($contact->fresh()->handled_at);

        $this->actingAs($admin)->get(route('admin.messages.index', ['message' => $contact->id]))
            ->assertInertia(fn (Assert $page) => $page
                ->where('counts.open', 0)
                ->has('messages.data', 0)
                // It stays open after leaving the "to handle" list.
                ->where('selected.handled', true)
                ->whereType('selected.handledAt', 'string'));

        $this->actingAs($admin)
            ->from(route('admin.messages.index', ['filter' => 'handled', 'page' => 1]))
            ->patch(route('admin.messages.handled', $contact), ['handled' => 0])
            ->assertRedirect(route('admin.messages.index', ['message' => $contact->id, 'filter' => 'handled', 'page' => 1]))
            ->assertSessionHas('success', 'Message moved back to unhandled.');
        $this->assertNull($contact->fresh()->handled_at);
    }

    public function test_admin_can_delete_a_message(): void
    {
        $contact = $this->message();

        $this->actingAs($this->admin())
            ->from(route('admin.messages.index', ['filter' => 'all', 'message' => $contact->id]))
            ->delete(route('admin.messages.destroy', $contact).'?filter=all')
            ->assertRedirect(route('admin.messages.index', ['filter' => 'all']))
            ->assertSessionHas('success', 'Message deleted.');

        $this->assertModelMissing($contact);
    }

    public function test_sidebar_links_to_the_inbox_with_the_unhandled_count(): void
    {
        $this->message();
        $this->message();
        $this->message(['handled_at' => now()]);

        $this->actingAs($this->admin())
            ->get(route('admin.cats.index'))
            ->assertOk()
            ->assertSee('href="'.route('admin.messages.index').'"', false)
            ->assertSee('title="Messages not handled yet">2</span>', false);
    }

    public function test_regular_users_cannot_use_the_inbox(): void
    {
        $contact = $this->message();
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('admin.messages.index'))->assertForbidden();
        $this->actingAs($user)->patch(route('admin.messages.handled', $contact), ['handled' => 1])->assertForbidden();
        $this->actingAs($user)->delete(route('admin.messages.destroy', $contact))->assertForbidden();

        $this->assertNull($contact->fresh()->handled_at);
        $this->assertModelExists($contact);
    }
}
