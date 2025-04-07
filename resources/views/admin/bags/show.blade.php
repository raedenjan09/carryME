<div class="row mt-4">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4>Product Statistics</h4>
                        <p class="mb-1">
                            <i class="bi bi-cart-check"></i> 
                            {{ $bag->orders()->count() }} customers bought this product
                        </p>
                        <p class="mb-1">
                            <i class="bi bi-star"></i> 
                            {{ number_format($bag->average_rating, 1) }} average rating
                        </p>
                        <p class="mb-0">
                            <i class="bi bi-chat-text"></i> 
                            {{ $bag->reviews()->count() }} customer reviews
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Customer Reviews</h5>
            </div>
            <div class="card-body">
                @forelse($bag->reviews()->latest()->get() as $review)
                    <div class="review-item border-bottom pb-3 mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6>{{ $review->is_anonymous ? 'Anonymous User' : 'Verified Buyer' }}</h6>
                                <div class="rating-display">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="bi bi-star{{ $i <= $review->rating ? '-fill' : '' }} text-warning"></i>
                                    @endfor
                                </div>
                            </div>
                            <small class="text-muted">{{ $review->created_at->format('M d, Y') }}</small>
                        </div>
                        <p class="mt-2 mb-0">{{ $review->comment }}</p>
                    </div>
                @empty
                    <p class="text-muted mb-0">No reviews yet</p>
                @endforelse
            </div>
        </div>
    </div>
</div>