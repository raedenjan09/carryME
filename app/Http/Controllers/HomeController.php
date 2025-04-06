<?php
// filepath: /c:/Users/raede/BagXury/app/Http/Controllers/HomeController.php


namespace App\Http\Controllers;

use App\Models\Bag;

class HomeController extends Controller
{
    public function index()
    {
        // Fetch the latest products
        $products = Bag::latest()->take(6)->get(); // Adjust the number of products as needed

        // Pass the products to the view
        return view('home', compact('products'));
    }
}