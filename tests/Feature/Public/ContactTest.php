<?php

namespace Tests\Feature\Public;

use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ContactTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_form_shows_the_success_message(): void
    {
        $this->from(route('contactus'))
            ->followingRedirects()
            ->post(route('contact.store'), [
                'full_name' => 'Juan Dela Cruz',
                'mobile_number' => '09171234567',
                'message' => 'Is Mingming still available?',
            ])
            ->assertOk()
            ->assertSee('Your message reached the AduCats team.');

        $this->assertSame(1, Contact::count());
    }

    public function test_contact_form_takes_an_optional_email_for_the_admin_inbox(): void
    {
        $this->get(route('contactus'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Contact')
                ->where('links.contactSend', route('contact.store')));

        $this->post(route('contact.store'), [
            'full_name' => 'Juan Dela Cruz',
            'email' => 'juan@example.com',
            'mobile_number' => '09171234567',
            'message' => 'Hello',
        ])->assertSessionHasNoErrors();

        $this->post(route('contact.store'), [
            'full_name' => 'Maria Clara',
            'email' => 'not-an-email',
            'mobile_number' => '09171234567',
            'message' => 'Hello',
        ])->assertSessionHasErrors('email');

        $contact = Contact::sole();
        $this->assertSame('juan@example.com', $contact->email);
        $this->assertNull($contact->handled_at);
    }

    public function test_contact_form_sends_validation_errors_back_to_the_react_page(): void
    {
        // The React form keeps what was typed itself; the server only has to send the errors back.
        $this->from(route('contactus'))
            ->post(route('contact.store'), [
                'full_name' => 'Juan Dela Cruz',
                'mobile_number' => str_repeat('9', 20),
                'message' => 'Is Mingming still available?',
            ])
            ->assertRedirect(route('contactus'))
            ->assertSessionHasErrors(['mobile_number' => 'Enter a phone number using digits, like 0917 123 4567.']);

        $this->get(route('contactus'))
            ->assertInertia(fn (Assert $page) => $page
                ->component('Contact')
                ->where('errors.mobile_number', 'Enter a phone number using digits, like 0917 123 4567.'));

        $this->assertSame(0, Contact::count());
    }
}
