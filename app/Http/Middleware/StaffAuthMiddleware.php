<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class StaffAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Check if the user session exists and role is staff/faculty
        if (!session()->has('user') || !in_array(session('user')->role, ['staff', 'faculty'])) {
            return redirect('/staff-login')->with('error', 'You must be logged in to access this page.');
        }

        return $next($request);
    }
}
