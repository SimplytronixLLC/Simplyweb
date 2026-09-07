<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class LeadIntelligenceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $minScore = (int) $request->get('min_score', 20);
        $country  = $request->get('country');
        $contact_status = $request->get('contact_status', 'uncontacted');
        $sortBy   = $request->get('sort', 'engagement_score');
        $sortDir  = $request->get('dir', 'desc');

        // Base query
        $query = DB::table('visitor_profiles as vp')
            ->leftJoin('visitor_behaviors as vb', 'vp.visitor_id', '=', 'vb.visitor_id')
            ->leftJoin('visitor_contacts as vc', 'vp.visitor_id', '=', 'vc.visitor_id')
            ->where('vp.is_bot', false)
            ->select(
                'vp.visitor_id', 'vp.ip_address', 'vp.ip_org', 'vp.ip_city',
                'vp.ip_country', 'vp.device_type', 'vp.browser_name',
                'vp.engagement_score', 'vp.first_visit_at', 'vp.last_visit_at',
                'vb.total_visits', 'vb.product_views', 'vb.total_searches',
                'vb.unique_products_viewed', 'vb.quote_requests',
                'vb.most_viewed_manufacturer',
                'vc.email', 'vc.name', 'vc.company', 'vc.contact_source'
            );

        // Filters
        if ($minScore > 0) {
            $query->where('vp.engagement_score', '>=', $minScore);
        }

        if ($country) {
            $query->where('vp.ip_country', $country);
        }

        if ($contact_status === 'contacted') {
            $query->whereNotNull('vc.email');
        } elseif ($contact_status === 'uncontacted') {
            $query->whereNull('vc.email');
        }

        // Sort
        $validSort = ['engagement_score', 'last_visit_at', 'product_views', 'total_visits'];
        if (!in_array($sortBy, $validSort)) $sortBy = 'engagement_score';
        $query->orderBy("vp.{$sortBy}", strtoupper($sortDir) === 'ASC' ? 'asc' : 'desc');

        $leads = $query->paginate(50);

        // Summary stats
        $totalProspects = DB::table('visitor_profiles')->count();
        $highEngagement = DB::table('visitor_profiles')->where('engagement_score', '>=', 50)->count();
        $byCountry      = DB::table('visitor_profiles')
            ->select('ip_country', DB::raw('COUNT(*) as count'))
            ->whereNotNull('ip_country')
            ->groupBy('ip_country')
            ->orderByDesc('count')
            ->limit(10)
            ->pluck('count', 'ip_country');

        return view('admin.leads.intelligence', compact(
            'leads', 'minScore', 'country', 'contact_status', 'sortBy',
            'totalProspects', 'highEngagement', 'byCountry'
        ));
    }

    public function leadDetail(string $visitorId)
    {
        $profile = DB::table('visitor_profiles')
            ->where('visitor_id', $visitorId)
            ->first();

        if (!$profile) abort(404);

        $behavior = DB::table('visitor_behaviors')
            ->where('visitor_id', $visitorId)
            ->first();

        $interests = DB::table('visitor_interests')
            ->where('visitor_id', $visitorId)
            ->orderByDesc('view_count')
            ->get();

        $contact = DB::table('visitor_contacts')
            ->where('visitor_id', $visitorId)
            ->first();

        $outreach = DB::table('visitor_outreach_logs')
            ->where('visitor_id', $visitorId)
            ->orderByDesc('created_at')
            ->get();

        $pageviews = DB::table('visitor_pageviews')
            ->where('visitor_id', $visitorId)
            ->orderByDesc('created_at')
            ->limit(30)
            ->get();

        return view('admin.leads.detail', compact(
            'profile', 'behavior', 'interests', 'contact', 'outreach', 'pageviews', 'visitorId'
        ));
    }

    public function sendOutreach(Request $request, string $visitorId)
    {
        $validated = $request->validate([
            'email'   => 'required|email',
            'type'    => 'required|in:email,phone,linkedin',
            'subject' => 'required_if:type,email|string|max:200',
            'message' => 'required|string|max:5000',
        ]);

        $profile = DB::table('visitor_profiles')
            ->where('visitor_id', $visitorId)
            ->first();

        if (!$profile) abort(404);

        // Save contact info if new
        $existing = DB::table('visitor_contacts')
            ->where('visitor_id', $visitorId)
            ->first();

        if (!$existing) {
            DB::table('visitor_contacts')->insert([
                'visitor_id'     => $visitorId,
                'email'          => $validated['email'],
                'contact_source' => 'outreach',
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        }

        // Log outreach attempt
        DB::table('visitor_outreach_logs')->insert([
            'visitor_id'      => $visitorId,
            'email'           => $validated['email'],
            'outreach_type'   => $validated['type'],
            'message_subject' => $validated['subject'] ?? null,
            'message_body'    => $validated['message'],
            'status'          => 'pending',
            'sent_by'         => auth()->user()->name,
            'sent_at'         => now(),
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        // Actually send if email
        if ($validated['type'] === 'email') {
            try {
                // Send email here
                Mail::raw($validated['message'], function ($message) use ($validated) {
                    $message->to($validated['email'])
                        ->subject($validated['subject'])
                        ->from(config('mail.from.address'));
                });

                DB::table('visitor_outreach_logs')
                ->where('id', DB::table('visitor_outreach_logs')
                    ->where('visitor_id', $visitorId)
                    ->latest('created_at')
                    ->value('id'))
                ->update(['status' => 'sent']);

                return back()->with('success', 'Email sent successfully');
            } catch (\Exception $e) {
                return back()->with('error', 'Failed to send: ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Outreach logged for ' . $validated['type']);
    }

    public function bulkOutreach(Request $request)
    {
        $visitorIds = $request->input('visitor_ids', []);
        $message    = $request->input('message');
        $subject    = $request->input('subject');

        if (empty($visitorIds) || empty($message)) {
            return back()->with('error', 'No visitors selected or message empty');
        }

        $contacts = DB::table('visitor_contacts')
            ->whereIn('visitor_id', $visitorIds)
            ->whereNotNull('email')
            ->get();

        $sent = 0;

        foreach ($contacts as $contact) {
            try {
                Mail::raw($message, function ($msg) use ($contact, $subject) {
                    $msg->to($contact->email)
                        ->subject($subject)
                        ->from(config('mail.from.address'));
                });

                DB::table('visitor_outreach_logs')->insert([
                    'visitor_id'     => $contact->visitor_id,
                    'email'          => $contact->email,
                    'outreach_type'  => 'email',
                    'message_subject' => $subject,
                    'message_body'   => $message,
                    'status'         => 'sent',
                    'sent_by'        => auth()->user()->name,
                    'sent_at'        => now(),
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);

                $sent++;
            } catch (\Exception $e) {
                \Log::error("Bulk outreach failed for {$contact->email}: " . $e->getMessage());
            }
        }

        return back()->with('success', "Sent to $sent prospects");
    }

    public function exportLeads(Request $request)
    {
        $minScore = (int) $request->get('min_score', 20);

        $leads = DB::table('visitor_profiles as vp')
            ->leftJoin('visitor_behaviors as vb', 'vp.visitor_id', '=', 'vb.visitor_id')
            ->leftJoin('visitor_contacts as vc', 'vp.visitor_id', '=', 'vc.visitor_id')
            ->select(
                'vp.visitor_id', 'vp.ip_org', 'vp.ip_city', 'vp.ip_country',
                'vp.device_type', 'vp.browser_name', 'vp.engagement_score',
                'vp.first_visit_at', 'vp.last_visit_at',
                'vb.total_visits', 'vb.product_views', 'vb.total_searches',
                'vb.most_viewed_manufacturer',
                'vc.email', 'vc.name', 'vc.company'
            )
            ->where('vp.engagement_score', '>=', $minScore)
            ->where('vp.is_bot', false)
            ->orderByDesc('vp.engagement_score')
            ->get();

        $csv = "Visitor ID,Email,Name,Company,Engagement Score,City,Country,Device,Browser,Total Visits,Product Views,Searches,Top Manufacturer,Last Visit\n";

        foreach ($leads as $lead) {
            $csv .= implode(',', [
                $lead->visitor_id,
                $lead->email ?? '',
                $lead->name ?? '',
                $lead->company ?? '',
                $lead->engagement_score,
                $lead->ip_city ?? '',
                $lead->ip_country ?? '',
                $lead->device_type,
                $lead->browser_name,
                $lead->total_visits,
                $lead->product_views,
                $lead->total_searches,
                $lead->most_viewed_manufacturer ?? '',
                $lead->last_visit_at,
            ]) . "\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="high-value-leads.csv"');
    }

    public function stockAlerts(Request $request)
    {
        $search = $request->get('search');

        $query = DB::table('leads')
            ->where('source', 'stock_alert')
            ->orderByDesc('created_at');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhere('part_number', 'like', "%{$search}%");
            });
        }

        $alerts = $query->paginate(50);
        $totalAlerts = DB::table('leads')->where('source', 'stock_alert')->count();

        return view('admin.leads.stock-alerts', compact('alerts', 'search', 'totalAlerts'));
    }

}