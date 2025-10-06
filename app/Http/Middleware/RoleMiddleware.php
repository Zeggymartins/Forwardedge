<?php
// app/Http/Middleware/RoleMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {
        if (!Auth::check()) {
            // If it's an AJAX request, return JSON
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Please login to continue'
                ], 401);
            }

            // For admin routes, redirect to Laravel login
            if ($role === 'admin') {
                return redirect()->route('login')->with('error', 'Please login to access admin panel');
            }

            // For user routes, return 401 so the modal triggers
            return response()->json([
                'status' => 'error',
                'message' => 'Authentication required'
            ], 401);
        }

        $user = Auth::user();

        // Admin trying to access admin routes
        if ($role === 'admin' && $user->role !== 'admin') {
            abort(403, 'Unauthorized access');
        }

        // User trying to access user routes (admin can also access)
        if ($role === 'user' && !in_array($user->role, ['user', 'admin'])) {
            abort(403, 'Unauthorized access');
        }

        return $next($request);
    }
}
