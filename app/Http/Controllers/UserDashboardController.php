<?php

namespace App\Http\Controllers;

use App\Models\Bag;
use App\Models\Category;

class UserDashboardController extends Controller
{
    public function index()
    {
        // Get all categories
        $categories = Category::all();

        // Check if a category filter is applied
        $categoryId = request('category_id');
        if ($categoryId) {
            // Get products filtered by category
            $products = Bag::where('category_id', $categoryId)
                ->with('primaryImage', 'category')
                ->latest()
                ->get();
        } else {
            // Get all products
            $products = Bag::with('primaryImage', 'category')
                ->latest()
                ->get();
        }

        return view('user.dashboard', compact('products', 'categories'));
    }
}
