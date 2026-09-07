<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VisitorController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index(Request $request)
    {
        $days = (int) $request->get('days', 7);
        $days = in_array($days, [1, 7, 30, 90]) ? $days : 7;
        $from = now()->subDays($days);

        // ── Overview stats ────────────────────────────────────────────────
        $totalVisitors = DB::table('visitor_sessions')
            ->where('first_seen_at', '>=', $from)->count();

        $totalPageviews = DB::table('visitor_pageviews')
            ->where('created_at', '>=', $from)->count();

        $totalSearches = DB::table('visitor_searches')
            ->where('created_at', '>=', $from)->count();

        $totalConversions = DB::table('visitor_sessions')
            ->where('first_seen_at', '>=', $from)
            ->where('converted_to_quote', true)->count();

        $conversionRate = $totalVisitors > 0
            ? round($totalConversions / $totalVisitors * 100, 2) : 0;

        // ── Daily visitors chart ──────────────────────────────────────────
        $dailyVisitors = DB::table('visitor_sessions')
            ->where('first_seen_at', '>=', $from)
            ->selectRaw('DATE(first_seen_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date');

        $dailyPageviews = DB::table('visitor_pageviews')
            ->where('created_at', '>=', $from)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date');

        // Fill missing dates
        $chartDates    = [];
        $chartVisitors = [];
        $chartPageviews = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $chartDates[]     = now()->subDays($i)->format('M d');
            $chartVisitors[]  = $dailyVisitors[$date]  ?? 0;
            $chartPageviews[] = $dailyPageviews[$date] ?? 0;
        }

        // ── Device breakdown ─────────────────────────────────────────────
        $devices = DB::table('visitor_sessions')
            ->where('first_seen_at', '>=', $from)
            ->selectRaw('device, COUNT(*) as count')
            ->groupBy('device')
            ->pluck('count', 'device');

        // ── Browser breakdown ─────────────────────────────────────────────
        $browsers = DB::table('visitor_sessions')
            ->where('first_seen_at', '>=', $from)
            ->selectRaw('browser, COUNT(*) as count')
            ->groupBy('browser')
            ->orderByDesc('count')
            ->pluck('count', 'browser');

        // ── Traffic sources ───────────────────────────────────────────────
        $sources = DB::table('visitor_sessions')
            ->where('first_seen_at', '>=', $from)
            ->selectRaw("
                CASE
                    WHEN first_utm_source IS NOT NULL THEN first_utm_source
                    WHEN first_referrer IS NULL THEN 'Direct'
                    WHEN first_referrer LIKE '%google%' THEN 'Google'
                    WHEN first_referrer LIKE '%bing%' THEN 'Bing'
                    WHEN first_referrer LIKE '%linkedin%' THEN 'LinkedIn'
                    WHEN first_referrer LIKE '%facebook%' THEN 'Facebook'
                    ELSE 'Referral'
                END as source,
                COUNT(*) as count
            ")
            ->groupBy('source')
            ->orderByDesc('count')
            ->pluck('count', 'source');

        // ── Top pages ─────────────────────────────────────────────────────
        $topPages = DB::table('visitor_pageviews')
            ->where('created_at', '>=', $from)
            ->selectRaw('url, COUNT(*) as views')
            ->groupBy('url')
            ->orderByDesc('views')
            ->limit(10)
            ->get();

        // ── Top products viewed ───────────────────────────────────────────
        $topProducts = DB::table('visitor_pageviews')
            ->where('created_at', '>=', $from)
            ->where('page_type', 'product')
            ->whereNotNull('product_key')
            ->selectRaw('product_key, manufacturer, COUNT(*) as views, COUNT(DISTINCT visitor_id) as unique_visitors')
            ->groupBy('product_key', 'manufacturer')
            ->orderByDesc('views')
            ->limit(15)
            ->get();

        // ── Top searched parts ────────────────────────────────────────────
        $topSearches = DB::table('visitor_searches')
            ->where('created_at', '>=', $from)
            ->selectRaw('part_number, COUNT(*) as searches, COUNT(DISTINCT visitor_id) as unique_visitors')
            ->groupBy('part_number')
            ->orderByDesc('searches')
            ->limit(15)
            ->get();

        // ── Page type breakdown ───────────────────────────────────────────
        $pageTypes = DB::table('visitor_pageviews')
            ->where('created_at', '>=', $from)
            ->selectRaw('page_type, COUNT(*) as count')
            ->groupBy('page_type')
            ->orderByDesc('count')
            ->pluck('count', 'page_type');

        // ── Conversion funnel ─────────────────────────────────────────────
        $funnelVisitors  = $totalVisitors;
        $funnelSearched  = DB::table('visitor_sessions as s')
            ->join('visitor_searches as vs', 's.visitor_id', '=', 'vs.visitor_id')
            ->where('s.first_seen_at', '>=', $from)
            ->distinct('s.visitor_id')->count('s.visitor_id');
        $funnelViewedProduct = DB::table('visitor_sessions as s')
            ->join('visitor_pageviews as vp', 's.visitor_id', '=', 'vp.visitor_id')
            ->where('s.first_seen_at', '>=', $from)
            ->where('vp.page_type', 'product')
            ->distinct('s.visitor_id')->count('s.visitor_id');
        $funnelConverted = $totalConversions;

        // ── Recent conversions ────────────────────────────────────────────
        $recentConversions = DB::table('visitor_sessions as s')
            ->leftJoin('leads as l', 's.visitor_id', '=', 'l.visitor_id')
            ->leftJoin('quote as q', 'l.email', '=', 'q.email')
            ->where('s.converted_to_quote', true)
            ->where('s.first_seen_at', '>=', $from)
            ->select('s.visitor_id', 's.device', 's.first_utm_source',
                     's.first_referrer', 's.first_seen_at', 's.total_pageviews',
                     'l.email', 'q.name', 'q.company', 'q.part_number')
            ->orderByDesc('s.first_seen_at')
            ->limit(20)
            ->get();

        // ── Live visitors (last 5 min) ────────────────────────────────────
        $liveVisitors = DB::table('visitor_pageviews')
            ->where('created_at', '>=', now()->subMinutes(5))
            ->distinct('visitor_id')
            ->count('visitor_id');

        return view('admin.visitors.index', compact(
            'days', 'totalVisitors', 'totalPageviews', 'totalSearches',
            'totalConversions', 'conversionRate', 'chartDates',
            'chartVisitors', 'chartPageviews', 'devices', 'browsers',
            'sources', 'topPages', 'topProducts', 'topSearches',
            'pageTypes', 'funnelVisitors', 'funnelSearched',
            'funnelViewedProduct', 'funnelConverted', 'recentConversions',
            'liveVisitors'
        ));
    }

    public function visitorDetail(Request $request, string $visitorId)
    {
        $session = DB::table('visitor_sessions')
            ->where('visitor_id', $visitorId)
            ->first();

        if (!$session) abort(404);

        $pageviews = DB::table('visitor_pageviews')
            ->where('visitor_id', $visitorId)
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();

        $searches = DB::table('visitor_searches')
            ->where('visitor_id', $visitorId)
            ->orderByDesc('created_at')
            ->get();

        $lead = DB::table('leads')
            ->where('visitor_id', $visitorId)
            ->first();

        $quotes = [];
        if ($lead) {
            $quotes = DB::table('quote')
                ->where('email', $lead->email)
                ->orderByDesc('created_at')
                ->get();
        }

        return view('admin.visitors.detail', compact(
            'session', 'pageviews', 'searches', 'lead', 'quotes', 'visitorId'
        ));
    }
}