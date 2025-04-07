<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserAccountController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $orders = Order::where('user_id', $user->id)
                      ->latest()
                      ->take(5)
                      ->get();

        return view('user.account.index', compact('user', 'orders'));
    }

    public function edit()
    {
        $user = Auth::user();
        return view('user.account.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if exists
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            // Store new profile picture
            $path = $request->file('profile_picture')->store('profile-pictures', 'public');
            $validated['profile_picture'] = $path;
        }

        $user->update($validated);

        return redirect()->route('user.account')
            ->with('success', 'Profile updated successfully');
    }

    public function orders()
    {
        $orders = auth()->user()->orders()->with(['items.bag'])->latest()->get();
        return view('user.account.orders', compact('orders'));
    }

    public function reviews()
    {
        $reviews = auth()->user()->reviews()->with('bag')->latest()->get();
        return view('user.account.reviews', compact('reviews'));
    }
}
