<!-- filepath: /c:/Users/raede/BagXury/resources/views/user/product-details.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row">
        <!-- Product Details -->
        <div class="col-md-6">
            <img src="{{ asset($product->primaryImage?->image_path ?? 'images/placeholder.jpg') }}" 
                 class="img-fluid rounded" alt="{{ $product->name }}">
        </div>
        <div class="col-md-6">
            <h1>{{ $product->name }}</h1>
            <p class="text-muted">{{ $product->description }}</p>
            <h3 class="text-primary">${{ number_format($product->price, 2) }}</h3>
            <div class="mb-4">
                <strong>Average Rating:</strong> 
                {{ number_format($product->average_rating, 1) }} / 5.0
                ({{ $product->reviews->count() }} reviews)
            </div>
        </div>
    </div>

    <!-- Reviews Section -->
    <div class="row mt-5">
        <div class="col-12">
            <h3>Customer Reviews</h3>
            
            @auth
                <form action="{{ route('products.review', $product->id) }}" method="POST" class="mb-4">
                    @csrf
                    <div class="mb-3">
                        <label for="rating" class="form-label">Rating</label>
                        <select name="rating" id="rating" class="form-select" required>
                            <option value="">Select Rating</option>
                            @for($i = 5; $i >= 1; $i--)
                                <option value="{{ $i }}">{{ $i }} Stars</option>
                            @endfor
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="comment" class="form-label">Your Review</label>
                        <textarea name="comment" id="comment" rows="3" 
                                  class="form-control" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit Review</button>
                </form>
            @else
                <p>Please <a href="{{ route('login') }}">login</a> to leave a review.</p>
            @endauth

            <div class="reviews mt-4">
                @forelse($product->reviews as $review)
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <h5 class="card-title">{{ $review->user->name }}</h5>
                                <div class="text-warning">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $review->rating)
                                            ★
                                        @else
                                            ☆
                                        @endif
                                    @endfor
                                </div>
                            </div>
                            <p class="card-text">{{ $review->comment }}</p>
                            <small class="text-muted">
                                Posted {{ $review->created_at->diffForHumans() }}
                            </small>
                        </div>
                    </div>
                @empty
                    <p>No reviews yet. Be the first to review this product!</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection