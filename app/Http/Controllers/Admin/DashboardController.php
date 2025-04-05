<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Bag;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            // Get the last 6 months
            $months = collect(range(5, 0))->map(function ($i) {
                return Carbon::now()->startOfMonth()->subMonths($i);
            });

            // Initialize collections
            $salesData = collect();
            $userGrowthData = collect();

            // Populate sales data
            foreach ($months as $month) {
                $total = Order::whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->sum('total') ?? 0;

                $salesData->push([
                    'month' => $month->format('M Y'),
                    'total' => $total
                ]);
            }

            // Populate user growth data
            foreach ($months as $month) {
                $count = User::whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count();

                $userGrowthData->push([
                    'month' => $month->format('M Y'),
                    'count' => $count
                ]);
            }

            // Pass data to the view
            return view('admin.dashboard', [
                'totalSales' => Order::sum('total') ?? 0,
                'totalUsers' => User::count() ?? 0,
                'totalProducts' => Bag::count() ?? 0,
                'totalOrders' => Order::count() ?? 0,
                'recentOrders' => Order::with('user')->latest()->take(5)->get(),
                'recentUsers' => User::latest()->take(5)->get(),
                'salesData' => $salesData,
                'userGrowthData' => $userGrowthData
            ]);
        } catch (\Exception $e) {
            \Log::error('Dashboard Error: ' . $e->getMessage());

            return view('admin.dashboard', [
                'totalSales' => 0,
                'totalUsers' => 0,
                'totalProducts' => 0,
                'totalOrders' => 0,
                'recentOrders' => collect(),
                'recentUsers' => collect(),
                'salesData' => collect(),
                'userGrowthData' => collect()
            ])->with('error', 'Error loading dashboard data');
        }
    }
}
