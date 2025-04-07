<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UserMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || auth()->user()->role === 'admin') {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Administrators should use the admin dashboard.');
        }

        return $next($request);
    }
}