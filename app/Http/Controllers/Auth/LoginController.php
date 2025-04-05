<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            $intended = $request->session()->get('url.intended');
            
            \Log::info('Login details', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $user->role,
                'is_admin' => ($user->role === 'admin'),
                'intended_url' => $intended,
                'current_route' => $request->route()->getName()
            ]);

            if ($user->role === 'admin') {
                \Log::info('Redirecting admin to dashboard');
                return redirect()->intended(route('admin.dashboard'));
            }

            \Log::info('Redirecting user to home');
            return redirect()->intended(route('home'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}