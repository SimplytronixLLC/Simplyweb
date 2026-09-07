@extends('admin.includes.masterpage-admin')

@section('content')
<style>
    .li-card{background:#fff;border-radius:8px;border:1px solid #e5e7eb;padding:20px;margin-bottom:18px}
    .info-row{display:flex;padding:6px 0;border-bottom:1px solid #f8fafc;font-size:13px}
    .info-row .key{width:140px;color:#64748b;flex-shrink:0}
    .info-row .val{color:#1e293b;font-weight:500;word-break:break-all}
    .score-circle{width:64px;height:64px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:700;color:#fff;margin:0 auto 8px}
    .timeline-dot{width:10px;height:10px;border-radius:50%;flex-shrink:0;margin-top:4px}
    .interest-bar{background:#e2e8f0;border-radius:99px;height:6px;overflow:hidden;margin-top:3px}
    .interest-fill{height:100%;background:#7c3aed;border-radius:99px}
    .outreach-status{padding:2px 8px;border-radius:99px;font-size:11px;font-weight:600}
</style>

<div class="d-flex align-items-center gap-3 mb-3">
    <a href="{{ route('admin.leads.index') }}"
       style="font-size:13px;color:#64748b;text-decoration:none">← Lead Intelligence</a>
    <h4 style="margin:0">Visitor Profile</h4>
    @if($behavior && $behavior->has_submitted_quote)
    <span style="background:#dcfce7;color:#15803d;padding:3px 10px;border-radius:99px;font-size:12px;font-weight:600">
        ✓ Converted to RFQ
    </span>
    @endif
</div>

<div class="row">

    {{-- LEFT COL --}}
    <div class="col-sm-4">

        {{-- Score & Identity --}}
        <div class="li-card" style="text-align:center;padding:24px">
            @php
                $score = $profile->engagement_score ?? 0;
                $bgColor = $score >= 50 ? '#dc2626' : ($score >= 20 ? '#d97706' : '#0369a1');
                $label   = $score >= 50 ? '🔥 Hot Lead' : ($score >= 20 ? '⚡ Warm Lead' : '❄️ Cold Lead');
            @endphp
            <div class="score-circle" style="background:{{ $bgColor }}">{{ $score }}</div>
            <div style="font-weight:700;font-size:15px;margin-bottom:2px">{{ $label }}</div>
            <div style="font-size:11px;color:#94a3b8;font-family:monospace">{{ $visitorId }}</div>

            @if($contact && $contact->email)
            <div style="margin-top:12px;padding:10px;background:#f0fdf4;border-radius:6px">
                <div style="font-size:13px;font-weight:700;color:#15803d">{{ $contact->email }}</div>
                @if($contact->name)
                <div style="font-size:12px;color:#64748b">{{ $contact->name }}</div>
                @endif
                @if($contact->company)
                <div style="font-size:12px;color:#64748b">{{ $contact->company }}</div>
                @endif
            </div>
            @endif

            @if($profile->ip_org)
            <div style="margin-top:8px">
                <span style="background:#ede9fe;color:#7c3aed;padding:3px 10px;border-radius:99px;font-size:12px;font-weight:600">
                    🏢 {{ $profile->ip_org }}
                </span>
            </div>
            @endif
        </div>

        {{-- Profile Details --}}
        <div class="li-card">
            <div style="font-size:14px;font-weight:600;margin-bottom:12px">Profile Details</div>
            <div class="info-row">
                <div class="key">IP Address</div>
                <div class="val" style="font-family:monospace">{{ $profile->ip_address ?? '—' }}</div>
            </div>
            <div class="info-row">
                <div class="key">Organization</div>
                <div class="val">{{ $profile->ip_org ?? '—' }}</div>
            </div>
            <div class="info-row">
                <div class="key">ISP</div>
                <div class="val">{{ $profile->ip_isp ?? '—' }}</div>
            </div>
            <div class="info-row">
            <div class="key">ASN</div>
                <div class="val" style="font-family:monospace">{{ $profile    ->ip_asn ?? '—' }}</div>
            </div>
            <div class="info-row">
                <div class="key">AS Name</div>
                <div class="val">{{ $profile->ip_asname ?? '—' }}</div>
            </div>
            <div class="info-row">
                <div class="key">First Referrer</div>
                <div class="val" style="font-size:11px">{{ $profile->first_referrer ?? 'Direct' }}</div>
            </div>
            <div class="info-row">
                <div class="key">Landing Page</div>
                <div class="val" style="font-size:11px">{{ $profile->first_landing_page ?? '—' }}</div>
            </div>
            <div class="info-row">
                <div class="key">Avg Session</div>
                <div class="val">
                    @php
                        $b = $behavior;
                        $secs = $profile->total_session_seconds ?? 0;
                        $visits = $b->total_visits ?? 1;
                        $avg = $visits > 0 ? round($secs / $visits) : 0;
                        echo $avg >= 60
                            ? floor($avg/60).'m '.($avg%60).'s'
                            : $avg.'s';
                    @endphp
                </div>
            </div>
            <div class="info-row">
                <div class="key">Country</div>
                <div class="val">{{ $profile->ip_country ?? '—' }}</div>
            </div>
            <div class="info-row">
                <div class="key">Region</div>
                <div class="val">{{ $profile->ip_region ?? '—' }}</div>
            </div>
            <div class="info-row">
                <div class="key">City</div>
                <div class="val">{{ $profile->ip_city ?? '—' }}</div>
            </div>
            <div class="info-row">
                <div class="key">Timezone</div>
                <div class="val">{{ $profile->ip_timezone ?? '—' }}</div>
            </div>
            <div class="info-row">
                <div class="key">Device</div>
                <div class="val" style="text-transform:capitalize">{{ $profile->device_type ?? '—' }}</div>
            </div>
            <div class="info-row">
                <div class="key">OS</div>
                <div class="val">{{ $profile->os_name ?? '—' }} {{ $profile->os_version ?? '' }}</div>
            </div>
            <div class="info-row">
                <div class="key">Browser</div>
                <div class="val">{{ $profile->browser_name ?? '—' }} {{ $profile->browser_version ?? '' }}</div>
            </div>
            <div class="info-row">
                <div class="key">Language</div>
                <div class="val">{{ $profile->language ?? '—' }}</div>
            </div>
            <div class="info-row">
                <div class="key">First Visit</div>
                <div class="val">{{ $profile->first_visit_at ? \Carbon\Carbon::parse($profile->first_visit_at)->format('M d Y, H:i') : '—' }}</div>
            </div>
            <div class="info-row" style="border-bottom:none">
                <div class="key">Last Visit</div>
                <div class="val">{{ $profile->last_visit_at ? \Carbon\Carbon::parse($profile->last_visit_at)->diffForHumans() : '—' }}</div>
            </div>
        </div>

        {{-- Behavior Stats --}}
        @if($behavior)
        <div class="li-card">
            <div style="font-size:14px;font-weight:600;margin-bottom:12px">Behavior Stats</div>
            @php
                $stats = [
                    ['Total Visits',         $behavior->total_visits],
                    ['Total Pageviews',       $behavior->total_pageviews],
                    ['Searches',             $behavior->total_searches],
                    ['Product Views',         $behavior->product_views],
                    ['Unique Products',       $behavior->unique_products_viewed],
                    ['Quote Requests',        $behavior->quote_requests],
                ];
            @endphp
            @foreach($stats as [$label, $val])
            <div class="info-row">
                <div class="key">{{ $label }}</div>
                <div class="val">{{ $val ?? 0 }}</div>
            </div>
            @endforeach
            @if($behavior->most_viewed_manufacturer)
            <div class="info-row" style="border-bottom:none">
                <div class="key">Top Manufacturer</div>
                <div class="val" style="color:#7c3aed">{{ $behavior->most_viewed_manufacturer }}</div>
            </div>
            @endif
        </div>
        @endif

    </div>

    {{-- RIGHT COL --}}
    <div class="col-sm-8">

        {{-- Product Interests --}}
        @if($interests->count())
        <div class="li-card">
            <div style="font-size:14px;font-weight:600;margin-bottom:14px">
                Product Interests ({{ $interests->count() }} products)
            </div>
            @php $maxViews = $interests->max('view_count'); @endphp
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
                @foreach($interests->take(12) as $interest)
                @php
                    $mfSlug  = \Illuminate\Support\Str::slug($interest->manufacturer ?? '');
                    $partEnc = rawurlencode(strtoupper($interest->product_key ?? ''));
                    $pct = $maxViews > 0 ? round($interest->view_count / $maxViews * 100) : 0;
                @endphp
                <div style="background:#f8fafc;border-radius:6px;padding:10px">
                    <div style="display:flex;justify-content:space-between;align-items:center">
                        <a href="{{ url('product/'.$mfSlug.'/'.$partEnc) }}" target="_blank"
                           style="font-weight:700;color:#1e293b;font-size:13px;text-decoration:none">
                            {{ $interest->product_key }}
                        </a>
                        <span style="font-size:11px;background:#ede9fe;color:#7c3aed;padding:1px 6px;border-radius:99px">
                            {{ $interest->view_count }}x
                        </span>
                    </div>
                    @if($interest->manufacturer)
                    <div style="font-size:11px;color:#64748b;margin-top:2px">{{ $interest->manufacturer }}</div>
                    @endif
                    <div class="interest-bar" style="margin-top:6px">
                        <div class="interest-fill" style="width:{{ $pct }}%"></div>
                    </div>
                    <div style="font-size:11px;color:#94a3b8;margin-top:3px">
                        Last viewed {{ \Carbon\Carbon::parse($interest->last_viewed_at)->diffForHumans() }}
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Outreach --}}
        <div class="li-card">
            <div style="font-size:14px;font-weight:600;margin-bottom:14px">Send Outreach</div>
            <form method="POST" action="{{ route('admin.leads.outreach', $visitorId) }}">
                @csrf
                @if(session('success'))
                <div style="background:#dcfce7;color:#15803d;padding:8px 12px;border-radius:6px;font-size:13px;margin-bottom:12px">
                    {{ session('success') }}
                </div>
                @endif
                @if(session('error'))
                <div style="background:#fee2e2;color:#dc2626;padding:8px 12px;border-radius:6px;font-size:13px;margin-bottom:12px">
                    {{ session('error') }}
                </div>
                @endif
                <div class="row">
                    <div class="col-sm-6">
                        <div style="margin-bottom:10px">
                            <label style="font-size:12px;color:#64748b;display:block;margin-bottom:4px">
                                Email Address *
                            </label>
                            <input type="email" name="email"
                                   value="{{ $contact->email ?? '' }}"
                                   placeholder="prospect@company.com"
                                   style="width:100%;border:1px solid #e2e8f0;border-radius:5px;padding:7px 10px;font-size:13px">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div style="margin-bottom:10px">
                            <label style="font-size:12px;color:#64748b;display:block;margin-bottom:4px">
                                Outreach Type
                            </label>
                            <select name="type"
                                    style="width:100%;border:1px solid #e2e8f0;border-radius:5px;padding:7px 10px;font-size:13px"
                                    onchange="toggleType(this.value)">
                                <option value="email">Email</option>
                                <option value="linkedin">LinkedIn (log only)</option>
                                <option value="phone">Phone (log only)</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div id="email-fields">
                    <div style="margin-bottom:10px">
                        <label style="font-size:12px;color:#64748b;display:block;margin-bottom:4px">Subject</label>
                        <input type="text" name="subject"
                               value="Parts Inquiry — Simplytronix"
                               style="width:100%;border:1px solid #e2e8f0;border-radius:5px;padding:7px 10px;font-size:13px">
                    </div>
                </div>
                <div style="margin-bottom:12px">
                    <label style="font-size:12px;color:#64748b;display:block;margin-bottom:4px">Message</label>
                    <textarea name="message" rows="5"
          style="width:100%;border:1px solid #e2e8f0;border-radius:5px;padding:7px 10px;font-size:13px;resize:vertical"
          placeholder="Hi, I noticed you were looking for...">
@if($interests->count())
Hi,

I noticed you've been looking at {{ $interests->first()->product_key }}{{ $interests->count() > 1 ? ' and '.$interests->count().' other parts' : '' }} on Simplytronix.

We have these in stock and can offer competitive pricing. Would you like a quote?

Best regards,
Simplytronix Team
sales@simplytronix.com
@endif
</textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-sm">
                    Send Outreach
                </button>
            </form>
        </div>

        {{-- Outreach History --}}
        @if($outreach->count())
        <div class="li-card">
            <div style="font-size:14px;font-weight:600;margin-bottom:14px">
                Outreach History ({{ $outreach->count() }})
            </div>
            @foreach($outreach as $log)
            @php
                $statusColors = [
                    'sent'          => ['#dbeafe','#1d4ed8'],
                    'opened'        => ['#dcfce7','#15803d'],
                    'clicked'       => ['#dcfce7','#15803d'],
                    'replied'       => ['#d1fae5','#047857'],
                    'bounced'       => ['#fee2e2','#dc2626'],
                    'pending'       => ['#f3f4f6','#6b7280'],
                    'unsubscribed'  => ['#fee2e2','#dc2626'],
                ];
                [$bg, $fg] = $statusColors[$log->status] ?? ['#f3f4f6','#6b7280'];
            @endphp
            <div style="padding:10px;background:#f8fafc;border-radius:6px;margin-bottom:8px">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px">
                    <div style="font-size:13px;font-weight:600">{{ $log->email }}</div>
                    <div style="display:flex;gap:6px;align-items:center">
                        <span class="outreach-status" style="background:{{ $bg }};color:{{ $fg }}">
                            {{ ucfirst($log->status) }}
                        </span>
                        <span style="font-size:11px;color:#94a3b8">
                            {{ \Carbon\Carbon::parse($log->created_at)->format('M d, H:i') }}
                        </span>
                    </div>
                </div>
                @if($log->message_subject)
                <div style="font-size:12px;color:#374151;font-weight:500">
                    {{ $log->message_subject }}
                </div>
                @endif
                <div style="font-size:11px;color:#94a3b8;margin-top:2px">
                    via {{ ucfirst($log->outreach_type) }} · sent by {{ $log->sent_by }}
                </div>
            </div>
            @endforeach
        </div>
        @endif

        {{-- Page Journey --}}
        @if($pageviews->count())
        <div class="li-card">
            <div style="font-size:14px;font-weight:600;margin-bottom:14px">
                Recent Page Journey (last {{ $pageviews->count() }} pages)
            </div>
            @foreach($pageviews as $pv)
            @php
    $typeColors = [
        'product'  => '#d35400',
        'quote'    => '#16a34a',
        'category' => '#7c3aed',
        'home'     => '#0369a1',
        'search'   => '#ca8a04',
    ];

    $color = $typeColors[$pv->page_type] ?? '#94a3b8';
@endphp
            <div style="display:flex;gap:10px;padding:7px 0;border-bottom:1px solid #f8fafc">
                <div class="timeline-dot" style="background:{{ $color }};margin-top:5px"></div>
                <div style="flex:1;min-width:0">
                    <div style="display:flex;justify-content:space-between;align-items:center">
                        <span style="background:{{ $color }}20;color:{{ $color }};padding:1px 7px;border-radius:99px;font-size:11px;font-weight:600;text-transform:capitalize">
                            {{ $pv->page_type }}
                        </span>
                        <span style="font-size:11px;color:#94a3b8">
                            {{ \Carbon\Carbon::parse($pv->created_at)->format('M d, H:i:s') }}
                        </span>
                    </div>
                    <div style="font-size:12px;color:#64748b;margin-top:2px;word-break:break-all">
                        {{ $pv->url }}
                    </div>
                    @if($pv->product_key)
                    <div style="font-size:12px;font-weight:700;color:#d35400;margin-top:1px">
                        {{ $pv->product_key }}
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @endif

    </div>
</div>

@endsection

@section('footer')
<script>
function toggleType(val) {
    document.getElementById('email-fields').style.display =
        val === 'email' ? 'block' : 'none';
}
</script>
@endsection