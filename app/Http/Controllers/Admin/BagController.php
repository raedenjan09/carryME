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
                    return '<a href="' . route('bags.edit', $bag->id) . '" class="btn btn-sm btn-primary">Edit</a>
                            <button onclick="deleteBag(' . $bag->id . ')" class="btn btn-sm btn-danger">Delete</button>';
                })
                ->rawColumns(['image', 'action'])
                ->make(true);
        }

        return view('admin.bags.index');
    }

    public function create()
    {
        return view('admin.bags.create');
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|max:255',
        'description' => 'required',
        'price' => 'required|numeric',
        'images.*' => 'required|image|mimes:jpeg,png,jpg|max:2048'
    ]);

    $bag = Bag::create([
        'name' => $validated['name'],
        'description' => $validated['description'],
        'price' => $validated['price'],
    ]);

    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $index => $imageFile) {
            $fileName = time() . '_' . $index . '.' . $imageFile->extension();
            $imageFile->move(public_path('images/bags'), $fileName);

            BagImage::create([
                'bag_id' => $bag->id,
                'image_path' => $fileName, // Store only the filename
                'is_primary' => $index === 0
            ]);
        }
    }

    return redirect()->route('bags.index')->with('success', 'Bag created successfully');
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
            'price' => 'required|numeric',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);
    
        $bag->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
        ]);
    
        if ($request->hasFile('images')) {
            // Delete old images if replace_images is checked
            if ($request->input('replace_images')) {
                foreach ($bag->images as $image) {
                    unlink(public_path($image->image_path));
                    $image->delete();
                }
            }
    
            foreach ($request->file('images') as $index => $image) {
                $imageName = time() . '_' . $index . '.' . $image->extension();
                $image->move(public_path('images/bags'), $imageName);
                
                BagImage::create([
                    'bag_id' => $bag->id,
                    'image_path' => 'images/bags/' . $imageName,
                    'is_primary' => !$bag->images()->exists() && $index === 0
                ]);
            }
        }
    
        return redirect()->route('bags.index')->with('success', 'Bag updated successfully');
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
}
