<!-- filepath: /c:/Users/raede/BagXury/resources/views/user/cart.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h2 class="mb-4">Shopping Cart</h2>

    @if($cart && $cart->items->count() > 0)
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cart->items as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ asset('images/bags/' . ($item->bag->primaryImage?->image_path ?? 'placeholder.jpg')) }}" 
                                                 alt="{{ $item->bag->name }}" 
                                                 class="me-3" 
                                                 style="width: 50px; height: 50px; object-fit: cover;">
                                            <div>
                                                <h6 class="mb-0">{{ $item->bag->name }}</h6>
                                                <small class="text-muted">{{ $item->bag->category->name ?? 'Uncategorized' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>${{ number_format($item->bag->price, 2) }}</td>
                                    <td>
                                        <form action="{{ route('cart.update', $item->id) }}" 
                                              method="POST" 
                                              class="d-flex align-items-center">
                                            @csrf
                                            @method('PUT')
                                            <input type="number" 
                                                   name="quantity" 
                                                   value="{{ $item->quantity }}" 
                                                   min="1" 
                                                   class="form-control form-control-sm" 
                                                   style="width: 70px;"
                                                   onchange="this.form.submit()">
                                        </form>
                                    </td>
                                    <td>${{ number_format($item->bag->price * $item->quantity, 2) }}</td>
                                    <td>
                                        <form action="{{ route('cart.remove', $item->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-link text-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                <td colspan="2">
                                    <strong>
                                        ${{ number_format($cart->items->sum(function($item) {
                                            return $item->bag->price * $item->quantity;
                                        }), 2) }}
                                    </strong>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="text-end mt-3">
                    <a href="{{ route('checkout') }}" class="btn btn-primary">
                        Proceed to Checkout
                        <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-5">
            <i class="bi bi-cart-x display-1 text-muted"></i>
            <p class="mt-3">Your cart is empty.</p>
            <a href="{{ route('home') }}" class="btn btn-primary mt-3">
                Continue Shopping
            </a>
        </div>
    @endif
</div>
@endsection