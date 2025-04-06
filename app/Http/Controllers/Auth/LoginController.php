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

    protected function attemptLogin(Request $request)
    {
        $credentials = $request->only('email', 'password');
        
        if (Auth::attempt($credentials)) {
            if (!Auth::user()->is_active) {
                Auth::logout();
                return false;
            }
            return true;
        }
        return false;
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        $user = \App\Models\User::where('email', $request->email)->first();
        
        if ($user && !$user->is_active) {
            return redirect()->back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors(['email' => 'Your account has been deactivated.']);
        }

        return redirect()->back()
            ->withInput($request->only('email', 'remember'))
            ->withErrors(['email' => trans('auth.failed')]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if ($this->attemptLogin($request)) {
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

        return $this->sendFailedLoginResponse($request);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}