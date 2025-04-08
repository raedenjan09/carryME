<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Bag;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            // Get stats for cards
            $data = [
                'totalSales' => Order::sum('total'),
                'totalUsers' => User::count(),
                'totalProducts' => Bag::count(),
                'totalOrders' => Order::count(),
                'recentOrders' => Order::with(['user'])
                    ->latest()
                    ->take(5)
                    ->get(),
                'recentUsers' => User::latest()
                    ->take(5)
                    ->get(),
                'recentBags' => Bag::with(['images' => function($query) {
                    $query->where('is_primary', true);
                }])
                ->latest()
                ->take(5)
                ->get(),
            ];

            return view('admin.dashboard', $data);

        } catch (\Exception $e) {
            Log::error('Dashboard Error: ' . $e->getMessage());
            return view('admin.dashboard', [
                'error' => 'Error loading dashboard data'
            ]);
        }
    }
}
