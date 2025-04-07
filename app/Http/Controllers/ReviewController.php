<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, Order $order)
    {
        $validated = $request->validate([
            'bag_id' => 'required|exists:bags,id',
            'rating' => 'required|integer|between:1,5',
            'comment' => 'required|string|min:10',
            'is_anonymous' => 'boolean'
        ]);

        // Check if order is delivered and belongs to user
        if ($order->status !== 'delivered' || $order->user_id !== Auth::id()) {
            return back()->with('error', 'You cannot review this product');
        }

        // Check if user already reviewed this bag in this order
        if (Review::where('user_id', Auth::id())
                 ->where('order_id', $order->id)
                 ->where('bag_id', $validated['bag_id'])
                 ->exists()) {
            return back()->with('error', 'You already reviewed this product');
        }

        Review::create([
            'user_id' => Auth::id(),
            'order_id' => $order->id,
            'bag_id' => $validated['bag_id'],
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
            'is_anonymous' => $validated['is_anonymous'] ?? false
        ]);

        return back()->with('success', 'Review submitted successfully');
    }

    public function update(Request $request, Review $review)
    {
        // Check if review belongs to user
        if ($review->user_id !== auth()->id()) {
            return back()->with('error', 'Unauthorized action.');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10',
            'is_anonymous' => 'boolean'
        ]);

        $review->update([
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
            'is_anonymous' => $validated['is_anonymous'] ?? false
        ]);

        return back()->with('success', 'Review updated successfully!');
    }
}
