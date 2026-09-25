<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Credentials come from the environment so no real login lives in the repository.
        $password = env('ADMIN_PASSWORD') ?: Str::password(16);

        User::factory()->create([
            'name' => 'AduCats Admin',
            'email' => env('ADMIN_EMAIL', 'admin@example.com'),
            'role' => 'admin',
            'password' => Hash::make($password),
        ]);

        if (! env('ADMIN_PASSWORD')) {
            $this->command?->warn("Generated admin password: {$password}");
        }
    }
}
