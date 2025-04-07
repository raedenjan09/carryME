@extends('layouts.app')

@section('content')
<!-- Hero Section with Parallax Effect -->
<div class="hero-banner position-relative">
    <div class="hero-overlay"></div>
    <div class="container position-relative">
        <div class="text-center text-white py-5">
            <h1 class="display-4 fw-bold mb-4">Premium Bags Collection</h1>
            <p class="lead mb-4">Discover our handcrafted bags for every occasion</p>
            <a href="#products" class="btn btn-primary btn-lg px-5 py-3">
                Shop Now
                <i class="bi bi-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</div>

<!-- Categories Section with Hover Effects -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-5 position-relative">
            Shop by Category
            <span class="position-absolute start-50 translate-middle-x bottom-0 border-2 border-primary border-bottom" style="width: 50px;"></span>
        </h2>
        <div class="row g-4">
            @foreach($categories as $category)
                <div class="col-6 col-md-3">
                    <div class="category-card position-relative overflow-hidden rounded-3">
                        <img src="{{ asset('images/categories/' . $category->slug . '.jpg') }}" 
                             class="img-fluid w-100" alt="{{ $category->name }}">
                        <div class="category-overlay">
                            <h5 class="text-white mb-0">{{ $category->name }}</h5>
                        </div>
                        <a href="{{ url('/') }}?category_id={{ $category->id }}" 
                           class="stretched-link"></a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Products Grid -->
<div class="container my-5" id="products">
    <h2 class="text-center mb-5 position-relative">
        Our Products
        <span class="position-absolute start-50 translate-middle-x bottom-0 border-2 border-primary border-bottom" style="width: 50px;"></span>
    </h2>

    @if($products->isEmpty())
        <div class="alert alert-warning text-center p-5">
            <i class="bi bi-exclamation-circle display-1"></i>
            <p class="mt-3">No products available at the moment.</p>
        </div>
    @else
        <div class="row g-4">
            @foreach($products as $product)
                <div class="col-md-4">
                    <div class="card product-card h-100 border-0 shadow-sm">
                        <div class="position-relative">
                            <img 
                                src="{{ asset('storage/' . ($product->primaryImage?->image_path ?? 'bags/placeholder.jpg')) }}" 
                                alt="{{ $product->name }}"
                                class="w-full h-48 object-cover"
                                onerror="this.src='{{ asset('storage/bags/placeholder.jpg') }}'"
                            >
                            @auth
                                <form action="{{ route('cart.add') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-cart-plus"></i> Add to Cart
                                    </button>
                                </form>
                            @endauth
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">{{ $product->name }}</h5>
                            <p class="card-text text-muted">{{ Str::limit($product->description, 100) }}</p>
                            <p class="card-text"><strong>Category:</strong> {{ $product->category->name ?? 'Uncategorized' }}</p>
                            <h4 class="text-primary mb-3">${{ number_format($product->price, 2) }}</h4>
                            <a href="{{ route('product.show', $product->id) }}" 
                               class="btn btn-outline-primary w-100">View Details</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    .hero-banner {
        min-height: 600px;
        background: url('{{ asset('images/hero-bg.jpg') }}') center/cover no-repeat fixed;
        display: flex;
        align-items: center;
    }

    .hero-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
    }

    .category-card {
        transition: all 0.3s ease;
        height: 200px;
    }

    .category-card:hover {
        transform: translateY(-5px);
    }

    .category-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 1rem;
        background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
        transition: all 0.3s ease;
    }

    .product-card {
        transition: all 0.3s ease;
    }

    .product-card:hover {
        transform: translateY(-5px);
    }

    .product-card img {
        height: 250px;
        object-fit: cover;
    }

    .btn-primary {
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
</style>
@endpush

