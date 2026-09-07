<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoriesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Products page
     */
    public function products()
    {
        return view('admin.products.products');
    }

    /**
     * DataTables AJAX
     * Source: cached_products table
     */
    public function cachedProductsList(Request $request)
    {
        $base = DB::table('cached_products');

        $totalRecords = (clone $base)->count();

        $query = DB::table('cached_products');

        if ($request->filled('manufacturer')) {
            $query->where('manufacturer', $request->input('manufacturer'));
        }
        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }
        if ($request->filled('stock_status')) {
            $status = $request->input('stock_status');
            if ($status === 'out_of_stock') {
                $query->where(function ($q) {
                    $q->whereNull('quantity')->orWhere('quantity', '<=', 0);
                });
            } elseif ($status === 'low_stock') {
                $query->whereBetween('quantity', [1, 9]);
            } elseif ($status === 'in_stock') {
                $query->where('quantity', '>=', 10);
            }
        }

        if ($request->filled('specs_synced')) {
            $syncStatus = $request->input('specs_synced');
            if ($syncStatus === 'synced') {
                $query->where('specs_synced', 1);
            } elseif ($syncStatus === 'not_synced') {
                $query->where(function ($q) {
                    $q->whereNull('specs_synced')->orWhere('specs_synced', 0);
                });
            }
        }

        $search = $request->input('search')['value'] ?? null;
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('product_key', 'like', "%{$search}%")
                  ->orWhere('manufacturer', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        $recordsFiltered = (clone $query)->count();

        $start  = intval($request->input('start', 0));
        $length = intval($request->input('length', 10));

        // Map DataTables column index -> actual sortable DB column.
        // Columns not listed here (image, datasheet, source, stock_status)
        // fall back to id desc since they're computed/non-sortable in SQL.
        $sortableColumns = [
            1 => 'product_key',
            2 => 'manufacturer',
            3 => 'category',
            4 => 'unit_price',
            5 => 'quantity',
            7 => 'specs_synced',
            8 => 'created_at',
            9 => 'updated_at',
        ];

        $orderCol = $request->input('order.0.column');
        $orderDir = $request->input('order.0.dir', 'desc');
        $orderDir = strtolower($orderDir) === 'asc' ? 'asc' : 'desc';

        if ($orderCol !== null && isset($sortableColumns[(int) $orderCol])) {
            $query->orderBy($sortableColumns[(int) $orderCol], $orderDir);
        } else {
            $query->orderBy('id', 'desc');
        }

        if ($length == -1) {
            // "All" selected in the entries dropdown.
            // MySQL rejects OFFSET without LIMIT, so skip both.
            $products = $query->get();
        } else {
            $products = $query
                ->offset($start)
                ->limit($length)
                ->get();
        }

        $data = [];

        foreach ($products as $product) {
            $qty = (int) ($product->quantity ?? 0);
            if ($qty <= 0) {
                $stockStatus = 'out_of_stock';
            } elseif ($qty < 10) {
                $stockStatus = 'low_stock';
            } else {
                $stockStatus = 'in_stock';
            }

            $data[] = [
                'id'            => $product->id,
                'product_key'   => $product->product_key,
                'name'          => $product->name,
                'manufacturer'  => $product->manufacturer ?? '-',
                'category'      => $product->category ?? '-',
                'unit_price'    => $product->unit_price !== null ? number_format($product->unit_price, 2) : null,
                'quantity'      => $product->quantity,
                'stock_status'  => $stockStatus,
                'specs_synced'  => (bool) ($product->specs_synced ?? false),
                'image_url'     => $product->image ?: ($product->image_backup ?: null),
                'datasheet_url' => $product->datasheet ?? null,
                'created_at'    => $product->created_at ? date('Y-m-d', strtotime($product->created_at)) : null,
                'updated_at'    => $product->updated_at ? date('Y-m-d', strtotime($product->updated_at)) : null,
            ];
        }

        $stats = [
            'total_products'     => $totalRecords,
            'manufacturer_count' => (clone $base)->distinct()->count('manufacturer'),
            'low_stock_count'    => (clone $base)->where('quantity', '<', 10)->count(),
            'avg_price'          => number_format((clone $base)->avg('unit_price') ?? 0, 2),
        ];

        return response()->json([
            'draw'            => intval($request->input('draw')),
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
            'stats'           => $stats,
        ]);
    }

    public function updateProduct(Request $request, $id)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'category'     => 'nullable|string|max:255',
            'unit_price'   => 'nullable|numeric|min:0',
            'quantity'     => 'nullable|integer|min:0',
        ]);

        $exists = DB::table('cached_products')->where('id', $id)->exists();
        if (!$exists) {
            return response()->json(['success' => false, 'message' => 'Product not found.'], 404);
        }

        DB::table('cached_products')->where('id', $id)->update([
            'name'         => $validated['name'],
            'manufacturer' => $validated['manufacturer'] ?? null,
            'category'     => $validated['category'] ?? null,
            'unit_price'   => $validated['unit_price'] ?? null,
            'quantity'     => $validated['quantity'] ?? 0,
            'updated_at'   => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Product updated.']);
    }

    public function bulkDeleteProducts(Request $request)
    {
        $ids = $request->input('ids', []);

        if (!is_array($ids) || empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No products selected.'], 422);
        }

        $ids = array_filter(array_map('intval', $ids));

        // Capture affected category_ids BEFORE deleting, since we lose this info after.
        $affectedCategoryIds = DB::table('cached_products')
            ->whereIn('id', $ids)
            ->whereNotNull('category_id')
            ->distinct()
            ->pluck('category_id');

        $deleted = DB::table('cached_products')->whereIn('id', $ids)->delete();

        $removedCategories = $this->removeEmptyCategories($affectedCategoryIds);

        $message = "{$deleted} product(s) deleted.";
        if ($removedCategories > 0) {
            $message .= " {$removedCategories} now-empty categor" . ($removedCategories === 1 ? 'y' : 'ies') . " also removed.";
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'deleted' => $deleted,
            'categories_removed' => $removedCategories,
        ]);
    }

    /**
     * Delete any category (by id) that has zero remaining cached_products
     * referencing it. Returns the number of categories removed.
     */
    private function removeEmptyCategories($categoryIds): int
    {
        $removed = 0;

        foreach ($categoryIds as $categoryId) {
            if (empty($categoryId)) {
                continue;
            }

            $remaining = DB::table('cached_products')->where('category_id', $categoryId)->count();

            if ($remaining === 0) {
                DB::table('categories')->where('id', $categoryId)->delete();
                $removed++;
            }
        }

        return $removed;
    }

    public function deleteByCategory(Request $request)
    {
        // Accept either a single 'category' string (legacy) or a 'categories' array.
        $categories = $request->input('categories', []);

        if (empty($categories) && $request->filled('category')) {
            $categories = [$request->input('category')];
        }

        $categories = array_values(array_filter(array_map('trim', (array) $categories)));

        if (empty($categories)) {
            return response()->json(['success' => false, 'message' => 'No category specified.'], 422);
        }

        // Capture affected category_ids BEFORE deleting (products in these named
        // categories may span more than one category_id if names aren't unique).
        $affectedCategoryIds = DB::table('cached_products')
            ->whereIn('category', $categories)
            ->whereNotNull('category_id')
            ->distinct()
            ->pluck('category_id');

        $deleted = DB::table('cached_products')->whereIn('category', $categories)->delete();

        $removedCategories = $this->removeEmptyCategories($affectedCategoryIds);

        $categoryList = implode(', ', $categories);
        $message = "{$deleted} product(s) deleted from " . count($categories) . " categor" . (count($categories) === 1 ? 'y' : 'ies') . " ({$categoryList}).";
        if ($removedCategories > 0) {
            $message .= " {$removedCategories} now-empty categor" . ($removedCategories === 1 ? 'y' : 'ies') . " also removed.";
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'deleted' => $deleted,
            'categories_removed' => $removedCategories,
        ]);
    }

    public function productFilterOptions()
    {
        return response()->json([
            'manufacturers' => DB::table('cached_products')
                ->whereNotNull('manufacturer')
                ->distinct()
                ->orderBy('manufacturer')
                ->pluck('manufacturer'),
            'categories' => DB::table('cached_products')
                ->whereNotNull('category')
                ->distinct()
                ->orderBy('category')
                ->pluck('category'),
        ]);
    }
}
