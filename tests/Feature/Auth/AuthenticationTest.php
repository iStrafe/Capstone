<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('home'));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_login_page_shows_why_sign_in_failed_and_keeps_the_email(): void
    {
        $user = User::factory()->create();

        $this->from('/login')
            ->followingRedirects()
            ->post('/login', [
                'email' => $user->email,
                'password' => 'wrong-password',
            ])
            ->assertOk()
            ->assertSee(__('auth.failed'))
            ->assertSee('value="'.$user->email.'"', false);
    }

    public function test_login_page_shows_the_status_message_and_forgot_password_link(): void
    {
        $this->withSession(['status' => 'Your password has been reset.'])
            ->get('/login')
            ->assertOk()
            ->assertSee('Your password has been reset.')
            ->assertSee('href="'.route('password.request').'"', false);
    }

    public function test_login_returns_the_user_to_the_page_they_wanted(): void
    {
        $user = User::factory()->create();

        $this->get(route('myRequest'))->assertRedirect(route('login'));

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('myRequest'));
    }

    public function test_admin_without_an_intended_page_lands_on_the_admin_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->post('/login', [
            'email' => $admin->email,
            'password' => 'password',
        ])->assertRedirect(url('adminDashboard'));
    }

    public function test_user_with_an_unknown_role_is_still_redirected(): void
    {
        $user = User::factory()->create(['role' => 'staff']);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('home'));
    }

    public function test_the_retired_user_dashboard_redirects_to_home(): void
    {
        $this->get('/userDashboard')->assertRedirect(route('home'));
        $this->actingAs(User::factory()->create())->get(route('dashboard'))->assertRedirect(route('home'));
    }

    public function test_verify_email_and_confirm_password_pages_are_gone(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get('/verify-email')->assertNotFound();
        $this->get('/confirm-password')->assertNotFound();
        $this->post('/confirm-password', ['password' => 'password'])->assertNotFound();
        $this->post('/email/verification-notification')->assertNotFound();

        foreach (['verification.notice', 'verification.verify', 'verification.send', 'password.confirm'] as $name) {
            $this->assertFalse(Route::has($name), "route {$name} should be gone");
        }
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
