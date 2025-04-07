@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
            @include('user.account.partials.sidebar')
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">My Orders</h5>
                </div>
                <div class="card-body">
                    @foreach($orders as $order)
                        <div class="order-item mb-4">
                            <!-- Order header -->
                            <div class="d-flex justify-content-between mb-3">
                                <div>
                                    <h6>Order #{{ $order->id }}</h6>
                                    <p class="mb-0">Placed on: {{ $order->created_at->format('M d, Y') }}</p>
                                </div>
                                <span class="badge bg-{{ $order->status === 'delivered' ? 'success' : 'primary' }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </div>

                            <!-- Order items -->
                            @foreach($order->items as $item)
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h6>{{ $item->bag->name }}</h6>
                                                <p class="mb-0">Quantity: {{ $item->quantity }}</p>
                                                <p class="mb-0">Price: ${{ number_format($item->price, 2) }}</p>
                                            </div>
                                            @if($order->status === 'delivered' && !$item->hasReview())
                                                <button type="button" class="btn btn-primary btn-sm"
                                                        data-bs-toggle="collapse"
                                                        data-bs-target="#review-{{ $item->id }}">
                                                    Write Review
                                                </button>
                                            @endif
                                        </div>

                                        @if($order->status === 'delivered' && !$item->hasReview())
                                            <div class="collapse mt-3" id="review-{{ $item->id }}">
                                                <form action="{{ route('user.reviews.store', $order->id) }}" 
                                                      method="POST" 
                                                      class="review-form">
                                                    @csrf
                                                    <input type="hidden" name="bag_id" value="{{ $item->bag_id }}">
                                                    
                                                    <div class="mb-3">
                                                        <label class="form-label">Rating</label>
                                                        <div class="star-rating">
                                                            @for($i = 5; $i >= 1; $i--)
                                                                <input type="radio" name="rating" value="{{ $i }}" 
                                                                    id="star-{{ $item->id }}-{{ $i }}" required>
                                                                <label for="star-{{ $item->id }}-{{ $i }}">★</label>
                                                            @endfor
                                                        </div>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">Your Review</label>
                                                        <textarea name="comment" class="form-control" rows="3" 
                                                                required minlength="10"></textarea>
                                                    </div>

                                                    <div class="mb-3 form-check">
                                                        <input type="checkbox" class="form-check-input" 
                                                               name="is_anonymous" id="anon-{{ $item->id }}">
                                                        <label class="form-check-label" for="anon-{{ $item->id }}">
                                                            Post anonymously
                                                        </label>
                                                    </div>

                                                    <button type="submit" class="btn btn-primary">
                                                        Submit Review
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.star-rating {
    display: flex;
    flex-direction: row-reverse;
    justify-content: flex-start;
}

.star-rating input {
    display: none;
}

.star-rating label {
    cursor: pointer;
    font-size: 24px;
    padding: 0 2px;
    color: #ddd;
}

.star-rating input:checked ~ label,
.star-rating label:hover,
.star-rating label:hover ~ label {
    color: #ffd700;
}
</style>
@endsection