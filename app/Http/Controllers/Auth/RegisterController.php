<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Storage;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = '/email/verify';

    public function __construct()
    {
        $this->middleware('guest');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'profile_picture' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);
    }

    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        $user = $this->create($request->all());

        if ($user->shouldVerifyEmail()) {
            event(new Registered($user));
            $this->guard()->login($user);
            return redirect()->route('verification.notice')
                ->with('success', 'Please verify your email address.');
        }

        $this->guard()->login($user);
        return redirect()->route('user.dashboard')
            ->with('success', 'Registration successful!');
    }

    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'profile_picture' => $data['profile_picture'] ?? null,
            'role' => 'user',  // Add this line
        ]);
    }

    protected function registered(Request $request, $user)
    {
        auth()->logout(); // Log out the user until they verify their email
        return redirect()->route('verification.notice');
    }
}