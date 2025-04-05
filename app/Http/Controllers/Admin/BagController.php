<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bag;
use Illuminate\Http\Request;

class BagController extends Controller
{
    public function index()
    {
        $bags = Bag::latest()->paginate(10);
        return view('admin.bags.index', compact('bags'));
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
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->extension();
            $image->move(public_path('images/bags'), $imageName);
            $validated['image'] = 'images/bags/' . $imageName;
        }

        Bag::create($validated);

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
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($request->hasFile('image')) {
            if ($bag->image) {
                unlink(public_path($bag->image));
            }
            $image = $request->file('image');
            $imageName = time() . '.' . $image->extension();
            $image->move(public_path('images/bags'), $imageName);
            $validated['image'] = 'images/bags/' . $imageName;
        }

        $bag->update($validated);

        return redirect()->route('bags.index')->with('success', 'Bag updated successfully');
    }

    public function destroy(Bag $bag)
    {
        if ($bag->image) {
            unlink(public_path($bag->image));
        }
        $bag->delete();

        return redirect()->route('bags.index')->with('success', 'Bag deleted successfully');
    }
}
