<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bag;
use App\Models\BagImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\BagsImport;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class BagController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $bags = Bag::with('category', 'images'); // Ensure relationships are loaded

            return DataTables::of($bags)
                ->addColumn('image', function ($bag) {
                    return $bag->primaryImage
                        ? '<img src="' . asset($bag->primaryImage->image_path) . '" height="50">'
                        : 'No image';
                })
                ->addColumn('category', function ($bag) {
                    return $bag->category->name ?? 'Uncategorized';
                })
                ->addColumn('action', function ($bag) {
                    return '<a href="' . route('admin.bags.edit', $bag->id) . '" class="btn btn-sm btn-primary">Edit</a>
                            <button onclick="deleteBag(' . $bag->id . ')" class="btn btn-sm btn-danger">Delete</button>';
                })
                ->rawColumns(['image', 'action'])
                ->make(true);
        }

        return view('admin.bags.index');
    }

    public function create()
    {
        $categories = \App\Models\Category::all(); // Fetch all categories
        return view('admin.bags.create', compact('categories'));
    }

    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'name' => 'required|max:255',
            'description' => 'required',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0', // Validate stock
            'images' => 'required|array|min:1',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            // Start a database transaction
            DB::beginTransaction();

            // Create the bag
            $bag = Bag::create([
                'name' => $validated['name'],
                'slug' => Str::slug($validated['name']),
                'description' => $validated['description'],
                'price' => $validated['price'],
                'stock' => $validated['stock'], // Save stock
            ]);

            // Handle image uploads
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    $path = $image->store('bags', 'public');

                    BagImage::create([
                        'bag_id' => $bag->id,
                        'image_path' => $path,
                        'is_primary' => $index === 0, // First image is primary
                    ]);
                }
            }

            // Commit the transaction
            DB::commit();

            return redirect()->route('admin.bags.index')
                ->with('success', 'Bag created successfully.');
        } catch (\Exception $e) {
            // Rollback the transaction on error
            DB::rollBack();

            \Log::error('Bag creation failed: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Failed to create bag. Please try again.');
        }
    }

    public function edit(Bag $bag)
    {
        return view('admin.bags.edit', compact('bag'));
    }

    public function update(Request $request, Bag $bag)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'description' => 'required',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0', // Validate stock
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $bag->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'stock' => $validated['stock'], // Update stock
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('bags', 'public');

                BagImage::create([
                    'bag_id' => $bag->id,
                    'image_path' => $path,
                    'is_primary' => !$bag->images()->exists() && $index === 0
                ]);
            }
        }

        return redirect()->route('admin.bags.index')->with('success', 'Bag updated successfully.');
    }

    public function destroy($id)
    {
        $bag = Bag::findOrFail($id);
        $bag->delete(); // Soft delete the product
        return redirect()->route('admin.bags.index')->with('success', 'Product deleted successfully.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls'
        ]);

        try {
            Excel::import(new BagsImport, $request->file('excel_file'));
            return redirect()->route('bags.index')->with('success', 'Products imported successfully');
        } catch (\Exception $e) {
            return redirect()->route('bags.index')->with('error', 'Error importing products: ' . $e->getMessage());
        }
    }

    public function restore($id)
    {
        Bag::withTrashed()->find($id)->restore();
        return response()->json(['success' => true]);
    }

    public function updateStock(Request $request, Bag $bag)
    {
        $validated = $request->validate([
            'stock' => 'required|integer|min:0'
        ]);

        try {
            $bag->update([
                'stock' => $validated['stock']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Stock updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update stock'
            ], 500);
        }
    }
}
