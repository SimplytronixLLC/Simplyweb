<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SpecsController extends Controller
{
    public function index()
    {
        return view('admin.specs.index');
    }

    public function load(Request $request)
    {
        $product = DB::table('cached_products')
            ->where('product_key', strtoupper(trim($request->product_key)))
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Part not found'
            ]);
        }

        return response()->json([
            'success' => true,
            'specs'   => json_decode($product->specs, true) ?? []
        ]);
    }

    public function save(Request $request)
    {
        $request->validate([
            'product_key' => 'required',
            'specs'       => 'required|array'
        ]);

        $productKey = strtoupper(trim($request->product_key));

        $updated = DB::table('cached_products')
            ->where('product_key', $productKey)
            ->update([
                'specs'        => json_encode($request->specs),
                'specs_synced' => 1,
                'updated_at'   => now()
            ]);

        if (!$updated) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found or nothing changed'
            ], 404);
        }

        // Bust both cache keys so the frontend reflects changes immediately
        Cache::forget('specs_' . md5($productKey));
        Cache::forget('product_record_' . md5($productKey));

        return response()->json([
            'success' => true,
            'message' => 'Specs updated'
        ]);
    }
}