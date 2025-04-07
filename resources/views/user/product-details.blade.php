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