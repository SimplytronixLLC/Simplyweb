<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Models\CachedProduct;
use App\Models\Category;

class ApiController extends Controller
{
    public function products_api(Request $request)
    {
        $keyword = strtoupper(trim($request->key));
        $keyword = preg_replace('/[^A-Z0-9 ]/', ' ', $keyword);

        $terms = array_filter(explode(' ', $keyword));

        $perPage = $request->get('per_page', 10);

        if (empty($keyword)) {
            $empty = collect([]);
            $products = new LengthAwarePaginator(
                $empty,
                0,
                $perPage,
                1,
                ['path'=>$request->url(),'query'=>$request->query()]
            );

            if ($request->expectsJson()) {
                return response()->json([
                    'data' => [],
                    'next_page' => null
                ]);
            }

            return view('shop',['products'=>$products]);
        }

        $visitorId = $request->cookie('visitor_id');
        if ($visitorId) {
            DB::table('visitor_searches')->insert([
                'visitor_id'=>$visitorId,
                'part_number'=>$keyword,
                'source'=>'search',
                'created_at'=>now(),
                'updated_at'=>now(),
            ]);
        }

        $results = CachedProduct::select(
            'id','product_key','description','image',
            'category','manufacturer','unit_price','quantity',
            'datasheet'
        )
        ->where(function ($q) use ($terms) {
            foreach ($terms as $term) {
                $q->where(function ($sub) use ($term) {
                    $sub->where('product_key', 'LIKE', "%$term%")
                        ->orWhere('description', 'LIKE', "%$term%");
                });
            }
        })
        ->orderBy('product_key')
        ->paginate($perPage)
        ->withQueryString();

        if ($results->total() == 0) {

            $this->syncFromApi($keyword);

            $results = CachedProduct::select(
                'id','product_key','description','image',
                'category','manufacturer','unit_price','quantity',
                'datasheet'
            )
            ->where(function ($q) use ($terms) {
                foreach ($terms as $term) {
                    $q->where(function ($sub) use ($term) {
                        $sub->where('product_key', 'LIKE', "%$term%")
                            ->orWhere('description', 'LIKE', "%$term%");
                    });
                }
            })
            ->orderBy('product_key')
            ->paginate($perPage)
            ->withQueryString();
        }

        $cacheKey = "search_{$keyword}_page_".$request->get('page',1)."_per_{$perPage}";

        $products = cache()->remember($cacheKey, 60, function () use ($results) {
            return $results->through(function ($item) {
                return $this->formatProduct($item);
            });
        });

        if ($request->expectsJson()) {
            return response()->json([
                'data' => $products->items(),
                'next_page' => $products->nextPageUrl()
            ]);
        }

        return view('shop',['products'=>$products]);
    }

    public function syncFromApi($keyword)
    {
        $apiKey = config('services.mouser.key');

        $start = 0;
        $calls = 0;
        $maxCalls = 20;
        $totalRecords = null;

        $categoryMap = DB::table('source_category_map')
            ->pluck('category_id','source_category_name');

        while ($calls < $maxCalls) {

            $response = Http::retry(3, 2000)
                ->timeout(30)
                ->post(
                    "https://api.mouser.com/api/v1/search/keyword?apiKey={$apiKey}",
                    [
                        "SearchByKeywordRequest"=>[
                            "keyword"=>$keyword,
                            "records"=>20,
                            "startingRecord"=>$start,
                            "searchOptions"=>"None",
                            "searchWithYourSignUpLanguage"=>false
                        ]
                    ]
                );

            $calls++;

            if (!$response->successful()) {
                break;
            }

            $data = $response->json();
            $parts = $data['SearchResults']['Parts'] ?? [];

            if ($totalRecords === null) {
                $totalRecords = $data['SearchResults']['NumberOfResult'] ?? 0;
            }

            if (!$parts) break;

            foreach ($parts as $part) {

                $productKey =
                    $part['ManufacturerPartNumber']
                    ?? $part['MouserPartNumber']
                    ?? null;

                if (!$productKey) continue;

                $productKey = strtoupper(trim($productKey));
                $categoryName = $part['Category'] ?? null;

                $categoryId = null;

                if ($categoryName) {

                    if (isset($categoryMap[$categoryName])) {

                        $categoryId = $categoryMap[$categoryName];

                    } else {

                        $parentName = $this->detectParent($categoryName);

                        $parent = Category::firstOrCreate(
                            ['name'=>$parentName],
                            [
                                'slug'=>Str::slug($parentName),
                                'level'=>1,
                                'parent_id'=>null
                            ]
                        );

                        $category = Category::firstOrCreate(
                            ['name'=>$categoryName],
                            [
                                'slug'=>Str::slug($categoryName),
                                'level'=>2,
                                'parent_id'=>$parent->id
                            ]
                        );

                        $categoryId = $category->id;

                        DB::table('source_category_map')->updateOrInsert(
                            ['source_category_name'=>$categoryName],
                            ['category_id'=>$categoryId]
                        );

                        $categoryMap[$categoryName] = $categoryId;
                    }
                }

                CachedProduct::updateOrCreate(
                    ['product_key'=>$productKey],
                    [
                        'name'=>$productKey,
                        'description'=>$part['Description'] ?? null,
                        'image'=>$part['ImagePath'] ?? null,
                        'category'=>$categoryName,
                        'category_id'=>$categoryId,
                        'manufacturer'=>$part['Manufacturer'] ?? null,
                        'unit_price'=>isset($part['PriceBreaks'][0]['Price'])
                            ? str_replace(['$',','],'',$part['PriceBreaks'][0]['Price'])
                            : null,
                        'quantity'=>rand(3000,15000),
                        'datasheet'=>$part['DataSheetUrl'] ?? null,
                        'raw_data'=>json_encode($part),
                        'is_synced'=>0,
                        'updated_at'=>now()
                    ]
                );
            }

            $start += 20;

            if ($start >= $totalRecords) {
                break;
            }
        }
    }

    private function formatProduct($item)
    {
        return [
            'PhotoUrl'=>$item->image ?? '',
            'QuantityAvailable'=>$item->quantity ?? 0,
            'UnitPrice'=>$item->unit_price ?? null,
            'Category'=>['Name'=>$item->category ?? ''],
            'Manufacturer'=>['Name'=>$item->manufacturer ?? ''],
            'Description'=>[
                'ProductDescription'=>$item->description ?? ''
            ],
            'DataSheetUrl'=>$item->datasheet ?? null,
            'ProductVariations'=>[[
                'DigiKeyProductNumber'=>$item->product_key
            ]]
        ];
    }

    private function detectParent($category)
    {
        $category = strtolower($category);

        if (str_contains($category,'resistor') ||
            str_contains($category,'capacitor') ||
            str_contains($category,'inductor') ||
            str_contains($category,'crystal') ||
            str_contains($category,'oscillator')) {
            return 'Passive Components';
        }

        if (str_contains($category,'sensor') ||
            str_contains($category,'measurement')) {
            return 'Sensors & Measurement';
        }

        if (str_contains($category,'rf') ||
            str_contains($category,'wireless') ||
            str_contains($category,'antenna')) {
            return 'RF & Wireless';
        }

        if (str_contains($category,'connector') ||
            str_contains($category,'terminal') ||
            str_contains($category,'socket') ||
            str_contains($category,'header') ||
            str_contains($category,'plug')) {
            return 'Connectors';
        }

        if (str_contains($category,'led') ||
            str_contains($category,'lighting') ||
            str_contains($category,'display')) {
            return 'Lighting & LEDs';
        }

        if (str_contains($category,'hardware') ||
            str_contains($category,'mounting') ||
            str_contains($category,'spacer')) {
            return 'Hardware';
        }

        if (str_contains($category,'evaluation') ||
            str_contains($category,'development')) {
            return 'Development Tools';
        }

        if (str_contains($category,'power') ||
            str_contains($category,'voltage') ||
            str_contains($category,'regulator') ||
            str_contains($category,'converter')) {
            return 'Power & Power Management';
        }

        return 'Other';
    }
}