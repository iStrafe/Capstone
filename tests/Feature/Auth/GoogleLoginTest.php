<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use RuntimeException;
use Tests\TestCase;

class GoogleLoginTest extends TestCase
{
    use RefreshDatabase;

    private function fakeGoogleUser(string $email, bool $verified = true, string $id = 'google-123'): void
    {
        $googleUser = (new SocialiteUser)->setRaw(['email_verified' => $verified])->map([
            'id' => $id,
            'name' => 'Juan Dela Cruz',
            'email' => $email,
        ]);

        $provider = Mockery::mock(Provider::class);
        $provider->shouldReceive('user')->andReturn($googleUser);
        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);
    }

    public function test_new_google_users_get_an_account_without_a_password(): void
    {
        $this->fakeGoogleUser('juan@example.com');

        $this->get('/auth/google/callbacks')->assertRedirect(route('profile.edit'));

        $user = User::where('email', 'juan@example.com')->sole();
        $this->assertAuthenticatedAs($user);
        $this->assertSame('google-123', $user->google_id);
        $this->assertNull($user->password);
        $this->assertNotNull($user->email_verified_at);
    }

    public function test_verified_google_email_links_to_the_existing_account(): void
    {
        $existing = User::factory()->create(['email' => 'juan@example.com']);
        $this->fakeGoogleUser('juan@example.com');

        $this->get('/auth/google/callbacks')->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($existing);
        $this->assertSame('google-123', $existing->fresh()->google_id);
        $this->assertSame(1, User::count());
    }

    public function test_unverified_google_email_does_not_take_over_an_existing_account(): void
    {
        $existing = User::factory()->create(['email' => 'juan@example.com']);
        $this->fakeGoogleUser('juan@example.com', verified: false);

        $this->get('/auth/google/callbacks')->assertRedirect(route('login'))->assertSessionHasErrors('email');

        $this->assertGuest();
        $this->assertNull($existing->fresh()->google_id);
    }

    public function test_returning_google_users_are_found_by_google_id(): void
    {
        $existing = User::factory()->create(['email' => 'old@example.com', 'google_id' => 'google-123']);
        $this->fakeGoogleUser('new@example.com');

        $this->get('/auth/google/callbacks')->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($existing);
    }

    public function test_failed_google_callback_returns_to_login(): void
    {
        $provider = Mockery::mock(Provider::class);
        $provider->shouldReceive('user')->andThrow(new RuntimeException('state mismatch'));
        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $this->get('/auth/google/callbacks')->assertRedirect(route('login'))->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}
