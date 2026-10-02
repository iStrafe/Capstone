<?php

namespace App\Http\Middleware;

use App\Enums\AdoptionStatus;
use App\Models\AdoptionRequest;
use App\Models\Contact;
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
        $isAdmin = $user?->role === 'admin';

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'isAdmin' => $isAdmin,
                ] : null,
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                // Breeze auth and profile messages, e.g. 'profile-updated' or a password reset notice.
                'status' => fn () => $request->session()->get('status'),
            ],
            // Admin-only data for the admin layout. Other visitors don't get the key at all.
            ...($isAdmin ? [
                'admin' => [
                    // Same count as the Blade sidebar badge (view composer in AppServiceProvider).
                    'unreadMessages' => fn () => Contact::unhandled()->count(),
                    'pendingRequests' => fn () => AdoptionRequest::where('status', AdoptionStatus::Pending)->count(),
                    // The admin sidebar. Cats, news and the breed helper are still Blade pages.
                    'links' => [
                        'cats' => route('admin.cats.index'),
                        'requests' => route('admin.requests.index'),
                        'messages' => route('admin.messages.index'),
                        'news' => route('news-events.index'),
                        'breedHelper' => url('analyzeImage'),
                    ],
                ],
            ] : []),
            // URLs the React pages link or post to. Admin pages and Google sign-in are not React, so links to them need a full page load.
            'links' => fn () => [
                'home' => route('home'),
                'adopt' => route('adoptCat'),
                'adoptionSend' => route('adoption.request'),
                'about' => route('aboutus'),
                'events' => route('news-events.events'),
                'contact' => route('contactus'),
                'contactSend' => route('contact.store'),
                'donate' => route('paymongo.create'),
                'login' => route('login'),
                'register' => route('register'),
                'google' => route('google-auth'),
                'passwordRequest' => route('password.request'),
                'passwordEmail' => route('password.email'),
                'passwordStore' => route('password.store'),
                'passwordUpdate' => route('password.update'),
                'logout' => route('logout'),
                'myRequests' => route('myRequest'),
                'profile' => route('profile.edit'),
                'admin' => url('adminDashboard'),
            ],
        ];
    }
}
