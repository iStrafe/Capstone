<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Support\Header;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * While the site is half Blade, half React: when an Inertia visit (a React link, or the redirect
 * after a React form) lands on a Blade page, tell the browser to load that page in full instead
 * of letting Inertia show the HTML in its error modal.
 *
 * Only successful GET responses that are plain HTML are converted. Redirects, JSON, files,
 * downloads, streams and Inertia responses pass through untouched.
 */
class InertiaBladeFallback
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $this->isBladePageForInertiaVisit($request, $response)) {
            return $response;
        }

        // The Blade page just consumed any flash data (success message, errors, old input);
        // keep it for the full page load that follows.
        if ($request->hasSession()) {
            $request->session()->reflash();
        }

        // What Inertia::location() answers an Inertia request with, built from this request
        // rather than the global one.
        return new Response('', 409, [Header::LOCATION => $request->fullUrl()]);
    }

    private function isBladePageForInertiaVisit(Request $request, Response $response): bool
    {
        if (! $request->header(Header::INERTIA) || ! $request->isMethod('GET')) {
            return false;
        }

        if ($response->headers->has(Header::INERTIA)
            || $response->getStatusCode() !== 200
            || $response instanceof JsonResponse
            || $response instanceof BinaryFileResponse
            || $response instanceof StreamedResponse
            || $response->headers->has('Content-Disposition')) {
            return false;
        }

        return str_contains((string) $response->headers->get('Content-Type'), 'text/html');
    }
}
