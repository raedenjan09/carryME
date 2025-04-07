@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Add New Bag</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.bags.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                           id="name" name="name" value="{{ old('name') }}">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" name="description">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="price" class="form-label">Price</label>
                    <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" 
                           id="price" name="price" value="{{ old('price') }}">
                    @error('price')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="category_id" class="form-label">Category</label>
                    <select name="category_id" id="category_id" class="form-control @error('category_id') is-invalid @enderror" required>
                        <option value="">Select a category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="stock" class="form-label">Stock</label>
                    <input type="number" 
                           class="form-control @error('stock') is-invalid @enderror" 
                           id="stock" 
                           name="stock" 
                           value="{{ old('stock', 0) }}" 
                           min="0" 
                           required>
                    @error('stock')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Image Upload Section -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Upload Images</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Select Images</label>
                            <input type="file" 
                                   class="form-control @error('images.*') is-invalid @enderror" 
                                   id="images" 
                                   name="images[]" 
                                   multiple 
                                   accept="image/*"
                                   onchange="previewImages(this)">
                            <small class="text-muted">You can select multiple images at once. First image will be set as primary.</small>
                            @error('images.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Image Preview Section -->
                        <div id="imagePreview" class="row mt-3"></div>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.bags.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Create Bag</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function previewImages(input) {
    const preview = document.getElementById('imagePreview');
    preview.innerHTML = ''; // Clear existing previews

    if (input.files && input.files.length > 0) {
        Array.from(input.files).forEach((file, index) => {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const col = document.createElement('div');
                col.className = 'col-md-3 col-sm-6 mb-3';
                
                col.innerHTML = `
                    <div class="position-relative">
                        <img src="${e.target.result}" class="img-thumbnail" alt="Preview">
                        <span class="position-absolute top-0 start-0 badge ${index === 0 ? 'bg-primary' : 'bg-secondary'} m-2">
                            ${index === 0 ? 'Primary' : `Image ${index + 1}`}
                        </span>
                    </div>
                `;
                
                preview.appendChild(col);
            };

            reader.readAsDataURL(file);
        });
    }
}
</script>
@endpush
@endsection