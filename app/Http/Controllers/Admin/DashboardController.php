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
            // Get the last 6 months for chart data
            $months = collect(range(5, 0))->map(function ($i) {
                return Carbon::now()->startOfMonth()->subMonths($i);
            });

            // Fetch sales data for the last 6 months
            $salesData = $months->map(function ($month) {
                return [
                    'month' => $month->format('M Y'),
                    'total' => Order::whereYear('created_at', $month->year)
                        ->whereMonth('created_at', $month->month)
                        ->sum('total') ?? 0,
                ];
            })->values()->toArray();

            // Fetch user growth data for the last 6 months
            $userGrowthData = $months->map(function ($month) {
                return [
                    'month' => $month->format('M Y'),
                    'count' => User::whereYear('created_at', $month->year)
                        ->whereMonth('created_at', $month->month)
                        ->count() ?? 0,
                ];
            })->values()->toArray();

            // Get dashboard statistics
            $data = [
                'totalSales' => Order::sum('total') ?? 0,
                'totalUsers' => User::count() ?? 0,
                'totalProducts' => Bag::count() ?? 0,
                'totalOrders' => Order::count() ?? 0,
                'recentOrders' => Order::with(['user', 'items.bag'])
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
                'salesData' => $salesData,
                'userGrowthData' => $userGrowthData
            ];

            Log::info('Dashboard Data Loaded Successfully', [
                'total_sales' => $data['totalSales'],
                'total_users' => $data['totalUsers'],
                'total_products' => $data['totalProducts'],
                'total_orders' => $data['totalOrders']
            ]);

            return view('admin.dashboard', $data);

        } catch (\Exception $e) {
            Log::error('Dashboard Error: ' . $e->getMessage());
            
            return view('admin.dashboard', [
                'error' => 'Error loading dashboard data: ' . $e->getMessage(),
                'totalSales' => 0,
                'totalUsers' => 0,
                'totalProducts' => 0,
                'totalOrders' => 0,
                'recentOrders' => collect([]),
                'recentUsers' => collect([]),
                'salesData' => [],
                'userGrowthData' => []
            ]);
        }
    }
}
