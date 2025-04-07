<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $cart = auth()->user()->cart;
        
        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')
                           ->with('error', 'Your cart is empty');
        }

        return view('checkout.index', compact('cart'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'shipping_address' => 'required|string',
            'shipping_city' => 'required|string',
            'shipping_state' => 'nullable|string',
            'shipping_zipcode' => 'nullable|string',
            'payment_method' => 'required|in:credit_card,paypal'
        ]);

        $cart = auth()->user()->cart;
        
        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')
                           ->with('error', 'Your cart is empty');
        }

        try {
            \DB::beginTransaction();
            
            // Calculate total
            $total = $cart->items->sum(function($item) {
                return $item->bag->price * $item->quantity;
            });

            // Log the order data for debugging
            Log::info('Creating order with data:', [
                'user_id' => auth()->id(),
                'total' => $total,
                'shipping_info' => $validated,
                'cart_items' => $cart->items->toArray()
            ]);

            // Create order
            $order = Order::create([
                'user_id' => auth()->id(),
                'total' => $total,
                'shipping_address' => $validated['shipping_address'],
                'shipping_city' => $validated['shipping_city'],
                'shipping_state' => $validated['shipping_state'] ?? '',
                'shipping_zipcode' => $validated['shipping_zipcode'] ?? '',
                'payment_method' => $validated['payment_method'],
                'status' => 'pending'
            ]);

            // Create order items - Modified this section
            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'bag_id' => $item->bag_id,
                    'quantity' => $item->quantity,
                    'price' => $item->bag->price
                ]);
            }

            // Clear cart after successful order creation
            $cart->items()->delete();
            $cart->delete();

            \DB::commit();
            
            return redirect()->route('checkout.success', $order->id)
                            ->with('success', 'Order placed successfully!');

        } catch (\Exception $e) {
            \DB::rollBack();
            
            // Log the error
            Log::error('Order creation failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'cart_items' => $cart->items->toArray() // Added for debugging
            ]);

            return back()->with('error', 'Order processing failed: ' . $e->getMessage())
                        ->withInput();
        }
    }

    public function success($orderId)
    {
        $order = Order::findOrFail($orderId);
        return view('checkout.success', compact('order'));
    }
}
