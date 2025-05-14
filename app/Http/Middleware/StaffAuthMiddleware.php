<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class StaffAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || !(auth()->user()->isStaff() || auth()->user()->isFaculty())) {
            return redirect('/staff-login')->with('error', 'You must be logged in to access this page.');
        }

        return $next($request);
    }
}
