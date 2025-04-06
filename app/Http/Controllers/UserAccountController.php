<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Review;
use Illuminate\Support\Facades\Hash;

class UserAccountController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $orders = $user->orders()->with(['items.bag'])->latest()->get();
        $reviews = $user->reviews()->with('bag')->latest()->get();
        
        return view('user.account.index', compact('user', 'orders', 'reviews'));
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . auth()->id(),
            'current_password' => 'nullable|required_with:password',
            'password' => 'nullable|min:8|confirmed',
        ]);

        $user = auth()->user();
        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'The provided password does not match your current password.']);
            }
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return back()->with('success', 'Profile updated successfully!');
    }
}
