<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Bag;
use App\Models\Order;
use Carbon\Carbon;

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
                        ->where('payment_status', 'paid')
                        ->sum('total'),
                ];
            });

            // Fetch user growth data for the last 6 months
            $userGrowthData = $months->map(function ($month) {
                return [
                    'month' => $month->format('M Y'),
                    'count' => User::whereYear('created_at', $month->year)
                        ->whereMonth('created_at', $month->month)
                        ->count(),
                ];
            });

            // Fetch dashboard statistics
            $data = [
                'totalSales' => Order::where('payment_status', 'paid')->sum('total'),
                'totalUsers' => User::count(),

                return [
                    'month' => $month->format('M Y'),
                    'count' => $count
                ];
            });

            // Get dashboard statistics
            $data = [
                'totalSales' => Order::sum('total'),
                'totalUsers' => User::count(),
                'totalProducts' => Bag::count(),
                'totalOrders' => Order::count(),
                'recentOrders' => Order::with('user')
                    ->latest()
                    ->take(5)
                    ->get(),
                'recentUsers' => User::latest()
                    ->take(5)
                    ->get(),
                'salesData' => $salesData,
                'userGrowthData' => $userGrowthData
            ];

            \Log::info('Dashboard Statistics:', $data);

            return view('admin.dashboard', $data);

        } catch (\Exception $e) {
            \Log::error('Dashboard Error: ' . $e->getMessage());
            return view('admin.dashboard', [
                'error' => 'Error loading dashboard data: ' . $e->getMessage(),
                'totalSales' => 0,
                'totalUsers' => 0,
                'totalProducts' => 0,
                'totalOrders' => 0,
                'recentOrders' => collect([]),
                'recentUsers' => collect([]),
                'salesData' => collect([]),
                'userGrowthData' => collect([])
            ]);
        }
    }
}
