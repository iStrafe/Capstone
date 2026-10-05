<?php

namespace Tests\Feature\Auth;

use App\Models\Cat;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/**
 * Stage 4 fixes: case-insensitive emails, rate limits, accounts made with Google, and the
 * cat pictured beside the log in form.
 */
class AccountFixesTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_ignores_the_case_of_the_email(): void
    {
        $user = User::factory()->create(['email' => 'juan@example.com']);

        $this->post('/login', ['email' => '  Juan@Example.COM ', 'password' => 'password']);

        $this->assertAuthenticatedAs($user);
    }

    public function test_emails_are_stored_in_lowercase(): void
    {
        $this->post('/register', [
            'name' => 'Juan',
            'email' => 'Juan@Example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertSessionHasNoErrors();

        $this->assertSame('juan@example.com', User::sole()->email);
    }

    public function test_the_same_email_in_another_case_cannot_register_twice(): void
    {
        User::factory()->create(['email' => 'juan@example.com']);

        $this->post('/register', [
            'name' => 'Someone else',
            'email' => 'JUAN@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertSame(1, User::count());
    }

    public function test_profile_email_changes_are_lowercased(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->patch('/profile', ['name' => 'Juan', 'email' => 'New@Example.com'])
            ->assertSessionHasNoErrors();

        $this->assertSame('new@example.com', $user->fresh()->email);
    }

    public function test_password_reset_links_ignore_the_case_of_the_email(): void
    {
        Notification::fake();
        $user = User::factory()->create(['email' => 'juan@example.com']);

        $this->post('/forgot-password', ['email' => 'JUAN@example.com'])->assertSessionHasNoErrors();

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_forgot_password_gives_the_same_answer_for_unknown_emails(): void
    {
        Notification::fake();
        User::factory()->create(['email' => 'juan@example.com']);

        $message = 'If an account uses that email, a link to choose a new password is on its way. Check your inbox.';

        $this->post('/forgot-password', ['email' => 'juan@example.com'])->assertSessionHasNoErrors()->assertSessionHas('status', $message);
        $this->post('/forgot-password', ['email' => 'nobody@example.com'])->assertSessionHasNoErrors()->assertSessionHas('status', $message);
    }

    public function test_the_lowercase_migration_fixes_old_mixed_case_emails_without_merging_accounts(): void
    {
        $migration = require database_path('migrations/2026_10_01_000000_lowercase_user_emails.php');

        // Written straight to the table, the way older code stored them.
        $mixed = User::factory()->create();
        $taken = User::factory()->create(['email' => 'pedro@example.com']);
        $clash = User::factory()->create();
        DB::table('users')->where('id', $mixed->id)->update(['email' => 'Juan@Example.com']);
        DB::table('users')->where('id', $clash->id)->update(['email' => 'Pedro@Example.com']);

        $migration->up();

        $this->assertSame('juan@example.com', DB::table('users')->where('id', $mixed->id)->value('email'));
        $this->assertSame('pedro@example.com', DB::table('users')->where('id', $taken->id)->value('email'));
        $this->assertSame('Pedro@Example.com', DB::table('users')->where('id', $clash->id)->value('email'), 'a clash is left for a person to sort out');
    }

    public function test_registering_is_rate_limited(): void
    {
        foreach (range(1, 6) as $attempt) {
            $this->post('/register', ['name' => 'Bot', 'email' => "bot{$attempt}@example.com", 'password' => 'short'])
                ->assertSessionHasErrors('password');
        }

        $this->post('/register', ['name' => 'Bot', 'email' => 'bot7@example.com', 'password' => 'short'])
            ->assertStatus(429);
    }

    public function test_reset_link_requests_are_rate_limited(): void
    {
        Notification::fake();

        foreach (range(1, 6) as $attempt) {
            $this->post('/forgot-password', ['email' => "someone{$attempt}@example.com"])->assertRedirect();
        }

        $this->post('/forgot-password', ['email' => 'someone7@example.com'])->assertStatus(429);
    }

    public function test_react_forms_that_hit_a_rate_limit_go_back_with_a_message(): void
    {
        foreach (range(1, 6) as $attempt) {
            $this->post('/register', ['name' => 'Bot', 'email' => "bot{$attempt}@example.com", 'password' => 'short']);
        }

        $this->from('/register')
            ->withHeader('X-Inertia', 'true')
            ->post('/register', ['name' => 'Bot', 'email' => 'bot7@example.com', 'password' => 'short'])
            ->assertRedirect('/register')
            ->assertSessionHas('error', 'Too many tries. Please wait a minute and try again.');
    }

    public function test_react_forms_with_an_expired_session_go_back_with_a_message(): void
    {
        // Turn the CSRF check back on, which the test suite normally skips.
        $this->app->instance('env', 'local');

        $this->from('/login')
            ->withHeader('X-Inertia', 'true')
            ->post('/login', ['email' => 'juan@example.com', 'password' => 'password', '_token' => 'stale'])
            ->assertRedirect('/login')
            ->assertSessionHas('error', 'This page expired. Please try again.');
    }

    public function test_google_only_accounts_can_set_a_password_without_a_current_one(): void
    {
        $user = User::factory()->create(['password' => null, 'google_id' => 'google-123']);

        $this->actingAs($user)
            ->get('/profile')
            ->assertInertia(fn (Assert $page) => $page->where('account.hasPassword', false)->where('account.usesGoogle', true));

        $this->from('/profile')
            ->put('/password', ['password' => 'new-password', 'password_confirmation' => 'new-password'])
            ->assertSessionHasNoErrors()
            ->assertSessionHas('status', 'password-set')
            ->assertRedirect('/profile');

        $this->assertTrue(Hash::check('new-password', $user->fresh()->password));
    }

    public function test_accounts_with_a_password_still_need_the_current_one(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from('/profile')
            ->put('/password', ['password' => 'new-password', 'password_confirmation' => 'new-password'])
            ->assertSessionHasErrorsIn('updatePassword', 'current_password');
    }

    public function test_google_only_accounts_confirm_deletion_by_typing_their_email(): void
    {
        $user = User::factory()->create(['email' => 'juan@example.com', 'password' => null, 'google_id' => 'google-123']);

        $this->actingAs($user)
            ->from('/profile')
            ->delete('/profile', ['confirm_email' => 'someone@example.com'])
            ->assertSessionHasErrorsIn('userDeletion', 'confirm_email')
            ->assertRedirect('/profile');
        $this->assertNotNull($user->fresh());

        $this->delete('/profile', ['confirm_email' => ' Juan@Example.com '])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/')
            ->assertSessionHas('success', 'Your account was deleted.');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_login_shows_the_cat_the_guest_wanted_to_adopt(): void
    {
        Storage::fake('public')->put('images/1730882812.png', 'photo');
        $cat = Cat::create(['cat_name' => 'Snow', 'age' => 2, 'color' => 'White', 'breed' => 'Puspin', 'sex' => 'Female', 'status' => Cat::STATUS_ACTIVE, 'cat_image' => '1730882812.png']);

        $this->get(route('adoption.start', $cat))->assertRedirect(route('login'));

        $this->get('/login')->assertInertia(fn (Assert $page) => $page
            ->component('Auth/Login')
            ->where('panelCat.name', 'Snow')
            ->where('panelCat.adopting', true)
            ->where('panelCat.image', asset('storage/images/1730882812.png')));
    }

    public function test_login_without_a_wanted_cat_shows_an_available_cat_with_a_photo(): void
    {
        Storage::fake('public')->put('images/1730882812.png', 'photo');
        Cat::create(['cat_name' => 'Nophoto', 'age' => 2, 'color' => 'Grey', 'breed' => 'Puspin', 'sex' => 'Male', 'status' => Cat::STATUS_ACTIVE]);
        Cat::create(['cat_name' => 'Snow', 'age' => 2, 'color' => 'White', 'breed' => 'Puspin', 'sex' => 'Female', 'status' => Cat::STATUS_ACTIVE, 'cat_image' => '1730882812.png']);

        $this->get('/register')->assertInertia(fn (Assert $page) => $page
            ->component('Auth/Register')
            ->where('panelCat.name', 'Snow')
            ->where('panelCat.adopting', false));
    }

    public function test_login_works_with_no_cats_at_all(): void
    {
        $this->get('/login')->assertInertia(fn (Assert $page) => $page->where('panelCat', null));
    }
}
