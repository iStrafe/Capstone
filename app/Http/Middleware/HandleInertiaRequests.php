<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

/**
 * Shared data for every React (Inertia) page. Blade pages don't use any of this.
 */
class HandleInertiaRequests extends Middleware
{
    /**
     * The root template loaded on the first visit to a React page: resources/views/app.blade.php.
     *
     * @var string
     */
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'isAdmin' => $user->role === 'admin',
                ] : null,
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            // Most of the site is still Blade, so the React layout links to it by URL.
            'links' => fn () => [
                'home' => route('home'),
                'adopt' => route('adoptCat'),
                'about' => route('aboutus'),
                'events' => route('news-events.events'),
                'contact' => route('contactus'),
                'donate' => route('paymongo.create'),
                'login' => route('login'),
                'register' => route('register'),
                'logout' => route('logout'),
                'myRequests' => route('myRequest'),
                'profile' => route('profile.edit'),
                'admin' => url('adminDashboard'),
            ],
        ];
    }
}
