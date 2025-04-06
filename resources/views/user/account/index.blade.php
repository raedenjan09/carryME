@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}" 
                                 class="rounded-circle" width="50" height="50" alt="Profile">
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">{{ auth()->user()->name }}</h6>
                            <small class="text-muted">Member since {{ auth()->user()->created_at->format('M Y') }}</small>
                        </div>
                    </div>
                    
                    <div class="list-group list-group-flush">
                        <a href="#profile" class="list-group-item list-group-item-action active" data-bs-toggle="list">
                            <i class="bi bi-person me-2"></i> Profile
                        </a>
                        <a href="#orders" class="list-group-item list-group-item-action" data-bs-toggle="list">
                            <i class="bi bi-bag me-2"></i> Orders
                        </a>
                        <a href="#reviews" class="list-group-item list-group-item-action" data-bs-toggle="list">
                            <i class="bi bi-star me-2"></i> Reviews
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <div class="tab-content">
                <!-- Profile Section -->
                <div class="tab-pane fade show active" id="profile">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">Profile Information</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('user.profile.update') }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="{{ old('name', auth()->user()->name) }}" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="{{ old('email', auth()->user()->email) }}" required>
                                </div>
                                
                                <h6 class="mb-3">Change Password</h6>
                                
                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Current Password</label>
                                    <input type="password" class="form-control" id="current_password" name="current_password">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="password" class="form-label">New Password</label>
                                    <input type="password" class="form-control" id="password" name="password">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control" id="password_confirmation" 
                                           name="password_confirmation">
                                </div>
                                
                                <button type="submit" class="btn btn-primary">Update Profile</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Orders Section -->
                <div class="tab-pane fade" id="orders">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">Order History</h5>
                        </div>
                        <div class="card-body">
                            @forelse($orders as $order)
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div>
                                                <h6 class="mb-1">Order #{{ $order->id }}</h6>
                                                <small class="text-muted">
                                                    Placed on {{ $order->created_at->format('M d, Y') }}
                                                </small>
                                            </div>
                                            <span class="badge bg-{{ $order->status === 'completed' ? 'success' : 'warning' }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </div>
                                        
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Item</th>
                                                        <th>Quantity</th>
                                                        <th>Price</th>
                                                        <th>Subtotal</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($order->items as $item)
                                                        <tr>
                                                            <td>{{ $item->bag->name }}</td>
                                                            <td>{{ $item->quantity }}</td>
                                                            <td>${{ number_format($item->price, 2) }}</td>
                                                            <td>${{ number_format($item->quantity * $item->price, 2) }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                                        <td><strong>${{ number_format($order->total, 2) }}</strong></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-4">
                                    <i class="bi bi-bag x-lg mb-3 d-block"></i>
                                    <p class="text-muted">No orders yet.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Reviews Section -->
                <div class="tab-pane fade" id="reviews">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">My Reviews</h5>
                        </div>
                        <div class="card-body">
                            @forelse($reviews as $review)
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1">{{ $review->bag->name }}</h6>
                                                <div class="text-warning mb-2">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        @if($i <= $review->rating)
                                                            ★
                                                        @else
                                                            ☆
                                                        @endif
                                                    @endfor
                                                </div>
                                                <p class="mb-1">{{ $review->comment }}</p>
                                                <small class="text-muted">
                                                    Posted on {{ $review->created_at->format('M d, Y') }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted">No reviews yet.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle tab navigation from URL hash
    let hash = window.location.hash;
    if (hash) {
        let tab = document.querySelector(`[href="${hash}"]`);
        if (tab) {
            tab.click();
        }
    }
});
</script>
@endpush