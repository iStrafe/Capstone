<?php

use App\Http\Middleware\admin;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Inertia\Support\Header;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
         $middleware->alias([
            'admin'=>admin::class,
        ]);

        // "Create an account" on a cat profile links to the adoption page with ?new=1, so the
        // guest signs up instead of logging in and still comes back to that cat afterwards.
        $middleware->redirectGuestsTo(fn (Request $request) => $request->routeIs('adoption.start') && $request->boolean('new')
            ? route('register')
            : route('login'));

        // Serves the React pages (resources/js/pages) through Inertia.
        $middleware->web(append: [
            HandleInertiaRequests::class,
        ]);

        // Laravel's default priority list, with the admin check moved ahead of route model
        // binding so non-admins get a 403 instead of learning which records exist (404).
        $middleware->priority([
            \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Contracts\Auth\Middleware\AuthenticatesRequests::class,
            \Illuminate\Routing\Middleware\ThrottleRequests::class,
            \Illuminate\Routing\Middleware\ThrottleRequestsWithRedis::class,
            \Illuminate\Contracts\Session\Middleware\AuthenticatesSessions::class,
            admin::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \Illuminate\Auth\Middleware\Authorize::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // A React form that hits an expired session (419) or a rate limit (429) goes back to
        // the same page with a message, instead of Inertia showing the error page in a modal.
        $exceptions->respond(function (Response $response, Throwable $e, Request $request) {
            if (! $request->header(Header::INERTIA) || ! in_array($response->getStatusCode(), [419, 429], true)) {
                return $response;
            }

            return back()->with('error', $response->getStatusCode() === 419
                ? 'This page expired. Please try again.'
                : 'Too many tries. Please wait a minute and try again.');
        });
    })->create();
