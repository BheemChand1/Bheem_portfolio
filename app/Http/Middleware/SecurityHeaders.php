<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        $dev = app()->environment('local') && is_file(public_path('hot'));
        $vite = $dev ? ' http://localhost:5173 http://127.0.0.1:5173 http://[::1]:5173' : '';
        $socket = $dev ? ' ws://localhost:5173 ws://127.0.0.1:5173 ws://[::1]:5173' : '';
        $response->headers->set('Content-Security-Policy', "default-src 'self'; script-src 'self' 'unsafe-inline'{$vite}; style-src 'self' 'unsafe-inline'{$vite}; img-src 'self' data:; font-src 'self'; connect-src 'self'{$vite}{$socket}; frame-src 'self'; object-src 'none'; base-uri 'self'; form-action 'self'; frame-ancestors 'self'");
        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000');
        }

        return $response;
    }
}
