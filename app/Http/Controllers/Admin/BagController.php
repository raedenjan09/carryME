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
            $bags = Bag::with(['images' => function($query) {
                $query->where('is_primary', true);
            }]);

            return DataTables::of($bags)
                ->addColumn('image', function ($bag) {
                    $primaryImage = $bag->images->first();
                    return $primaryImage ? $primaryImage->image_path : null;
                })
                ->addColumn('category', function ($bag) {
                    return $bag->category ? $bag->category->name : 'Uncategorized';
                })
                ->addColumn('action', function ($bag) {
                    return '
                        <a href="'.route('admin.bags.edit', $bag->id).'" class="btn btn-sm btn-primary">Edit</a>
                        <form action="'.route('admin.bags.destroy', $bag->id).'" method="POST" class="d-inline">
                            '.csrf_field().'
                            '.method_field('DELETE').'
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure?\')">Delete</button>
                        </form>
                    ';
                })
                ->rawColumns(['action', 'image'])
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
        $validated = $request->validate([
            'name' => 'required|max:255',
            'description' => 'required',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'images' => 'required|array|min:1',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            DB::beginTransaction();

            $bag = Bag::create([
                'name' => $validated['name'],
                'slug' => Str::slug($validated['name']),
                'description' => $validated['description'],
                'price' => $validated['price'],
                'stock' => $validated['stock'],
                'category_id' => $validated['category_id']
            ]);

            if ($request->hasFile('images')) {
                $uploadPath = public_path('images/bags');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                foreach ($request->file('images') as $index => $image) {
                    $fileName = time() . '_' . $index . '.' . $image->getClientOriginalExtension();
                    $image->move($uploadPath, $fileName);

                    BagImage::create([
                        'bag_id' => $bag->id,
                        'image_path' => 'images/bags/' . $fileName,
                        'is_primary' => $index === 0
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.bags.index')->with('success', 'Bag created successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Bag creation failed: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to create bag: ' . $e->getMessage());
        }
    }

    public function edit(Bag $bag)
    {
        $categories = \App\Models\Category::all(); // Fetch all categories
        return view('admin.bags.edit', compact('bag', 'categories'));
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

        try {
            DB::beginTransaction();

            // Update bag details
            $bag->update([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'price' => $validated['price'],
                'stock' => $validated['stock']
            ]);

            // Handle new images if any
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('bags', 'public');
                    
                    BagImage::create([
                        'bag_id' => $bag->id,
                        'image_path' => $path,
                        'is_primary' => !$bag->images()->exists()
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('admin.bags.index')
                ->with('success', 'Bag updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Bag update failed: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Failed to update bag. Please try again.');
        }
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
            
            return redirect()->route('admin.bags.index')
                ->with('success', 'Bags imported successfully');
        } catch (\Exception $e) {
            return redirect()->route('admin.bags.index')
                ->with('error', 'Error importing bags: ' . $e->getMessage());
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
