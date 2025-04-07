<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CustomEmailVerification
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Allow admin to bypass email verification
        if ($user->role === 'admin') {
            return $next($request);
        }

        // Check email verification for regular users
        if (!$user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        return $next($request);
    }
}