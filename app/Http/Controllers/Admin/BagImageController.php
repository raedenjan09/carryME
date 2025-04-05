<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BagImage;
use Illuminate\Http\Request;

class BagImageController extends Controller
{
    public function destroy(BagImage $bagImage)
    {
        if ($bagImage->is_primary) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete primary image'
            ]);
        }

        unlink(public_path($bagImage->image_path));
        $bagImage->delete();

        return response()->json([
            'success' => true,
            'message' => 'Image deleted successfully'
        ]);
    }

    public function makePrimary(BagImage $bagImage)
    {
        $bagImage->bag->images()->update(['is_primary' => false]);
        $bagImage->update(['is_primary' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Image set as primary'
        ]);
    }
}