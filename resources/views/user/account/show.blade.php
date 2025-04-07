@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2>My Orders</h2>
    
    @foreach($orders as $order)
        <div class="card mb-4">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Order #{{ $order->id }}</h5>
                    <span class="badge bg-{{ $order->status === 'delivered' ? 'success' : 'primary' }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                @foreach($order->items as $item)
                    <div class="border-bottom pb-3 mb-3">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6>{{ $item->bag->name }}</h6>
                                <p class="mb-0">Quantity: {{ $item->quantity }}</p>
                                <p class="mb-0">Price: ${{ number_format($item->price, 2) }}</p>
                            </div>
                            
                            @if($order->status === 'delivered' && !$item->hasReview())
                                <button class="btn btn-primary btn-sm" 
                                        data-bs-toggle="collapse" 
                                        data-bs-target="#review-form-{{ $item->id }}">
                                    Write Review
                                </button>
                            @endif
                        </div>

                        @if($order->status === 'delivered' && !$item->hasReview())
                            <div class="collapse mt-3" id="review-form-{{ $item->id }}">
                                <form action="{{ route('user.reviews.store', $order) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="bag_id" value="{{ $item->bag_id }}">
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Rating</label>
                                        <div class="star-rating">
                                            @for($i = 5; $i >= 1; $i--)
                                                <input type="radio" name="rating" value="{{ $i }}" 
                                                       id="star{{ $i }}_{{ $item->id }}" required>
                                                <label for="star{{ $i }}_{{ $item->id }}">
                                                    <i class="bi bi-star-fill"></i>
                                                </label>
                                            @endfor
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Your Review</label>
                                        <textarea name="comment" class="form-control" required 
                                                  rows="3" minlength="10"></textarea>
                                    </div>

                                    <div class="mb-3 form-check">
                                        <input type="checkbox" name="is_anonymous" class="form-check-input" 
                                               id="anonymous_{{ $item->id }}">
                                        <label class="form-check-label" for="anonymous_{{ $item->id }}">
                                            Post anonymously
                                        </label>
                                    </div>

                                    <button type="submit" class="btn btn-primary">Submit Review</button>
                                </form>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</div>
@endsection