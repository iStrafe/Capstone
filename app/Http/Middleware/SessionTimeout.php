<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SessionTimeout
{
     protected $timeout = 300; // 5 minutes

    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $lastActivity = session('lastActivityTime');
            if ($lastActivity && (time() - $lastActivity > $this->timeout)) {
                Auth::logout();
                session(['timeoutMessage' => 'You have been logged out due to inactivity.']);
                return redirect('/login');
            }
            session(['lastActivityTime' => time()]);
        }

        return $next($request);
    }
}
