<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SeoAuditController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index(Request $request)
    {
        $query = DB::table('seo_audit_cache as a')
            ->join('cached_products as p', 'a.product_id', '=', 'p.id')
            ->select('a.*', 'p.unit_price', 'p.quantity');

        if ($request->filled('filter')) {
            match($request->filter) {
                'no_image'     => $query->where('a.has_image', 0),
                'no_desc'      => $query->where('a.has_description', 0),
                'no_specs'     => $query->where('a.has_specs', 0),
                'no_datasheet' => $query->where('a.has_datasheet', 0),
                'no_meta'      => $query->where('a.has_meta_title', 0),
                'bad_title'    => $query->where('a.title_ok', 0),
                'bad_desc'     => $query->where('a.desc_ok', 0),
                'schema_fail'  => $query->where('a.schema_ok', 0),
                'low_score'    => $query->where('a.score', '<', 50),
                default        => null
            };
        }

        if ($request->filled('manufacturer')) {
            $query->where('a.manufacturer', $request->manufacturer);
        }

        if ($request->filled('search')) {
            $query->where('a.product_key', 'like', '%'.$request->search.'%');
        }

        $sortCol = in_array($request->sort, ['score','product_key','manufacturer'])
            ? 'a.'.$request->sort : 'a.score';
        $sortDir = $request->dir === 'asc' ? 'asc' : 'desc';

        $products = $query->orderBy($sortCol, $sortDir)->paginate(50)->withQueryString();

        // Summary stats
        $stats = DB::table('seo_audit_cache')->selectRaw('
            COUNT(*)                                as total,
            SUM(has_image)                          as has_image,
            SUM(has_datasheet)                      as has_datasheet,
            SUM(has_specs)                          as has_specs,
            SUM(has_description)                    as has_description,
            SUM(has_meta_title)                     as has_meta_title,
            SUM(has_meta_desc)                      as has_meta_desc,
            SUM(schema_ok)                          as schema_ok,
            SUM(title_ok)                           as title_ok,
            SUM(desc_ok)                            as desc_ok,
            ROUND(AVG(score),1)                     as avg_score,
            SUM(CASE WHEN score >= 80 THEN 1 END)   as good,
            SUM(CASE WHEN score >= 50
                      AND score < 80 THEN 1 END)    as medium,
            SUM(CASE WHEN score < 50 THEN 1 END)    as poor
        ')->first();

        $lastRun = DB::table('seo_audit_cache')->max('updated_at');

        $manufacturers = DB::table('seo_audit_cache')
            ->whereNotNull('manufacturer')
            ->where('manufacturer', '!=', '')
            ->distinct()
            ->orderBy('manufacturer')
            ->pluck('manufacturer');

        return view('admin.seo.audit', compact(
            'products', 'stats', 'lastRun', 'manufacturers'
        ));
    }

   public function runAudit()
{
    $running = DB::table('system_settings')
        ->where('setting_key', 'seo_audit_running')
        ->value('setting_value');

    if ($running) {
        return response()->json(['success' => false, 'message' => 'Audit already running.']);
    }

    DB::table('system_settings')->updateOrInsert(
        ['setting_key' => 'seo_audit_running'],
        ['setting_value' => 1, 'updated_at' => now()]
    );

    try {
        \Artisan::call('seo:audit');

        DB::table('system_settings')->updateOrInsert(
            ['setting_key' => 'seo_audit_running'],
            ['setting_value' => 0, 'updated_at' => now()]
        );

        return response()->json(['success' => true, 'message' => 'Audit complete.']);

    } catch (\Exception $e) {

        DB::table('system_settings')->updateOrInsert(
            ['setting_key' => 'seo_audit_running'],
            ['setting_value' => 0, 'updated_at' => now()]
        );

        \Log::error('SEO audit failed: ' . $e->getMessage());

        return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
}

    public function export(Request $request)
    {
        $filter = $request->filter;

        $query = DB::table('seo_audit_cache');

        if ($filter) {
            match($filter) {
                'no_image'     => $query->where('has_image', 0),
                'no_desc'      => $query->where('has_description', 0),
                'no_specs'     => $query->where('has_specs', 0),
                'no_datasheet' => $query->where('has_datasheet', 0),
                'schema_fail'  => $query->where('schema_ok', 0),
                'low_score'    => $query->where('score', '<', 50),
                default        => null
            };
        }

        $rows = $query->orderBy('score')->get();

        $csv = "Product Key,Manufacturer,Score,Image,Datasheet,Specs,Description,Meta Title,Meta Desc,Schema OK,Title OK,Desc OK\n";

        foreach ($rows as $r) {
            $csv .= implode(',', [
                $r->product_key,
                $r->manufacturer,
                $r->score,
                $r->has_image     ? 'Yes' : 'No',
                $r->has_datasheet ? 'Yes' : 'No',
                $r->has_specs     ? 'Yes' : 'No',
                $r->has_description ? 'Yes' : 'No',
                $r->has_meta_title  ? 'Yes' : 'No',
                $r->has_meta_desc   ? 'Yes' : 'No',
                $r->schema_ok     ? 'Yes' : 'No',
                $r->title_ok      ? 'Yes' : 'No',
                $r->desc_ok       ? 'Yes' : 'No',
            ]) . "\n";
        }

        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="seo-audit-'.now()->format('Y-m-d').'.csv"',
        ]);
    }
}