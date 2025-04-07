@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4">Edit Bag</h1>

    <div class="card shadow">
        <div class="card-body">
            <form action="{{ route('admin.bags.update', $bag) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <!-- Basic Information -->
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                           id="name" name="name" value="{{ old('name', $bag->name) }}">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" name="description">{{ old('description', $bag->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="price" class="form-label">Price</label>
                    <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" 
                           id="price" name="price" value="{{ old('price', $bag->price) }}">
                    @error('price')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Image Management Section -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Image Management</h5>
                    </div>
                    <div class="card-body">
                        <!-- Current Images Gallery -->
                        <label class="form-label">Current Images</label>
                        <div class="row mb-3">
                            @forelse($bag->images as $image)
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <div class="position-relative">
                                        <img src="{{ asset($image->image_path) }}" 
                                             class="img-thumbnail" 
                                             alt="Bag Image">
                                        @if($image->is_primary)
                                            <span class="position-absolute top-0 start-0 badge bg-primary m-2">
                                                Primary
                                            </span>
                                        @endif
                                        <div class="position-absolute top-0 end-0 m-2">
                                            <button type="button" 
                                                    class="btn btn-danger btn-sm"
                                                    onclick="deleteImage({{ $image->id }})">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            @if(!$image->is_primary)
                                                <button type="button" 
                                                        class="btn btn-primary btn-sm"
                                                        onclick="makePrimary({{ $image->id }})">
                                                    <i class="bi bi-star"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <p class="text-muted">No images uploaded yet</p>
                                </div>
                            @endforelse
                        </div>

                        <!-- Image Upload Options -->
                        <div class="mb-3">
                            <label class="form-label">Add New Images</label>
                            <div class="input-group mb-3">
                                <input type="file" 
                                       class="form-control @error('images.*') is-invalid @enderror" 
                                       id="images" 
                                       name="images[]" 
                                       multiple 
                                       accept="image/*">
                                <button class="btn btn-outline-secondary" 
                                        type="button" 
                                        onclick="document.getElementById('images').click()">
                                    Browse Files
                                </button>
                            </div>
                            <div id="imagePreview" class="row mt-2"></div>
                            @error('images.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.bags.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Bag</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Image preview functionality
    document.getElementById('images').addEventListener('change', function(event) {
        const preview = document.getElementById('imagePreview');
        preview.innerHTML = '';
        
        Array.from(event.target.files).forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'col-md-3 col-sm-6 mb-3';
                div.innerHTML = `
                    <div class="position-relative">
                        <img src="${e.target.result}" class="img-thumbnail" alt="Preview">
                        <span class="position-absolute top-0 start-0 badge bg-info m-2">New</span>
                    </div>
                `;
                preview.appendChild(div);
            }
            reader.readAsDataURL(file);
        });
    });

    // Delete image function
    function deleteImage(imageId) {
        if (confirm('Are you sure you want to delete this image?')) {
            // Add your delete logic here using AJAX
            fetch(`/admin/bag-images/${imageId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        }
    }

    // Make image primary function
    function makePrimary(imageId) {
        fetch(`/admin/bag-images/${imageId}/make-primary`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
</script>
@endpush
@endsection

<?php
public function edit(Bag $bag)
{
    $categories = \App\Models\Category::all(); // Fetch all categories
    return view('admin.bags.edit', compact('bag', 'categories'));
}