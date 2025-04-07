<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CustomVerifyEmail
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        Log::info('Auth Check - User:', [
            'id' => $user->id,
            'email' => $user->email,
            'role' => $user->role
        ]);

        // Admin bypass - no email verification needed
        if ($user->role === 'admin') {
            return $next($request);
        }

        // Regular users must verify email
        if (!$user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        return $next($request);
    }
}