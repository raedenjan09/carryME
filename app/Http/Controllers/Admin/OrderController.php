<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $orders = Order::with(['user', 'items'])->latest();

            return DataTables::of($orders)
                ->addColumn('customer', function ($order) {
                    return $order->user->name;
                })
                ->addColumn('total', function ($order) {
                    $total = $order->items->sum(function($item) {
                        return $item->price * $item->quantity;
                    });
                    return $total ?: 0; // Return 0 if no items or total is null
                })
                ->editColumn('status', function ($order) {
                    $badges = [
                        'pending' => 'bg-warning',
                        'processing' => 'bg-info',
                        'shipped' => 'bg-primary',
                        'delivered' => 'bg-success',
                        'cancelled' => 'bg-danger'
                    ];
                    return '<span class="badge ' . ($badges[$order->status] ?? 'bg-secondary') . '">' 
                        . ucfirst($order->status) . '</span>';
                })
                ->rawColumns(['status'])
                ->make(true);
        }

        return view('admin.orders.index');
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items.bag']);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:pending,processing,shipped,delivered,cancelled'
            ]);

            $order->update(['status' => $validated['status']]);

            return response()->json([
                'success' => true,
                'message' => 'Order status updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update order status'
            ], 500);
        }
    }
}