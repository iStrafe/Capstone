<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Logins and password resets now lowercase the email before looking it up, so stored
 * emails have to be lowercase too. An account whose lowercase email already belongs to
 * another account is left as it is and logged, since merging accounts needs a person.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')
            ->whereRaw('email <> LOWER(email)')
            ->orderBy('id')
            ->get(['id', 'email'])
            ->each(function ($user) {
                $lower = mb_strtolower($user->email);

                $taken = DB::table('users')
                    ->where('id', '<>', $user->id)
                    ->whereRaw('LOWER(email) = ?', [$lower])
                    ->exists();

                if ($taken) {
                    Log::warning('Email not lowercased: another account already uses it', ['user_id' => $user->id]);

                    return;
                }

                DB::table('users')->where('id', $user->id)->update(['email' => $lower]);
            });
    }

    public function down(): void
    {
        // The original casing isn't kept, and lowercase emails work either way.
    }
};
