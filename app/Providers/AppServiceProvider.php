<?php

namespace App\Providers;

use Illuminate\Foundation\Console\ServeCommand;
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
        // `artisan serve` gives the PHP server only a short list of environment variables when
        // php.ini has variables_order="EGPCS". On Windows PHP then has no TEMP or TMP folder,
        // so every upload fails before Laravel sees it. Pass those two through as well.
        ServeCommand::$passthroughVariables = array_values(array_unique([...ServeCommand::$passthroughVariables, 'TEMP', 'TMP']));
    }
}
