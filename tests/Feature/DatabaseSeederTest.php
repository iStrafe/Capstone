<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_creates_an_admin_without_hard_coded_credentials(): void
    {
        $this->seed();

        $admin = User::where('role', 'admin')->sole();
        $this->assertSame(env('ADMIN_EMAIL', 'admin@example.com'), $admin->email);
        $this->assertFalse(password_verify('akbar911', $admin->password));
    }
}
