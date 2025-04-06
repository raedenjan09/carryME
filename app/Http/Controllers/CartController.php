<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Bag;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = auth()->user()->cart;
        return view('user.cart', compact('cart'));
    }

    public function add(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:bags,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = auth()->user()->cart ?? Cart::create(['user_id' => auth()->id()]);
        
        $cartItem = $cart->items()->where('bag_id', $validated['product_id'])->first();

        if ($cartItem) {
            $cartItem->increment('quantity', $validated['quantity']);
        } else {
            $cart->items()->create([
                'bag_id' => $validated['product_id'],
                'quantity' => $validated['quantity']
            ]);
        }

        return back()->with('success', 'Item added to cart successfully!');
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
