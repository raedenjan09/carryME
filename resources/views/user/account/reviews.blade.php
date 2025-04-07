@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-3">
            @include('user.account.partials.sidebar')
        </div>

        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">My Reviews</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @forelse($reviews as $review)
                        <div class="review-item border-bottom pb-3 mb-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6>{{ $review->bag->name }}</h6>
                                    <div class="rating-display mb-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="bi bi-star{{ $i <= $review->rating ? '-fill' : '' }} text-warning"></i>
                                        @endfor
                                    </div>
                                    <p class="mb-1">{{ $review->comment }}</p>
                                    <small class="text-muted">
                                        Posted {{ $review->created_at->diffForHumans() }}
                                        {{ $review->is_anonymous ? '(Anonymous)' : '' }}
                                    </small>
                                </div>
                                <button class="btn btn-sm btn-primary" data-bs-toggle="collapse" 
                                        data-bs-target="#edit-review-{{ $review->id }}">
                                    Edit Review
                                </button>
                            </div>

                            <div class="collapse mt-3" id="edit-review-{{ $review->id }}">
                                <form action="{{ route('user.reviews.update', $review) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Rating</label>
                                        <div class="star-rating">
                                            @for($i = 5; $i >= 1; $i--)
                                                <input type="radio" name="rating" value="{{ $i }}" 
                                                    id="edit-star-{{ $review->id }}-{{ $i }}"
                                                    {{ $review->rating == $i ? 'checked' : '' }} required>
                                                <label for="edit-star-{{ $review->id }}-{{ $i }}">★</label>
                                            @endfor
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Your Review</label>
                                        <textarea name="comment" class="form-control" rows="3" 
                                            required minlength="10">{{ $review->comment }}</textarea>
                                    </div>

                                    <div class="mb-3 form-check">
                                        <input type="checkbox" class="form-check-input" 
                                            name="is_anonymous" id="edit-anon-{{ $review->id }}"
                                            {{ $review->is_anonymous ? 'checked' : '' }}>
                                        <label class="form-check-label" for="edit-anon-{{ $review->id }}">
                                            Post anonymously
                                        </label>
                                    </div>

                                    <button type="submit" class="btn btn-primary">Update Review</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">No reviews yet.</p>
                    @endforelse
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