<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callbackGoogle(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable $e) {
            // Cancelled consent, an expired state or a Google outage all land here.
            Log::warning('Google sign-in failed', ['error' => $e->getMessage()]);

            return redirect()->route('login')->withErrors(['email' => 'Google sign-in did not complete. Please try again.']);
        }

        $user = User::where('google_id', $googleUser->getId())->first();

        if (! $user) {
            $emailVerified = (bool) ($googleUser->user['email_verified'] ?? false);
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // Linking trusts Google's word that this person owns the address, so it needs a verified email.
                if (! $emailVerified) {
                    return redirect()->route('login')->withErrors([
                        'email' => 'An account with this email already exists. Log in with your password instead.',
                    ]);
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

        if ($user->role === 'admin') {
            return redirect()->route('admin.cats.index');
        }

        // Back to the page that asked them to log in, if any.
        return redirect()->intended(route('home'));
    }
}
