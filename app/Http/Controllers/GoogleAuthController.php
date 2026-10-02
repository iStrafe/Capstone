<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleAuthController extends Controller
{
    /** Whether Google sign-in is set up. Without keys, Google would only show its own error page. */
    public static function enabled(): bool
    {
        return filled(config('services.google.client_id')) && filled(config('services.google.client_secret'));
    }

    public function redirect()
    {
        if (! self::enabled()) {
            return redirect()->route('login')->with('error', 'Google sign-in isn’t available right now. Please log in with your email and password.');
        }

        return Socialite::driver('google')->redirect();
    }

    public function callbackGoogle(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable $e) {
            // Cancelled consent, an expired state or a Google outage all land here.
            Log::warning('Google sign-in failed', ['error' => $e::class, 'message' => $e->getMessage()]);

            return redirect()->route('login')->withErrors(['email' => 'Google sign-in did not complete. Please try again.']);
        }

        $user = User::where('google_id', $googleUser->getId())->first();
        $removedPassword = false;

        if (! $user) {
            $emailVerified = (bool) ($googleUser->user['email_verified'] ?? false);
            $user = User::where('email', Str::lower(trim((string) $googleUser->getEmail())))->first();

            if ($user) {
                // Linking trusts Google's word that this person owns the address, so it needs a verified email.
                if (! $emailVerified) {
                    return redirect()->route('login')->withErrors([
                        'email' => 'An account with this email already exists. Log in with your password instead.',
                    ]);
                }

                // Nobody ever proved they own this address, so whoever set the password may not be
                // the person Google just vouched for (someone could have signed up with another
                // person's email first). Remove that password and end the account's other sessions.
                if ($user->email_verified_at === null && $user->hasPassword()) {
                    $user->forceFill(['password' => null, 'remember_token' => Str::random(60)]);
                    $this->endSessions($user);
                    $removedPassword = true;
                }

                $user->forceFill([
                    'google_id' => $googleUser->getId(),
                    'email_verified_at' => $user->email_verified_at ?? now(),
                ])->save();
            } else {
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                ]);

                if ($emailVerified) {
                    $user->forceFill(['email_verified_at' => now()])->save();
                }

                Auth::login($user);

                return redirect()->intended(route('profile.edit'));
            }
        }

        Auth::login($user);

        if ($removedPassword) {
            session()->flash('success', 'You\'re logged in with Google. The password on this account was removed because its email was never confirmed. You can set a new one on your profile.');
        }

        if ($user->role === 'admin') {
            return redirect()->route('admin.cats.index');
        }

        // Back to the page that asked them to log in, if any.
        return redirect()->intended(route('home'));
    }

    /** Signs the account out everywhere else. Only the database session driver keeps a user_id to look up. */
    private function endSessions(User $user): void
    {
        if (config('session.driver') === 'database') {
            DB::connection(config('session.connection'))
                ->table(config('session.table', 'sessions'))
                ->where('user_id', $user->getKey())
                ->delete();
        }
    }
}
