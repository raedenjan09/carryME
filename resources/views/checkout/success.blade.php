@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="card mx-auto" style="max-width: 600px;">
        <div class="card-body">
            <div class="text-center mb-4">
                <i class="bi bi-check-circle text-success" style="font-size: 4rem;"></i>
                <h2 class="mt-2">Order Confirmed!</h2>
                <p class="text-muted">Order #{{ $order->id }}</p>
            </div>

            <div class="border-bottom pb-3 mb-3">
                <div class="row">
                    <div class="col-6">
                        <h6 class="text-muted">Order Date</h6>
                        <p>{{ $order->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                    <div class="col-6">
                        <h6 class="text-muted">Status</h6>
                        <span class="badge bg-success">{{ ucfirst($order->status) }}</span>
                    </div>
                </div>
            </div>

            <div class="border-bottom pb-3 mb-3">
                <h6 class="text-muted">Shipping Address</h6>
                <p class="mb-0">{{ $order->shipping_address }}</p>
                <p class="mb-0">{{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_zipcode }}</p>
            </div>

            <div class="border-bottom pb-3 mb-3">
                <h6 class="text-muted">Order Items</h6>
                @foreach($order->items as $item)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <p class="mb-0">{{ $item->bag->name }}</p>
                            <small class="text-muted">Quantity: {{ $item->quantity }}</small>
                        </div>
                        <span>${{ number_format($item->price * $item->quantity, 2) }}</span>
                    </div>
                @endforeach
            </div>

            <div class="d-flex justify-content-between">
                <h5>Total</h5>
                <h5>${{ number_format($order->total, 2) }}</h5>
            </div>

            <div class="text-center mt-4">
                <a href="{{ route('user.dashboard') }}" class="btn btn-primary">
                    Continue Shopping
                </a>
            </div>
        </div>
    </div>
</div>
@endsection