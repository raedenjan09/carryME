<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $reviews = Review::with(['bag', 'user'])
                           ->select('reviews.*');

            return DataTables::of($reviews)
                ->addColumn('reviewer_name', function ($review) {
                    return $review->user ? $review->user->name : 'Anonymous';
                })
                ->addColumn('action', function ($review) {
                    return ''; // Buttons are rendered in JavaScript
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.reviews.index');
    }

    public function destroy(Review $review)
    {
        $review->delete();
        return response()->json(['message' => 'Review deleted successfully']);
    }

    


public function getStats()
{
    $stats = [
        'total' => Review::count(),
        'average' => Review::avg('rating') ?? 0
    ];
    return response()->json($stats);
}


}