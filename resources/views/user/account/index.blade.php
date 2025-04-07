@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <!-- Sidebar Navigation -->
        <div class="col-md-3">
            <div class="card">
                <div class="list-group list-group-flush">
                    <a href="{{ route('user.account') }}" class="list-group-item list-group-item-action active">
                        <i class="bi bi-person"></i> Profile
                    </a>
                    <a href="{{ route('user.account.orders') }}" class="list-group-item list-group-item-action">
                        <i class="bi bi-box"></i> My Orders
                    </a>
                    <a href="{{ route('user.account.reviews') }}" class="list-group-item list-group-item-action">
                        <i class="bi bi-star"></i> My Reviews
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">My Profile</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <img src="{{ $user->profile_picture_url }}" 
                             alt="Profile Picture" 
                             class="rounded-circle mb-3"
                             style="width: 150px; height: 150px; object-fit: cover;">
                    </div>
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="mb-3">
                        <strong>Name:</strong>
                        <p>{{ $user->name }}</p>
                    </div>

                    <div class="mb-3">
                        <strong>Email:</strong>
                        <p>{{ $user->email }}</p>
                    </div>

                    <div class="mb-3">
                        <strong>Phone:</strong>
                        <p>{{ $user->phone ?? 'Not provided' }}</p>
                    </div>

                    <div class="mb-3">
                        <strong>Address:</strong>
                        <p>{{ $user->address ?? 'Not provided' }}</p>
                    </div>

                    <a href="{{ route('user.account.edit') }}" class="btn btn-primary">
                        Edit Profile
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Recent Orders</h5>
                </div>
                <div class="card-body">
                    @if($orders->isEmpty())
                        <p>No orders found.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Date</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                    <tr>
                                        <td>#{{ $order->id }}</td>
                                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                                        <td>${{ number_format($order->total, 2) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $order->status === 'pending' ? 'warning' : 
                                                ($order->status === 'processing' ? 'info' : 
                                                ($order->status === 'shipped' ? 'primary' : 
                                                ($order->status === 'delivered' ? 'success' : 'danger'))) }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">My Reviews</h5>
                </div>
                <div class="card-body">
                    @forelse(auth()->user()->reviews()->latest()->get() as $review)
                        <div class="border-bottom mb-3 pb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6>{{ $review->bag->name }}</h6>
                                <small class="text-muted">{{ $review->created_at->format('M d, Y') }}</small>
                            </div>
                            <div class="rating-display mb-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bi bi-star{{ $i <= $review->rating ? '-fill' : '' }} text-warning"></i>
                                @endfor
                            </div>
                            <p class="mb-1">{{ $review->comment }}</p>
                            <small class="text-muted">Posted as: {{ $review->is_anonymous ? 'Anonymous' : 'Verified Purchase' }}</small>
                        </div>
                    @empty
                        <p>No reviews yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection