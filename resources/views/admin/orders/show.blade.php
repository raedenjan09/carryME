@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Order #{{ $order->id }}</h2>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Orders
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Order Items</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr>
                                <td>{{ $item->bag->name }}</td>
                                <td>${{ number_format($item->price, 2) }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>${{ number_format($item->price * $item->quantity, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                <td>${{ number_format($order->total, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Order Details</h5>
                </div>
                <div class="card-body">
                    <p><strong>Status:</strong> 
                        <span class="badge bg-{{ $order->status === 'pending' ? 'warning' : 
                            ($order->status === 'processing' ? 'info' : 
                            ($order->status === 'shipped' ? 'primary' : 
                            ($order->status === 'delivered' ? 'success' : 'danger'))) }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </p>
                    <p><strong>Customer:</strong> {{ $order->user->name }}</p>
                    <p><strong>Email:</strong> {{ $order->user->email }}</p>
                    <p><strong>Payment Method:</strong> {{ ucfirst($order->payment_method) }}</p>
                    <p><strong>Order Date:</strong> {{ $order->created_at->format('M d, Y H:i') }}</p>
                    
                    <hr>
                    
                    <h6>Shipping Address</h6>
                    <p class="mb-0">{{ $order->shipping_address }}</p>
                    <p class="mb-0">{{ $order->shipping_city }}</p>
                    <p class="mb-0">{{ $order->shipping_country }}</p>
                    <p>{{ $order->shipping_postal_code }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection