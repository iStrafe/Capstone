<?php

namespace Tests\Feature\Console;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CreateAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_an_admin_with_a_prompted_password(): void
    {
        $this->artisan('app:create-admin', ['email' => 'Admin@AduCats.test'])
            ->expectsQuestion('Password', 'Str0ngPassword!')
            ->expectsQuestion('Confirm password', 'Str0ngPassword!')
            ->assertSuccessful();

        $admin = User::where('email', 'admin@aducats.test')->sole();
        $this->assertSame('admin', $admin->role);
        $this->assertTrue(Hash::check('Str0ngPassword!', $admin->password));
    }

    public function test_rejects_weak_or_mismatched_passwords(): void
    {
        $this->artisan('app:create-admin', ['email' => 'admin@aducats.test'])
            ->expectsQuestion('Password', 'akbar911')
            ->expectsQuestion('Confirm password', 'akbar911')
            ->assertFailed();

        $this->artisan('app:create-admin', ['email' => 'admin@aducats.test'])
            ->expectsQuestion('Password', 'Str0ngPassword!')
            ->expectsQuestion('Confirm password', 'Different0ne!')
            ->assertFailed();

        $this->assertDatabaseCount('users', 0);
    }

    public function test_promotes_an_existing_account(): void
    {
        $user = User::factory()->create(['email' => 'juan@example.com']);

        $this->artisan('app:create-admin', ['email' => 'juan@example.com', '--promote' => true])->assertSuccessful();

        $this->assertSame('admin', $user->fresh()->role);
    }

    public function test_does_not_create_a_second_account_for_an_existing_email(): void
    {
        User::factory()->create(['email' => 'juan@example.com']);

        $this->artisan('app:create-admin', ['email' => 'juan@example.com'])->assertFailed();

        $this->assertDatabaseCount('users', 1);
    }
}
