<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to BagXury</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-2xl font-bold text-gray-800">BagXury</h1>
                </div>
                <div class="flex items-center space-x-4">
                    @auth
                        @if(auth()->user()->role === 'admin')
                            <a href="{{ route('admin.dashboard') }}" class="text-gray-700 hover:text-gray-900">Dashboard</a>
                        @else
                            <a href="{{ route('user.dashboard') }}" class="text-gray-700 hover:text-gray-900">My Account</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-700 hover:text-gray-900">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Sign In</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="relative bg-white overflow-hidden">
        <div class="max-w-7xl mx-auto">
            <div class="relative z-10 pb-8 bg-white sm:pb-16 md:pb-20 lg:max-w-2xl lg:w-full lg:pb-28 xl:pb-32">
                <main class="mt-10 mx-auto max-w-7xl px-4 sm:mt-12 sm:px-6 md:mt-16 lg:mt-20 lg:px-8 xl:mt-28">
                    <div class="sm:text-center lg:text-left">
                        <h1 class="text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
                            <span class="block">Discover Luxury</span>
                            <span class="block text-indigo-600">Handbags Collection</span>
                        </h1>
                        <p class="mt-3 text-base text-gray-500 sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0">
                            Experience the epitome of fashion with our exclusive collection of luxury handbags. Each piece is carefully curated to bring you the finest in style and craftsmanship.
                        </p>
                        <div class="mt-5 sm:mt-8 sm:flex sm:justify-center lg:justify-start">
                            <div class="rounded-md shadow">
                                <!-- Shop Now Button -->
                                <a href="{{ route('user.dashboard') }}" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 md:py-4 md:text-lg md:px-10">
                                    Shop Now
                                </a>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
        <div class="lg:absolute lg:inset-y-0 lg:right-0 lg:w-1/2">
            <!-- Use leather.jpg as the Hero Section image -->
            <img class="h-56 w-full object-cover sm:h-72 md:h-96 lg:w-full lg:h-full" src="{{ asset('images/bags/leather.jpg') }}" alt="Luxury Leather Bag">
        </div>
    </div>

    <!-- Featured Products Section -->
    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="lg:text-center">
                <h2 class="text-base text-indigo-600 font-semibold tracking-wide uppercase">Featured Products</h2>
                <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                    Explore Our Bestsellers
                </p>
            </div>

            <div class="mt-10">
                <div class="grid grid-cols-1 gap-10 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($products as $product)
                        <div class="bg-white p-6 rounded-lg shadow">
                            <img class="h-48 w-full object-cover rounded-md" src="{{ asset('images/' . $product->image) }}" alt="{{ $product->name }}">
                            <h3 class="mt-4 text-lg font-medium text-gray-900">{{ $product->name }}</h3>
                            <p class="mt-2 text-base text-gray-500">{{ Str::limit($product->description, 100) }}</p>
                            <div class="mt-4 flex justify-between items-center">
                                <span class="text-indigo-600 font-bold">${{ number_format($product->price, 2) }}</span>
                                <a href="#" class="text-indigo-600 hover:text-indigo-800 font-medium">View Details</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <p class="text-base text-gray-400">&copy; 2024 BagXury. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Welcome Section -->
    <div class="text-center py-12">
        <h1>Welcome to BagXury</h1>
        <p>Discover our exclusive collection of luxury bags.</p>
        @auth
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="text-gray-700 hover:text-gray-900">Logout</button>
            </form>
        @endauth
    </div>
</body>
</html>