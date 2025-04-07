<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Bag;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $cart = auth()->user()->cart ?? new Cart(['user_id' => auth()->id()]);
        return view('user.cart', compact('cart'));
    }

    public function add(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:bags,id',
            'quantity' => 'sometimes|integer|min:1'
        ]);

        $quantity = $validated['quantity'] ?? 1;
        
        // Get or create cart
        $cart = auth()->user()->cart ?? Cart::create(['user_id' => auth()->id()]);
        
        // Check if item already exists in cart
        $cartItem = $cart->items()->where('bag_id', $validated['product_id'])->first();
        
        if ($cartItem) {
            $cartItem->increment('quantity', $quantity);
        } else {
            $cart->items()->create([
                'bag_id' => $validated['product_id'],
                'quantity' => $quantity
            ]);
        }

        return redirect()->back()->with('success', 'Item added to cart successfully!');
    }

    public function remove($itemId)
    {
        $cartItem = CartItem::findOrFail($itemId);
        $cartItem->delete();
        return back()->with('success', 'Item removed from cart!');
    }

    public function update(Request $request, $itemId)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cartItem = CartItem::findOrFail($itemId);
        $cartItem->update(['quantity' => $validated['quantity']]);

        return back()->with('success', 'Cart updated successfully!');
    }
}
