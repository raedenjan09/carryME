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
    try {
        if (!$request->ajax()) {
            return view('admin.orders.index');
        }

        $orders = Order::with('user')
            ->select('orders.*')
            ->latest()
            ->paginate(10);

        return response()->json([
            'data' => $orders->map(function ($order) {
                return [
                    'id' => $order->id,
                    'customer' => $order->user ? $order->user->name : 'N/A',
                    'total' => $order->total,
                    'status' => $order->status,
                    'created_at' => $order->created_at,
                ];
            }),
            'current_page' => $orders->currentPage(),
            'last_page' => $orders->lastPage(),
            'per_page' => $orders->perPage(),
            'total' => $orders->total()
        ]);
    } catch (\Exception $e) {
        \Log::error('Error loading orders: ' . $e->getMessage());
        return response()->json(['error' => 'Failed to load orders'], 500);
    }
}

public function updateStatus(Request $request, Order $order)
{
    try {
        // Validate input
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,completed,shipped,delivered,cancelled'
        ]);

        // Update order status
        $order->status = $validated['status'];
        $order->save();

        \Log::info("Order {$order->id} status updated to {$order->status}");

        // Dispatch notification job to queue
        dispatch(function () use ($order, $validated) {
            try {
                $pdfPath = null;
                
                if ($validated['status'] === 'completed') {
                    try {
                        $pdfGenerator = new PdfReceiptGenerator();
                        $pdfPath = $pdfGenerator->generateReceipt($order);
                    } catch (\Exception $e) {
                        \Log::error("PDF generation failed: " . $e->getMessage());
                    }
                }

                $order->user->notify(new OrderStatusUpdated($order, $pdfPath));
                \Log::info("Notification queued for order {$order->id}");
                
            } catch (\Exception $e) {
                \Log::error("Notification failed: " . $e->getMessage());
                // Retry logic could be added here
            }
        })->afterResponse();

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully. Notification queued.'
        ]);

    } catch (\Exception $e) {
        \Log::error("Order status update failed: " . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to update order status'
        ], 500);
    }
}
public function show(Order $order)
{
    return view('admin.orders.show', compact('order'));
}`  

}