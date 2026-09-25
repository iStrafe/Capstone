<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_registration_form_keeps_name_and_email_after_an_error(): void
    {
        $this->from('/register')
            ->followingRedirects()
            ->post('/register', [
                'name' => 'Juan Dela Cruz',
                'email' => 'juan@example.com',
                'password' => 'password',
                'password_confirmation' => 'different',
            ])
            ->assertOk()
            ->assertSee('value="Juan Dela Cruz"', false)
            ->assertSee('value="juan@example.com"', false);

        $this->assertGuest();
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('home', absolute: false));
    }
}
