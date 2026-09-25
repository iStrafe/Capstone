<?php

namespace App\Providers;

use App\Models\Contact;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Badge on the admin sidebar's Messages link.
        View::composer('admin.adminNavbar', function ($view) {
            $view->with('unhandledMessages', auth()->user()?->role === 'admin' ? Contact::unhandled()->count() : 0);
        });
    }
}
