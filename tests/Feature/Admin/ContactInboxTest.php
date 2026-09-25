<?php

namespace Tests\Feature\Admin;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
        $this->message([
            'full_name' => 'Newer Sender',
            'email' => 'newer@example.com',
            'mobile_number' => '09998887777',
            'message' => 'Can I volunteer on weekends?',
            'created_at' => now()->subDay(),
        ]);

        $this->actingAs($this->admin())
            ->get(route('admin.messages.index'))
            ->assertOk()
            ->assertSeeInOrder(['Newer Sender', 'Older Sender'])
            ->assertSee('href="mailto:newer@example.com"', false)
            ->assertSee('09998887777')
            ->assertSee('Can I volunteer on weekends?')
            ->assertSee(now()->subDay()->format('M j, Y'))
            ->assertSee('2 messages are not handled yet.');
    }

    public function test_inbox_is_paginated(): void
    {
        for ($i = 1; $i <= 16; $i++) {
            $this->message(['full_name' => "Sender {$i}", 'created_at' => now()->subMinutes(100 - $i)]);
        }

        $admin = $this->admin();
        $this->actingAs($admin)->get(route('admin.messages.index'))
            ->assertOk()
            ->assertSee('Sender 16')
            ->assertDontSee('Sender 1<', false)
            ->assertViewHas('messages', fn ($paginator) => $paginator->total() === 16 && $paginator->count() === 15);

        $this->actingAs($admin)->get(route('admin.messages.index', ['page' => 2]))
            ->assertOk()
            ->assertSee('Sender 1<', false);
    }

    public function test_inbox_shows_when_no_email_was_given(): void
    {
        $this->message(['email' => null]);

        $this->actingAs($this->admin())->get(route('admin.messages.index'))->assertOk()->assertSee('Not given');
    }

    public function test_admin_can_mark_a_message_handled_and_back(): void
    {
        $contact = $this->message();
        $admin = $this->admin();

        $this->actingAs($admin)
            ->from(route('admin.messages.index'))
            ->patch(route('admin.messages.handled', $contact), ['handled' => 1])
            ->assertRedirect(route('admin.messages.index'))
            ->assertSessionHas('success', 'Message marked as handled.');
        $this->assertNotNull($contact->fresh()->handled_at);

        $this->actingAs($admin)->get(route('admin.messages.index'))
            ->assertSee('Handled')
            ->assertSee('Mark as unhandled')
            ->assertSee('Nothing is waiting for a reply.');

        $this->actingAs($admin)
            ->from(route('admin.messages.index'))
            ->patch(route('admin.messages.handled', $contact), ['handled' => 0])
            ->assertRedirect(route('admin.messages.index'));
        $this->assertNull($contact->fresh()->handled_at);
    }

    public function test_admin_can_delete_a_message_after_confirming(): void
    {
        $contact = $this->message();
        $admin = $this->admin();

        $this->actingAs($admin)->get(route('admin.messages.index'))
            ->assertSee("onsubmit=\"return confirm('Delete this message? This cannot be undone.')\"", false);

        $this->actingAs($admin)
            ->from(route('admin.messages.index'))
            ->delete(route('admin.messages.destroy', $contact))
            ->assertRedirect(route('admin.messages.index'))
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
