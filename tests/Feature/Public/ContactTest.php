<?php

namespace Tests\Feature\Public;

use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
            ->assertSee('Message sent successfully!');

        $this->assertSame(1, Contact::count());
    }

    public function test_contact_form_shows_validation_errors_and_keeps_the_input(): void
    {
        $this->from(route('contactus'))
            ->followingRedirects()
            ->post(route('contact.store'), [
                'full_name' => 'Juan Dela Cruz',
                'mobile_number' => str_repeat('9', 20),
                'message' => 'Is Mingming still available?',
            ])
            ->assertOk()
            ->assertSee('The mobile number field must not be greater than 15 characters.')
            ->assertSee('value="Juan Dela Cruz"', false)
            ->assertSee('>Is Mingming still available?</textarea>', false);

        $this->assertSame(0, Contact::count());
    }
}
