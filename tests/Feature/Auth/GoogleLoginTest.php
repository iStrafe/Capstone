<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
        $this->assertFalse($user->hasPassword());
        $this->assertSame('google-123', $user->google_id);
        $this->assertNull($user->password);
        $this->assertNotNull($user->email_verified_at);
    }

    public function test_verified_google_email_links_to_the_existing_account(): void
    {
        $existing = User::factory()->create(['email' => 'juan@example.com']);
        $this->fakeGoogleUser('juan@example.com');

        $this->get('/auth/google/callbacks')->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($existing);
        $this->assertSame('google-123', $existing->fresh()->google_id);
        $this->assertSame(1, User::count());
    }

    public function test_linking_removes_a_password_set_by_someone_who_never_confirmed_the_email(): void
    {
        // Someone signed up with Juan's email first and set their own password.
        $squatter = User::factory()->unverified()->create(['email' => 'juan@example.com', 'password' => 'squatter-password', 'remember_token' => 'old-token']);
        config(['session.driver' => 'database']);
        DB::table('sessions')->insert(['id' => 'squatter-session', 'user_id' => $squatter->id, 'payload' => '', 'last_activity' => time()]);

        $this->fakeGoogleUser('juan@example.com');

        $this->get('/auth/google/callbacks')
            ->assertRedirect(route('home'))
            ->assertSessionHas('success');

        $squatter->refresh();
        $this->assertAuthenticatedAs($squatter);
        $this->assertNull($squatter->password, 'the squatter can no longer log in with their password');
        $this->assertNotSame('old-token', $squatter->remember_token, '"keep me logged in" cookies stop working');
        $this->assertNotNull($squatter->email_verified_at);
        $this->assertSame(0, DB::table('sessions')->where('id', 'squatter-session')->count(), 'their other sessions end');
    }

    public function test_linking_keeps_the_password_of_a_confirmed_account(): void
    {
        $existing = User::factory()->create(['email' => 'juan@example.com', 'password' => 'my-password']);
        $this->fakeGoogleUser('juan@example.com');

        $this->get('/auth/google/callbacks')->assertRedirect(route('home'))->assertSessionMissing('success');

        $this->assertTrue(Hash::check('my-password', $existing->fresh()->password));
    }

    public function test_google_emails_match_accounts_whatever_their_case(): void
    {
        $existing = User::factory()->create(['email' => 'juan@example.com']);
        $this->fakeGoogleUser('Juan@Example.COM');

        $this->get('/auth/google/callbacks')->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($existing);
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

        $this->get('/auth/google/callbacks')->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($existing);
    }

    public function test_returning_google_users_go_back_to_the_page_they_wanted(): void
    {
        User::factory()->create(['google_id' => 'google-123']);
        $this->fakeGoogleUser('juan@example.com');

        $this->get(route('myRequest'))->assertRedirect(route('login'));

        $this->get('/auth/google/callbacks')->assertRedirect(route('myRequest'));
    }

    public function test_google_admins_still_land_on_the_cat_inventory(): void
    {
        User::factory()->create(['google_id' => 'google-123', 'role' => 'admin']);
        $this->fakeGoogleUser('admin@example.com');

        $this->get('/auth/google/callbacks')->assertRedirect(route('admin.cats.index'));
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
