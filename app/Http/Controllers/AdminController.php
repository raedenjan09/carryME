<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;
use App\Models\Bag;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function dashboard()
    {
        // Get basic statistics for the admin dashboard
        $stats = [
            'total_users' => User::where('role', '!=', 'admin')->count(),
            'total_orders' => Order::count(),
            'total_products' => Bag::count(),
            'recent_orders' => Order::with('user')
                                ->latest()
                                ->take(5)
                                ->get(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}