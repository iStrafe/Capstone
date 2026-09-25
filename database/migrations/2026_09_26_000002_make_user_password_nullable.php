<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Accounts created through Google sign-in have no password until the user sets one.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('password')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Password-less accounts get an unusable random hash so the column can be NOT NULL again.
        DB::table('users')->whereNull('password')->orderBy('id')->each(function ($user) {
            DB::table('users')->where('id', $user->id)->update(['password' => Hash::make(Str::random(40))]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('password')->nullable(false)->change();
        });
    }
};
