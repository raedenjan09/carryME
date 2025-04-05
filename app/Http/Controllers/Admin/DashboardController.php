<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Bag;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            // Get last 6 months for chart data
            $months = collect(range(5, 0))->map(function ($i) {
                return Carbon::now()->startOfMonth()->subMonths($i);
            });

            // Populate sales data with real DB queries
            $salesData = $months->map(function ($month) {
                $total = Order::whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->where('payment_status', 'paid')
                    ->sum('total');

                return [
                    'month' => $month->format('M Y'),
                    'total' => (float) $total
                ];
            });

            // Populate user growth data
            $userGrowthData = $months->map(function ($month) {
                $count = User::whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count();

                return [
                    'month' => $month->format('M Y'),
                    'count' => $count
                ];
            });

            // Get dashboard statistics
            $data = [
                'totalSales' => Order::where('payment_status', 'paid')->sum('total'),
                'totalUsers' => User::count(),
                'totalProducts' => Bag::count(),
                'totalOrders' => Order::count(),
                'recentOrders' => Order::with(['user', 'bag'])
                    ->latest()
                    ->take(5)
                    ->get(),
                'recentUsers' => User::latest()
                    ->take(5)
                    ->get(),
                'salesData' => $salesData,
                'userGrowthData' => $userGrowthData
            ];

            \Log::info('Dashboard Data:', $data); // Debug log

            return view('admin.dashboard', $data);

        } catch (\Exception $e) {
            \Log::error('Dashboard Error: ' . $e->getMessage());
            return back()->with('error', 'Error loading dashboard data: ' . $e->getMessage());
        }
    }
}
