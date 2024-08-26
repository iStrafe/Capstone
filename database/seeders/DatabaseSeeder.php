<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'AduCats Admin',
            'email' => 'reginald.unisa@adamson.edu.ph',
            'role' => 'admin',
            'phone' => '09270130174',
            'password' => Hash::make('akbar911'),
        ]);
    }
}
