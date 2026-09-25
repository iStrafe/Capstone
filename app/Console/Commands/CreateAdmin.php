<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class CreateAdmin extends Command
{
    protected $signature = 'app:create-admin
                            {email : Email address of the admin account}
                            {--name=AduCats Admin : Display name for a new account}
                            {--promote : Make an existing account an admin instead of creating one}';

    protected $description = 'Create an admin account, or promote an existing one, without putting a password in code or shell history';

    public function handle(): int
    {
        $email = strtolower(trim($this->argument('email')));
        $existing = User::where('email', $email)->first();

        if ($this->option('promote')) {
            if (! $existing) {
                $this->error("No account uses {$email}.");

                return self::FAILURE;
            }

            $existing->forceFill(['role' => 'admin'])->save();
            $this->info("{$email} is now an admin.");

            return self::SUCCESS;
        }

        if ($existing) {
            $this->error("{$email} already has an account. Use --promote to make it an admin.");

            return self::FAILURE;
        }

        $emailCheck = Validator::make(['email' => $email], ['email' => ['required', 'email', 'max:255']]);
        if ($emailCheck->fails()) {
            $this->error($emailCheck->errors()->first('email'));

            return self::FAILURE;
        }

        // Asked interactively so the password never appears in the command line or shell history.
        $password = (string) $this->secret('Password');
        $confirmation = (string) $this->secret('Confirm password');

        $validator = Validator::make(
            ['password' => $password, 'password_confirmation' => $confirmation],
            ['password' => ['required', 'confirmed', $this->passwordRule()]],
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $message) {
                $this->error($message);
            }

            return self::FAILURE;
        }

        $user = User::create([
            'name' => $this->option('name'),
            'email' => $email,
            'password' => $password,
        ]);
        $user->forceFill(['role' => 'admin', 'email_verified_at' => now()])->save();

        $this->info("Admin account created for {$email}.");

        return self::SUCCESS;
    }

    private function passwordRule(): Password
    {
        $rule = Password::min(12)->letters()->mixedCase()->numbers();

        // The leaked-password check calls the Have I Been Pwned range API, so only production pays for it.
        return app()->isProduction() ? $rule->uncompromised() : $rule;
    }
}
