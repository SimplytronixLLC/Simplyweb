<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SeoToolsController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index(Request $request)
    {
        $seo = DB::table('seo_settings')->first();

        if (!$seo) {
            DB::table('seo_settings')->insert([
                'site_title'               => '',
                'site_description'         => '',
                'seo_title_template'       => '',
                'seo_description_template' => '',
                'created_at'               => now(),
                'updated_at'               => now(),
            ]);
            $seo = DB::table('seo_settings')->first();
        }

        // Load preview product
        $product = null;
        if ($request->mpn) {
            $product = DB::table('cached_products')
                ->where('product_key', $request->mpn)
                ->first();
        }
        if (!$product) {
            $product = DB::table('cached_products')
                ->whereNotNull('raw_data')
                ->whereNotNull('manufacturer')
                ->orderByDesc('id')
                ->first();
        }

        $preview = null;
        if ($product) {
            $mouser  = json_decode($product->raw_data ?? '{}');
            $digikey = json_decode($product->digikey_raw ?? '{}');
            $preview = [
                'manufacturer' => $product->manufacturer ?? ($mouser->Manufacturer ?? ''),
                'mpn'          => $product->product_key  ?? ($mouser->ManufacturerPartNumber ?? ''),
                'category'     => $product->category     ?? ($mouser->Category ?? ''),
                'price'        => $product->unit_price   ? '₹'.$product->unit_price : 'Contact',
                'stock'        => ($product->quantity ?? 0) > 0 ? 'In Stock' : 'Available',
                'rohs'         => $mouser->ROHSStatus      ?? '',
                'lifecycle'    => $mouser->LifecycleStatus ?? '',
                'digikey_desc' => $digikey->Product->Description->DetailedDescription ?? '',
                'description'  => $product->description   ?? ($mouser->Description ?? ''),
                'image'        => $product->image          ?? ($mouser->ImagePath ?? ''),
            ];
        }

        // Template analysis stats
        $templateStats = null;
        if (!empty($seo->seo_title_template)) {
            $templateStats = $this->analyzeTemplate($seo);
        }

        // Recent products for quick preview switcher
        $recentProducts = DB::table('cached_products')
            ->whereNotNull('manufacturer')
            ->whereNotNull('description')
            ->orderByDesc('id')
            ->limit(10)
            ->pluck('product_key');

        return view('admin.seo.index', compact(
            'seo', 'preview', 'templateStats', 'recentProducts'
        ));
    }

    private function analyzeTemplate($seo): array
    {
        $total    = DB::table('cached_products')->count();
        $sample   = DB::table('cached_products')
            ->whereNotNull('manufacturer')
            ->inRandomOrder()
            ->limit(200)
            ->get(['product_key','manufacturer','category','description',
                   'unit_price','quantity']);

        $titleOk  = 0;
        $descOk   = 0;
        $titleOver = 0;
        $titleUnder = 0;
        $descOver  = 0;
        $descUnder = 0;

        foreach ($sample as $p) {
            $replace = [
                '{manufacturer}' => $p->manufacturer ?? '',
                '{mpn}'          => $p->product_key  ?? '',
                '{category}'     => $p->category     ?? '',
                '{price}'        => $p->unit_price    ? '₹'.$p->unit_price : 'Contact',
                '{stock}'        => ($p->quantity ?? 0) > 0 ? 'In Stock' : 'Available',
                '{rohs}'         => '',
                '{lifecycle}'    => '',
                '{digikey_desc}' => '',
                '{description}'  => $p->description  ?? '',
            ];

            $title = trim(strtr($seo->seo_title_template ?? '', $replace));
            $desc  = trim(strtr($seo->seo_description_template ?? '', $replace));

            $tLen = strlen($title);
            $dLen = strlen($desc);

            if ($tLen >= 20 && $tLen <= 60)  $titleOk++;
            if ($tLen > 60)  $titleOver++;
            if ($tLen < 20)  $titleUnder++;
            if ($dLen >= 50 && $dLen <= 160) $descOk++;
            if ($dLen > 160) $descOver++;
            if ($dLen < 50)  $descUnder++;
        }

        $count = count($sample) ?: 1;

        return [
            'total'        => $total,
            'sample'       => $count,
            'title_ok_pct' => round($titleOk  / $count * 100),
            'title_over'   => round($titleOver / $count * 100),
            'title_under'  => round($titleUnder/ $count * 100),
            'desc_ok_pct'  => round($descOk   / $count * 100),
            'desc_over'    => round($descOver  / $count * 100),
            'desc_under'   => round($descUnder / $count * 100),
        ];
    }

    public function saveTemplate(Request $request)
    {
        DB::table('seo_settings')->updateOrInsert(
            ['id' => 1],
            [
                'seo_title_template'       => $request->seo_title_template       ?? '',
                'seo_description_template' => $request->seo_description_template ?? '',
                'updated_at'               => now(),
            ]
        );
        return back()->with('message', 'Template saved successfully.');
    }

    public function saveGlobal(Request $request)
    {
        DB::table('seo_settings')->updateOrInsert(
            ['id' => 1],
            [
                'site_title'       => $request->site_title       ?? '',
                'site_description' => $request->site_description ?? '',
                'updated_at'       => now(),
            ]
        );
        return back()->with('message', 'Global SEO saved.');
    }

    public function resetTemplate()
    {
        DB::table('seo_settings')->where('id', 1)->update([
            'seo_title_template'       => '',
            'seo_description_template' => '',
            'updated_at'               => now(),
        ]);
        return back()->with('message', 'Template reset.');
    }

    public function previewAjax(Request $request)
    {
        $mpn = $request->mpn;

        $product = DB::table('cached_products')
            ->where('product_key', $mpn)
            ->first();

        if (!$product) {
            return response()->json(['error' => 'Product not found']);
        }

        $mouser  = json_decode($product->raw_data   ?? '{}');
        $digikey = json_decode($product->digikey_raw ?? '{}');

        return response()->json([
            'manufacturer' => $product->manufacturer ?? ($mouser->Manufacturer ?? ''),
            'mpn'          => $product->product_key  ?? '',
            'category'     => $product->category     ?? ($mouser->Category ?? ''),
            'price'        => $product->unit_price   ? '₹'.$product->unit_price : 'Contact',
            'stock'        => ($product->quantity ?? 0) > 0 ? 'In Stock' : 'Available',
            'rohs'         => $mouser->ROHSStatus      ?? '',
            'lifecycle'    => $mouser->LifecycleStatus ?? '',
            'digikey_desc' => $digikey->Product->Description->DetailedDescription ?? '',
            'description'  => $product->description   ?? ($mouser->Description ?? ''),
            'image'        => $product->image          ?? ($mouser->ImagePath ?? ''),
        ]);
    }
}