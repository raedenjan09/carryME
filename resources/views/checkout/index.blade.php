@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row">
        <!-- Order Summary -->
        <div class="col-md-4 order-md-2 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Order Summary</h4>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @foreach($cart->items as $item)
                            <li class="list-group-item d-flex justify-content-between lh-sm">
                                <div>
                                    <h6 class="my-0">{{ $item->bag->name }}</h6>
                                    <small class="text-muted">Quantity: {{ $item->quantity }}</small>
                                </div>
                                <span class="text-muted">${{ number_format($item->bag->price * $item->quantity, 2) }}</span>
                            </li>
                        @endforeach
                        <li class="list-group-item d-flex justify-content-between">
                            <strong>Total</strong>
                            <strong>${{ number_format($cart->items->sum(function($item) {
                                return $item->bag->price * $item->quantity;
                            }), 2) }}</strong>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Checkout Form -->
        <div class="col-md-8 order-md-1">
            <h4 class="mb-3">Shipping Information</h4>
            <form action="{{ route('checkout.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-12">
                        <label for="shipping_address" class="form-label">Address</label>
                        <input type="text" class="form-control @error('shipping_address') is-invalid @enderror" 
                               id="shipping_address" name="shipping_address" value="{{ old('shipping_address') }}" required>
                        @error('shipping_address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="shipping_city" class="form-label">City</label>
                        <input type="text" class="form-control @error('shipping_city') is-invalid @enderror" 
                               id="shipping_city" name="shipping_city" value="{{ old('shipping_city') }}" required>
                        @error('shipping_city')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="shipping_state" class="form-label">State</label>
                        <input type="text" class="form-control @error('shipping_state') is-invalid @enderror" 
                               id="shipping_state" name="shipping_state" value="{{ old('shipping_state') }}" required>
                        @error('shipping_state')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="shipping_zipcode" class="form-label">ZIP Code</label>
                        <input type="text" class="form-control @error('shipping_zipcode') is-invalid @enderror" 
                               id="shipping_zipcode" name="shipping_zipcode" value="{{ old('shipping_zipcode') }}" required>
                        @error('shipping_zipcode')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <hr class="my-4">

                <h4 class="mb-3">Payment Method</h4>
                <div class="my-3">
                    <div class="form-check">
                        <input id="credit_card" name="payment_method" type="radio" 
                               class="form-check-input" value="credit_card" checked required>
                        <label class="form-check-label" for="credit_card">Credit Card</label>
                    </div>
                    <div class="form-check">
                        <input id="paypal" name="payment_method" type="radio" 
                               class="form-check-input" value="paypal" required>
                        <label class="form-check-label" for="paypal">PayPal</label>
                    </div>
                </div>

                <button class="w-100 btn btn-primary btn-lg" type="submit">Complete Order</button>
            </form>
        </div>
    </div>
</div>
@endsection