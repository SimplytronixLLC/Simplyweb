<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\AdminLoginController;
use App\Http\Controllers\Admin\AdminHomeController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\ManufacturersController;
use App\Http\Controllers\Admin\CategoriesController;
use App\Http\Controllers\Admin\QuotationController;
use App\Http\Controllers\Admin\SeoToolsController;
use App\Http\Controllers\Admin\SyncController;
use App\Http\Controllers\Admin\SeoAuditController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\HarvestController;
use App\Http\Controllers\Admin\SpecsController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\SearchTrackController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ManufacturerController;
use App\Http\Controllers\API\SupplierAuthController;
use App\Http\Controllers\Admin\CrmWinbackController;
use App\Http\Controllers\Admin\CrmDuplicatesController;
use App\Http\Controllers\Admin\CrmPipelineController;
use App\Http\Controllers\Admin\CrmBulkEmailController;
use App\Models\ApiUsage;
use App\Models\CachedProduct;
use App\Http\Controllers\Admin\PagesController;
use App\Http\Controllers\VoiceController;



/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

// Homepage
Route::get('/', [HomeController::class, 'index'])->name('home');

// Public unsubscribe flow — signed links only, no auth required.
// Recipient clicks the link in an email footer, sees a confirmation page,
// then POSTs to actually unsubscribe (not a one-click GET action).
Route::get('/unsubscribe/{contact}', [App\Http\Controllers\UnsubscribeController::class, 'show'])
    ->name('crm.unsubscribe.show');
Route::post('/unsubscribe/{contact}', [App\Http\Controllers\UnsubscribeController::class, 'confirm'])
    ->name('crm.unsubscribe.confirm');

// Static pages
Route::get('/about-us',            [HomeController::class, 'about_us'])->name('about_us');
Route::get('/contact-us',          [HomeController::class, 'contact_us'])->name('contact_us');
Route::post('/contact-us',         [HomeController::class, 'contact_us_submit'])->name('contact_us_submit');
Route::get('/industries-we-serve', fn() => view('industries'))->name('industries');
Route::get('/quality-assurance',   fn() => view('quality-assurance'))->name('quality.assurance');
Route::get('/terms-and-conditions',fn() => view('terms'))->name('terms');

// Auth pages (noindex via robots.txt)
Route::get('/login',   [HomeController::class, 'login'])->name('login');
Route::get('/sign-up', [HomeController::class, 'sign_up'])->name('sign_up');

// Quote
Route::get('/get-a-quote',        [HomeController::class, 'get_a_quote'])->name('get_a_quote');
Route::post('/stock-alert-signup', [HomeController::class, 'stock_alert_signup'])->name('stock_alert_signup');
Route::post('/get-a-quote-submit',[HomeController::class, 'get_a_quote_submit'])->name('get_a_quote_submit');
Route::get('/quote-thank-you',    fn() => view('quote_thank_you'))->name('quote.thankyou');

// Order tracking
Route::get('/track-order',         [HomeController::class, 'track_order'])->name('track_order');
Route::post('/submit-track-order', [HomeController::class, 'submit_track_order'])->name('submit_track_order');

// Categories
Route::get('/category/{slug}', [HomeController::class, 'category_details'])->name('category.show');
Route::get('/get-level2/{parent}', [HomeController::class, 'getLevel2'])->name('get.level2');
// A-Z Category Listing (fixes the 404)
Route::get('/category', [HomeController::class, 'category_listing'])->name('category.listing');

// Redirect /categories → /category permanently (good for SEO)
Route::redirect('/categories', '/category', 301);

// Individual category detail (keep this as-is)
Route::get('/category/{slug}', [HomeController::class, 'category_details'])->name('category.show');

// Products
Route::get('/product/{manufacturer}/{part}', [ProductController::class, 'show'])
    ->where('part', '.*')
    ->name('product.show.path');
Route::get('/product/{manufacturer}', [ProductController::class, 'show'])
    ->name('product.show.query');
Route::get('/datasheet/{part}', [ProductController::class, 'datasheet'])
    ->where('part', '.*')
    ->name('datasheet.proxy');

// Stock & manufacturers
Route::get('/available-stock', [HomeController::class, 'available_stock'])->name('available_stock');
Route::get('/manufacturers',   [ManufacturerController::class, 'index'])->name('manufacturers.index');

// Blog & news
Route::get('/blog/{slug}',  [BlogController::class, 'show'])->name('blog.show');
Route::get('/news-alerts',  [HomeController::class, 'newsAlerts'])->name('news.alerts');

// BOM
Route::get('/bom-upload',  fn() => view('bom.upload'))->name('bom.upload');
Route::post('/bom-process', [App\Http\Controllers\BomController::class, 'process'])->name('bom.process');

// Sitemaps
Route::get('/sitemap.xml',                [SitemapController::class, 'index']);
Route::get('/sitemap-static.xml',         [SitemapController::class, 'static']);
Route::get('/sitemap-products-{page}.xml',[SitemapController::class, 'products'])
    ->where('page', '[0-9]+');

// Search tracking
Route::post('/track-part-search', [SearchTrackController::class, 'store'])->name('track.part.search');

//Calling
//Calling
Route::post('/voice/twiml', [VoiceController::class, 'twiml']);
Route::post('/voice/voicemail', [VoiceController::class, 'voicemail']);
Route::post('/voice/voicemail-saved', [VoiceController::class, 'voicemailSaved']);


// Internal APIs
Route::get('/manufacturers-api', [ApiController::class, 'manufacturers_api'])->name('manufacturers_api');
Route::get('/shop',              [ApiController::class, 'products_api'])->name('products_api');


// Redirect old URL pattern to relevant category
Route::get('/product/{slug}', function ($slug) {
    // Check if product exists
    $product = Product::where('slug', $slug)->first();
    
    if (!$product) {
        // Redirect to search with the slug as keyword
        return redirect('/available-stock?search=' . $slug, 301);
    }
    
    return view('product.show', compact('product'));
});

// Cache clear (local only)
Route::get('/cache', function () {
    if (app()->environment('local')) {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        return 'Cache Cleared';
    }
    abort(403);
});

/*
|--------------------------------------------------------------------------
| SUPPLIER PORTAL
|--------------------------------------------------------------------------
*/

Route::prefix('supplier')->group(function () {
    Route::post('/register', [SupplierAuthController::class, 'register']);
    Route::post('/login',    [SupplierAuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [SupplierAuthController::class, 'me']);
        
    Route::get('/leads', [App\Http\Controllers\Admin\LeadIntelligenceController::class, 'index'])
        ->name('admin.leads.index');
    Route::get('/leads/{id}', [App\Http\Controllers\Admin\LeadIntelligenceController::class, 'leadDetail'])
        ->name('admin.leads.detail');
    Route::post('/leads/{id}/outreach', [App\Http\Controllers\Admin\LeadIntelligenceController::class, 'sendOutreach'])
        ->name('admin.leads.outreach');
    Route::post('/leads/bulk-outreach', [App\Http\Controllers\Admin\LeadIntelligenceController::class, 'bulkOutreach'])
        ->name('admin.leads.bulk');
    Route::get('/leads/export/csv', [App\Http\Controllers\Admin\LeadIntelligenceController::class, 'exportLeads'])
        ->name('admin.leads.export');
    Route::get('/leads/stock-alerts', [App\Http\Controllers\Admin\LeadIntelligenceController::class, 'stockAlerts'])->name('leads.stock-alerts');
    });
});

Route::view('/supplier-dev/{any?}', 'supplier.index')->where('any', '.*');

/*
|--------------------------------------------------------------------------
| ADMIN — LOGIN (unauthenticated)
|--------------------------------------------------------------------------
*/

Route::post('/track-session-duration', function (\Illuminate\Http\Request $request) {
    $visitorId = $request->cookie('visitor_id');
    $seconds   = (int) $request->input('seconds', 0);

    if (!$visitorId || $seconds <= 0 || $seconds > 7200) {
        return response()->noContent();
    }

    \Illuminate\Support\Facades\DB::table('visitor_profiles')
        ->where('visitor_id', $visitorId)
        ->increment('total_session_seconds', $seconds);

    return response()->noContent();
})->name('track.session.duration');

Route::prefix('admin')->group(function () {
    Route::get('/login',  [AdminLoginController::class, 'login'])->name('admin.login');
    Route::post('/login', [AdminLoginController::class, 'loginSubmit'])->name('admin.login.submit');
});

/*
|--------------------------------------------------------------------------
| ADMIN — PROTECTED
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->middleware(['auth'])->group(function () {

    // Core
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin_dashboard');
    Route::get('/home',      [AdminHomeController::class, 'index'])->name('admin.home');
    Route::post('/logout',   [AdminLoginController::class, 'logout'])->name('admin.logout');

    // Resources
    Route::resource('slider',        SliderController::class);
    Route::resource('manufacturers', ManufacturersController::class);
    Route::resource('categories',    CategoriesController::class);

    // Products
    Route::get('/products',              [CategoriesController::class, 'products'])->name('admin.products');
    Route::match(['GET','POST'], '/products/list',
        [CategoriesController::class, 'cachedProductsList']
    )->name('cached_products_list');
    Route::get('/products/filter-options', [CategoriesController::class, 'productFilterOptions'])->name('products.filter_options');
    Route::patch('/products/{id}', [CategoriesController::class, 'updateProduct'])->name('products.update');
    Route::post('/products/bulk-delete', [CategoriesController::class, 'bulkDeleteProducts'])->name('products.bulk_delete');
    Route::post('/products/delete-by-category', [CategoriesController::class, 'deleteByCategory'])->name('products.delete_by_category');

    // Quotations
    Route::get('/quotation',               [QuotationController::class, 'index'])->name('admin.quotation');
    Route::post('/quotation/list',         [QuotationController::class, 'quotation_list'])->name('quotation_list');
    Route::post('/quotation/status-update',[QuotationController::class, 'updateStatus'])->name('quotation_update_status');

    // CRM Winback
    Route::get('/crm/winback',    [CrmWinbackController::class, 'index'])->name('admin.crm.winback');
    Route::post('/crm/winback',   [CrmWinbackController::class, 'approve'])->name('admin.crm.winback.approve');
    Route::post('/crm/winback/update-name', [CrmWinbackController::class, 'updateName'])->name('admin.crm.winback.update_name');
    Route::get('/crm', [CrmPipelineController::class, 'index'])->name('admin.crm.pipeline');
    Route::get('/crm/dashboard', [App\Http\Controllers\Admin\CrmDashboardController::class, 'index'])->name('admin.crm.dashboard');
    Route::get('/crm/export',    [App\Http\Controllers\Admin\CrmDashboardController::class, 'export'])->name('admin.crm.export');
    Route::post('/crm/move-stage', [CrmPipelineController::class, 'moveStage'])->name('admin.crm.move_stage');
    Route::get('/crm/contact/{id}', [CrmPipelineController::class, 'show'])->name('admin.crm.contact.show');
    Route::post('/crm/contact/{id}/note', [CrmPipelineController::class, 'addNote'])->name('admin.crm.contact.note');
    Route::post('/crm/contact/{id}/manual-action', [CrmPipelineController::class, 'manualAction'])->name('admin.crm.contact.manual_action');
    Route::get('/crm/bulk-email', [CrmBulkEmailController::class, 'index'])->name('admin.crm.bulk_email');
    Route::post('/crm/bulk-email', [CrmBulkEmailController::class, 'send'])->name('admin.crm.bulk_email.send');
    Route::post('/crm/bulk-email/signature', [CrmBulkEmailController::class, 'saveSignature'])->name('admin.crm.bulk_email.signature');
    Route::get('/crm/duplicates', [CrmDuplicatesController::class, 'index'])->name('admin.crm.duplicates');
    Route::post('/crm/duplicates/{id}/merge', [CrmDuplicatesController::class, 'merge'])->name('admin.crm.duplicates.merge');
    Route::post('/crm/duplicates/{id}/dismiss', [CrmDuplicatesController::class, 'dismiss'])->name('admin.crm.duplicates.dismiss');
    Route::post('/crm/duplicates/group/{primaryId}/merge-all', [CrmDuplicatesController::class, 'mergeGroup'])->name('admin.crm.duplicates.merge_group');
    Route::post('/crm/duplicates/merge-all', [CrmDuplicatesController::class, 'mergeAll'])->name('admin.crm.duplicates.merge_all');
    // Hot leads
    Route::get('/hot-leads', [HomeController::class, 'hotLeads'])->name('admin.hot.leads');

    // Blog / News posts
    Route::get('/posts',              [PostController::class, 'index'])->name('admin.posts.index');
    Route::get('/posts/create',       [PostController::class, 'create'])->name('admin.posts.create');
    Route::post('/posts/store',       [PostController::class, 'store'])->name('admin.posts.store');
    Route::post('/posts/preview',     [PostController::class, 'preview'])->name('admin.posts.preview');
    Route::get('/posts/edit/{id}',    [PostController::class, 'edit'])->name('admin.posts.edit');
    Route::post('/posts/update/{id}', [PostController::class, 'update'])->name('admin.posts.update');

    // SEO Tools
    Route::get('/seo-tools',                  [SeoToolsController::class, 'index'])->name('admin.seo.tools');
    Route::post('/seo-tools/save-template',   [SeoToolsController::class, 'saveTemplate'])->name('admin.seo.saveTemplate');
    Route::post('/seo-tools/save-global',     [SeoToolsController::class, 'saveGlobal'])->name('admin.seo.saveGlobal');
    Route::post('/seo-tools/reset-template',  [SeoToolsController::class, 'resetTemplate'])->name('admin.seo.resetTemplate');
    Route::post('/seo-tools/insert-field',    [SeoToolsController::class, 'insertField'])->name('admin.seo.insert');
    Route::post('/seo-tools/update-product',  [SeoToolsController::class, 'updateProductSeo'])->name('admin.seo.updateProductSeo');
    Route::get('/seo-tools/preview-ajax',     [SeoToolsController::class, 'previewAjax'])->name('admin.seo.previewAjax');

    // SEO Audit
    Route::get('/seo-audit',        [SeoAuditController::class, 'index'])->name('admin.seo.audit');
    Route::post('/seo-audit/run',   [SeoAuditController::class, 'runAudit'])->name('admin.seo.audit.run');
    Route::get('/seo-audit/export', [SeoAuditController::class, 'export'])->name('admin.seo.audit.export');

    // DigiKey Sync
    Route::get('/sync',          [SyncController::class, 'index'])->name('admin.sync.index');
    Route::post('/sync/run',     [SyncController::class, 'run'])->name('admin.sync.run');
    Route::post('/sync/pause',   [SyncController::class, 'pause'])->name('admin.sync.pause');
    Route::post('/sync/stop',    [SyncController::class, 'stop'])->name('admin.sync.stop');
    Route::get('/sync/status',   [SyncController::class, 'status'])->name('admin.sync.status');
    Route::get('/sync/log-tail', [SyncController::class, 'logTail'])->name('admin.sync.log-tail');
    Route::post('/sync/schedule',[SyncController::class, 'schedule'])->name('admin.sync.schedule');

    // Specs Editor
    Route::get('/specs-editor',       [SpecsController::class, 'index'])->name('admin.specs.index');
    Route::post('/specs-editor/load', [SpecsController::class, 'load'])->name('admin.specs.load');
    Route::post('/specs-editor/save', [SpecsController::class, 'save'])->name('admin.specs.save');

    // Harvest
    Route::get('/harvest',        [HarvestController::class, 'index'])->name('admin.harvest.index');
    Route::post('/harvest/start', [HarvestController::class, 'start'])->name('admin.harvest.start');
    Route::post('/harvest/stop',  [HarvestController::class, 'stop'])->name('admin.harvest.stop');
    Route::get('/harvest/status', [HarvestController::class, 'status'])->name('admin.harvest.status');
   
   
    //Calling
    Route::get('/voice/token', [VoiceController::class, 'token']);
    Route::get('/voice/app', [VoiceController::class, 'app'])->middleware('auth');
   
    
    
    //Page Editor
    
    Route::get('pages', [PagesController::class, 'index'])->name('admin.pages.index');
    Route::get('pages/{slug}/edit', [PagesController::class, 'edit'])->name('admin.pages.edit');
    Route::put('pages/{slug}', [PagesController::class, 'update'])->name('admin.pages.update');
Route::post('pages/upload-image', [PagesController::class, 'uploadImage'])->name('admin.pages.upload-image');
    
    //Visitor Tracking
    Route::get('/visitors',          [App\Http\Controllers\Admin\VisitorController::class, 'index'])->name('admin.visitors.index');
    Route::get('/visitors/{id}',     [App\Http\Controllers\Admin\VisitorController::class, 'visitorDetail'])->name('admin.visitors.detail');
    // API Usage
    Route::get('/api-usage', function (\Illuminate\Http\Request $request) {
        $provider = $request->get('provider', 'mouser');

        // ── Dynamic limit ────────────────────────────────────────────────────
        // DigiKey: count key pairs in config (each = 1,000 calls/day)
        // Mouser:  fixed 1,000 (adjust if you add multi-key support later)
        if ($provider === 'digikey') {
            $multiKeys = config('services.digikey.keys', []);
            $keyCount  = !empty($multiKeys) ? count($multiKeys) : 1;
        } else {
            $keyCount = 1;
        }
        $limit = $keyCount * 1000;

        // DigiKey resets its quota at 00:00 UTC (05:30 IST), not local midnight.
        // Align this dashboard's "today" window to that boundary so counts
        // match DigiKey's real billing day. Mouser keeps calendar-day boundaries
        // (reset time unconfirmed). app.timezone config is left untouched.
        if ($provider === 'digikey') {
            $resetTime = now()->copy()->setTime(5, 30, 0);
            $windowStart = now()->gte($resetTime) ? $resetTime : $resetTime->copy()->subDay();
            $windowEnd = $windowStart->copy()->addDay();
            $prevWindowStart = $windowStart->copy()->subDay();
            $prevWindowEnd = $windowStart;
        } else {
            $windowStart = today();
            $windowEnd = today()->copy()->addDay();
            $prevWindowStart = now()->subDay()->startOfDay();
            $prevWindowEnd = now()->subDay()->endOfDay()->addSecond();
        }

        // ── Standard counts ──────────────────────────────────────────────────
        $today     = ApiUsage::where('provider', $provider)->whereBetween('called_at', [$windowStart, $windowEnd])->count();
        $yesterday = ApiUsage::where('provider', $provider)->whereBetween('called_at', [$prevWindowStart, $prevWindowEnd])->count();
        $total     = ApiUsage::where('provider', $provider)->count();
        $percent   = $limit > 0 ? round(($today / $limit) * 100, 2) : 0;

        // ── Monthly calendar data ────────────────────────────────────────────
        $monthlyUsage = ApiUsage::where('provider', $provider)
            ->whereMonth('called_at', now()->month)
            ->whereYear('called_at', now()->year)
            ->selectRaw('DATE(called_at) as call_date, COUNT(*) as cnt')
            ->groupBy('call_date')
            ->pluck('cnt', 'call_date');

        // ── Per-function breakdown for today ─────────────────────────────────
        $callerBreakdown = ApiUsage::where('provider', $provider)
            ->whereBetween('called_at', [$windowStart, $windowEnd])
            ->selectRaw('controller, endpoint, COUNT(*) as calls')
            ->groupBy('controller', 'endpoint')
            ->orderByDesc('calls')
            ->get();

        // ── Per-key breakdown for today (DigiKey only — key_index is null
        //    for single-key providers like Mouser) ───────────────────────────
        $keyBreakdown = ApiUsage::where('provider', $provider)
            ->whereBetween('called_at', [$windowStart, $windowEnd])
            ->whereNotNull('key_index')
            ->selectRaw('key_index, COUNT(*) as calls,
                         SUM(CASE WHEN status_code = 429 THEN 1 ELSE 0 END) as throttled_calls,
                         SUM(CASE WHEN status_code >= 200 AND status_code < 300 THEN 1 ELSE 0 END) as successful_calls')
            ->groupBy('key_index')
            ->orderBy('key_index')
            ->get();

        // ── Exhaustion events (429 with Retry-After > 3 hours) ───────────────
        // Surfaced for today by default, but query is open-ended so you can
        // see historical exhaustion events too — most recent first.
        $exhaustionThreshold = \App\Services\DigiKeyService::EXHAUSTION_THRESHOLD_SECONDS ?? 10800;

        $exhaustionEvents = ApiUsage::where('provider', $provider)
            ->where('status_code', 429)
            ->where('retry_after_seconds', '>', $exhaustionThreshold)
            ->whereBetween('called_at', [$windowStart, $windowEnd])
            ->orderByDesc('called_at')
            ->get();

        // All 429s today (not just exhaustion-level) for a fuller picture
        $allThrottleEvents = ApiUsage::where('provider', $provider)
            ->where('status_code', 429)
            ->whereBetween('called_at', [$windowStart, $windowEnd])
            ->orderByDesc('called_at')
            ->get();

        // ── Raw call log for today ────────────────────────────────────────────
        $todayCalls = ApiUsage::where('provider', $provider)
            ->whereBetween('called_at', [$windowStart, $windowEnd])
            ->latest()
            ->get();

        return view('admin.api_usage', compact(
            'today', 'yesterday', 'total', 'limit',
            'percent', 'monthlyUsage', 'todayCalls',
            'provider', 'keyCount', 'callerBreakdown',
            'keyBreakdown', 'exhaustionEvents', 'allThrottleEvents',
            'exhaustionThreshold'
        ));
    })->name('admin.api.usage');
    // Test expand (keep behind auth so it's not publicly accessible)
    Route::get('/test-expand', function () {
        $apiKey = config('services.mouser.key');
        if (!$apiKey) {
            return response()->json(['error' => 'Mouser API key not configured'], 500);
        }

        $keywords = ['PIC1','STM3','SN74','TPS6','TPS7','TPS5','TPS2','ATME','XC3S','BCM5'];
        $totalInserted = 0;
        $details = [];

        foreach ($keywords as $keyword) {
            $url = "https://api.mouser.com/api/v1/search/keyword?apiKey={$apiKey}";
            $payload = [
                'SearchByKeywordRequest' => [
                    'keyword'                    => $keyword,
                    'records'                    => 100,
                    'startingRecord'             => 0,
                    'searchOptions'              => 'None',
                    'searchWithYourSignUpLanguage' => false,
                ],
            ];

            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
                CURLOPT_POSTFIELDS     => json_encode($payload),
                CURLOPT_TIMEOUT        => 30,
            ]);
            $response = curl_exec($ch);
            curl_close($ch);

            $parts = json_decode($response, true)['SearchResults']['Parts'] ?? [];
            $insertedThisKeyword = 0;

            foreach ($parts as $part) {
                $productKey = strtoupper(trim(
                    $part['ManufacturerPartNumber'] ?? $part['MouserPartNumber'] ?? ''
                ));
                if (!$productKey) continue;

                $exists = CachedProduct::where('product_key', $productKey)->exists();

                CachedProduct::updateOrCreate(
                    ['product_key' => $productKey],
                    [
                        'name'           => $productKey,
                        'description'    => $part['Description']  ?? null,
                        'manufacturer'   => $part['Manufacturer'] ?? null,
                        'category'       => is_array($part['Category'] ?? null)
                            ? ($part['Category']['Name'] ?? null)
                            : ($part['Category'] ?? null),
                        'image'          => $part['ImagePath'] ?? null,
                        'quantity'       => $part['AvailabilityInStock'] ?? 0,
                        'raw_data'       => json_encode($part),
                        'is_synced'      => 1,
                        'last_synced_at' => now(),
                    ]
                );

                if (!$exists) { $insertedThisKeyword++; $totalInserted++; }
            }

            ApiUsage::create([
                'provider'   => 'mouser',
                'called_at'  => now('UTC'),
                'endpoint'   => 'keyword-expand-test',
                'query'      => $keyword,
                'controller' => 'test-expand',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            $details[] = [
                'keyword'        => $keyword,
                'returned_parts' => count($parts),
                'new_inserted'   => $insertedThisKeyword,
            ];
        }

        return response()->json([
            'keywords_tested'          => count($keywords),
            'total_new_parts_inserted' => $totalInserted,
            'details'                  => $details,
        ]);
    })->name('admin.test.expand');

});
Route::post('/admin/crm/{id}/send-initial-followup', [App\Http\Controllers\Admin\CrmPipelineController::class, 'sendInitialFollowup'])
    ->name('admin.crm.send-initial-followup')
    ->middleware('auth');

Route::get("/admin/general-settings", [App\Http\Controllers\Admin\GeneralSettingsController::class, "index"])->name("admin.general-settings")->middleware('auth');
Route::post("/admin/general-settings", [App\Http\Controllers\Admin\GeneralSettingsController::class, "update"])->name("admin.general-settings.update")->middleware('auth');

Route::get('/admin/logo', [App\Http\Controllers\Admin\LogoController::class, 'index'])->name('admin.logo')->middleware('auth');
Route::post('/admin/update_logo', [App\Http\Controllers\Admin\LogoController::class, 'update'])->name('admin.update_logo')->middleware('auth');

// Contact Import Routes
Route::middleware(['auth'])->prefix('admin/crm/import')->group(function () {
    Route::get('/', [App\Http\Controllers\ContactImportController::class, 'index'])->name('admin.crm.import.index');
    Route::post('/preview', [App\Http\Controllers\ContactImportController::class, 'preview'])->name('admin.crm.import.preview');
    Route::post('/store', [App\Http\Controllers\ContactImportController::class, 'store'])->name('admin.crm.import.store');
    Route::get('/history', [App\Http\Controllers\ContactImportController::class, 'history'])->name('admin.crm.import.history');
});

// Get contacts for bulk email (AJAX)
Route::get('/admin/crm/get-contacts', [App\Http\Controllers\ContactImportController::class, 'getContacts'])->middleware('auth');