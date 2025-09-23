<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Debug: Log middleware execution
        \Log::info('AdminMiddleware: Executing', [
            'url' => $request->fullUrl(),
            'user_agent' => $request->userAgent(),
            'auth_check' => Auth::check(),
            'expects_json' => $request->expectsJson(),
        ]);

        // For testing purposes, allow all authenticated users
        // In production, you would check for admin role/permission
        if (Auth::check()) {
            \Log::info('AdminMiddleware: User is authenticated, proceeding');
            return $next($request);
        }

        // Return JSON response for API requests, redirect for web requests
        if ($request->expectsJson()) {
            \Log::info('AdminMiddleware: API request, returning JSON error');
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        \Log::info('AdminMiddleware: Redirecting to login');
        return redirect('/admin/login');
    }
}
