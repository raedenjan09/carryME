<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Bag;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display admin dashboard
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $data = [
            'recentUsers' => User::latest()->take(5)->get(),
            'recentBags' => Bag::latest()->take(5)->get(),
            'recentOrders' => Order::with('user')->latest()->take(5)->get(),
            'totalSales' => Order::sum('total'),
            'totalUsers' => User::count(),
            'totalProducts' => Bag::count(),
            'totalOrders' => Order::count(),
        ];

        return view('admin.dashboard', $data);
    }
}
