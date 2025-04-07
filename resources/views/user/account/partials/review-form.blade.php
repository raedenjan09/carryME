<form action="{{ route('user.reviews.store', $item->order) }}" method="POST">
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