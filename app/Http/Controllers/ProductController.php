<?php

namespace App\Http\Controllers;

use App\Models\Bag;
use App\Models\Review;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function show($id)
    {
        $product = Bag::with(['reviews.user'])->findOrFail($id);
        return view('user.product-details', compact('product'));
    }

    public function addReview(Request $request, $id)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10',
        ]);

        $review = new Review([
            'bag_id' => $id,
            'user_id' => auth()->id(),
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
        ]);

        $review->save();

        return redirect()->back()->with('success', 'Review added successfully!');
    }
}
