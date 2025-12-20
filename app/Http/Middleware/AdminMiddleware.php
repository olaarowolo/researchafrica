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
        // Check if user is authenticated and has the 'Admin' role.
        if (Auth::check()) {
            $user = Auth::user();
            if (method_exists($user, 'roles') && $user->roles()->where('title', 'Admin')->exists()) {
                return $next($request);
            }
        }

        // If the user is not an admin, handle the response.
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        // For web requests, redirect them to the admin login page.
        return redirect()->route('admin.login')->with('error', 'You do not have permission to access this page.');
    }
}
