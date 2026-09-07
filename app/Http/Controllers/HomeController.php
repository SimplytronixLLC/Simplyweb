<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request; 
use DB;
use Hash;
use File;
use Mail;
use Mpdf\Mpdf;
use Mpdf\MpdfException;
use App\Helpers\Common;  
use App\Models\Category;
use App\Models\Quote;
use Illuminate\Support\Str;
use App\Models\CachedProduct;
use Illuminate\Support\Facades\Http;
use App\Models\Post;

class HomeController extends Controller{

    public function index()
    {
        $listing = Category::whereNull('parent_id')
                            ->orderBy('name')
                            ->limit(10)
                            ->get();
    
        $popularProducts = CachedProduct::whereNotNull('description')
            ->where('description', 'LIKE', '%IC%')
            ->inRandomOrder()
            ->limit(8)
            ->get();
    
        return view('index', compact('listing', 'popularProducts'));
    }
    
    public function about_us() { return view('about-us'); } 
    public function contact_us() { return view('contact_us'); }
    public function login() { return view('login.login'); }
    public function sign_up() { return view('login.sign_up'); }

    public function home_category(Request $request)
    {
        $data['listing'] = Category::whereNull('parent_id')
                                    ->orderBy('name')
                                    ->get();
    
        return view('category', $data);
    }

    public function category_details($slug)
{
    $category = Category::where('slug', $slug)->firstOrFail();

    $hasProducts = CachedProduct::where('category_id', $category->id)->exists();

    if ($hasProducts) {
        $query = CachedProduct::where('category_id', $category->id);

        if (request()->filled('manufacturer')) {
            $query->where('manufacturer', request('manufacturer'));
        }

        $products = $query
            ->orderByRaw('quantity > 0 DESC')
            ->inRandomOrder()
            ->paginate(25)
            ->withQueryString();

        $manufacturers = CachedProduct::where('category_id', $category->id)
            ->whereNotNull('manufacturer')
            ->distinct()
            ->orderBy('manufacturer')
            ->pluck('manufacturer');

        return view('category.level3', compact('category', 'products', 'manufacturers'));
    }

    // No direct products — show children
    $children = Category::where('parent_id', $category->id)->orderBy('name')->get();

    if ($category->level == 1) return view('category.level1', compact('category', 'children'));
    if ($category->level == 2) return view('category.level2', compact('category', 'children'));

    abort(404);
}
    
    public function category_listing(Request $request)
    {
        $q = strtoupper(trim($request->get('q', '')));
    
        // Get all distinct first letters for the A-Z sidebar
        $listing = Category::selectRaw('UPPER(LEFT(name, 1)) as first_letter')
                    ->distinct()
                    ->orderByRaw('UPPER(LEFT(name, 1))')
                    ->get();
    
        // If a letter is selected, filter; otherwise show all root categories
        $records = $q
            ? Category::whereRaw('UPPER(LEFT(name, 1)) = ?', [$q])
                       ->orderBy('name')
                       ->get()
            : Category::orderBy('name')->get();
    
        return view('category', compact('listing', 'records', 'q'));
    }

    public function home_products()
    {
        $data['listing'] = Category::whereNull('parent_id')
                                    ->orderBy('name')
                                    ->get();
    
        return view('products', $data);
    }
        
    public function terms()
    {
        return view('pages.terms');
    }

    public function contact_us_submit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'subject' => 'required',
            'message' => 'required',
        ]);

        return back()->with('success', 'Your message has been sent successfully!');
    }

    public function newsAlerts()
    {
        $posts = Post::where('is_published', 1)->latest()->paginate(9);
        return view('news_alerts', compact('posts'));
    }

    public function home_shop()
    {  
        return view('shop');  
    }

    public function get_a_quote(Request $request)
    {  
        $data = $request->all();
        return view('get_a_quote', $data);
    }
   
    public function product_details(Request $request, $id)
    {
        $data['id'] = $id;
        return view('product_details', $data);
    }

   public function get_a_quote_submit(Request $request)
{
    $request->validate([
        'g-recaptcha-response' => 'required'
    ]);

    $captchaResponse = Http::asForm()->post(
        'https://www.google.com/recaptcha/api/siteverify',
        [
            'secret'   => env('RECAPTCHA_SECRET_KEY'),
            'response' => $request->input('g-recaptcha-response'),
            'remoteip' => $request->ip(),
        ]
    );

    if (!($captchaResponse->json()['success'] ?? false)) {
        return redirect()->back()
            ->with('fail', 'Captcha verification failed. Please try again.');
    }

    $email = strtolower(trim($request->email));

    \DB::table('leads')->updateOrInsert(
        ['email' => $email],
        [
            'visitor_id' => $request->cookie('visitor_id'),
            'email_hash' => hash('sha256', $email),
            'updated_at' => now(),
            'created_at' => now(),
        ]
    );

    $order_id = 'SMT' . rand(1234567, 9876552);

    $quote = Quote::create([
        'order_id'    => $order_id,
        'name'        => $request->name,
        'email'       => $request->email,
        'phone'       => $request->phone,
        'company'     => $request->company,
        'part_number' => $request->part_number,
        'quantity'    => $request->quantity,
        'comments'    => $request->comments
    ]);

    if (!$quote) {
        return redirect()->back()
            ->with('fail', 'Failed to save the quote. Please try again.');
    }

    $array = [
        'name'        => $request->name,
        'email'       => $request->email,
        'phone'       => $request->phone,
        'company'     => $request->company,
        'part_number' => $request->part_number,
        'order_id'    => $order_id,
        'quantity'    => $request->quantity,
        'comments'    => $request->comments,
        'title'       => 'Received A Quote from ' . $request->name,
        'link'        => url('/track-order?id=' . $order_id),
    ];

    try {
        // Admin notification
        Mail::send('email.get_a_quote', $array, function ($message) use ($array) {
            $message->to('sales@simplytronix.com')
                    ->subject($array['title'])
                    ->from('info@simplytronix.com', 'Simplytronix');
        });

        // User confirmation
        Mail::send('email.user_quote_confirmation', $array, function ($message) use ($array) {
            $message->to($array['email'])
                    ->subject('Your Quote Request Confirmation')
                    ->from('info@simplytronix.com', 'Simplytronix');
        });

    } catch (\Exception $e) {
        \Log::error('Quote mail failed: ' . $e->getMessage());
    }

    return redirect()->route('quote.thankyou');
}

    public function chatbotLead(Request $request)
    {
        $data = $request->only(['name', 'email', 'company', 'phone', 'part_number']);

        Mail::send('email.admin_chatbot_notification', ['data' => $data], function ($message) {
            $message->to('info@simplytronix.com')
                    ->subject('New Lead from Chatbot')
                    ->from('info@simplytronix.com', 'Simplytronix');
        });

        return response()->json(['status' => 'ok']);
    }

    public function track_order()
    {
        $data['quotation'] = array();
        return view('track-order', $data);
    }
    
public function available_stock(Request $request)
{
    $baseQuery = CachedProduct::query();

    if ($request->filled('level1')) {

        $level2Ids = Category::where('parent_id', $request->level1)->pluck('id');
        $level3Ids = Category::whereIn('parent_id', $level2Ids)->pluck('id');

        $allIds = collect([$request->level1])
            ->merge($level2Ids)
            ->merge($level3Ids);

        $baseQuery->whereIn('category_id', $allIds);
    }

    if ($request->filled('level2')) {

        $level3Ids = Category::where('parent_id', $request->level2)->pluck('id');

        $allIds = collect([$request->level2])->merge($level3Ids);

        $baseQuery->whereIn('category_id', $allIds);
    }

    if ($request->filled('manufacturer')) {
        $baseQuery->where('manufacturer', $request->manufacturer);
    }

        if ($request->filled('search')) {
        $baseQuery->where('manufacturer', 'LIKE', '%' . $request->input('search') . '%');
    }
    $products = (clone $baseQuery)
        ->with(['categoryRelation.parent.parent'])
        ->orderByRaw('quantity > 0 DESC')
        ->inRandomOrder()
        ->paginate(25)
        ->withQueryString();

    $level1Categories = Category::where('level', 1)
        ->orderBy('name')
        ->get();

    $level2Categories = collect();

    if ($request->filled('level1')) {
        $level2Categories = Category::where('parent_id', $request->level1)
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    $manufacturers = (clone $baseQuery)
        ->whereNotNull('manufacturer')
        ->where('manufacturer', '!=', '')
        ->distinct()
        ->orderBy('manufacturer')
        ->pluck('manufacturer');

    return view('available_stock', compact(
        'products',
        'level1Categories',
        'level2Categories',
        'manufacturers'
    ));
}

public function getLevel2($parentId)
{
    $categories = Category::where('parent_id', $parentId)
        ->orderBy('name')
        ->get(['id','name']);

    return response()->json($categories);
}

    public function submit_track_order(Request $request)
    { 
        $orderID = $request->input('order_id');
        $quotation = Quote::where('order_id', $orderID)->first();  

        if (!empty($quotation)) { 
            $data['quotation'] = $quotation;
            return view('track-order', $data);
        } else {
            return redirect()->back()->with('danger', 'Order ID not found. Please try again.');
        }
    }

    public function hotLeads()
    {
        $quoteTable = (new \App\Models\Quote)->getTable();

        $hotLeads = \DB::table('leads as l')
            ->join('visitor_searches as vs', 'l.visitor_id', '=', 'vs.visitor_id')
            ->leftJoin($quoteTable . ' as q', 'l.email', '=', 'q.email')
            ->select(
                'l.email',
                'l.visitor_id',
                \DB::raw('COUNT(vs.id) as total_searches'),
                \DB::raw('COUNT(DISTINCT vs.part_number) as unique_parts'),
                \DB::raw('MAX(vs.created_at) as last_search')
            )
            ->whereNull('q.id')
            ->groupBy('l.email', 'l.visitor_id')
            ->havingRaw('COUNT(vs.id) >= 3')
            ->orderByDesc('last_search')
            ->get();

        return view('admin.hot_leads', compact('hotLeads'));
    }
    
    public function qualityAssurance()
    {
        return view('quality-assurance');
    }

    public function stock_alert_signup(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'mpn'   => 'nullable|string|max:100',
        ]);

        $email = strtolower(trim($request->email));

        \DB::table('leads')->updateOrInsert(
            ['email' => $email],
            [
                'visitor_id' => $request->cookie('visitor_id'),
                'email_hash' => hash('sha256', $email),
                'source'     => 'stock_alert',
                'part_number'=> $request->mpn,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        return response()->json(['success' => true]);
    }

}