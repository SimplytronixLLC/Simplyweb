@extends('admin.includes.masterpage-admin')

@section('content')
<style>
    .v-card{background:#fff;border-radius:8px;border:1px solid #e5e7eb;padding:20px;margin-bottom:20px}
    .stat-box{background:#f8fafc;border-radius:6px;padding:14px;text-align:center}
    .stat-box .val{font-size:26px;font-weight:700;color:#1e293b}
    .stat-box .lbl{font-size:12px;color:#64748b;margin-top:2px}
    .badge-src{display:inline-block;padding:2px 8px;border-radius:99px;font-size:11px;font-weight:500}
    .funnel-step{text-align:center;padding:12px;background:#f8fafc;border-radius:6px;position:relative}
    .funnel-step .fn{font-size:22px;font-weight:700;color:#1e293b}
    .funnel-step .fl{font-size:12px;color:#64748b}
    .funnel-step .fp{font-size:11px;color:#16a34a;font-weight:600}
    .live-dot{width:8px;height:8px;border-radius:50%;background:#16a34a;display:inline-block;animation:blink 1.2s infinite}
    @keyframes blink{0%,100%{opacity:1}50%{opacity:.3}}
    .day-btn{padding:5px 12px;border-radius:5px;border:1px solid #e2e8f0;background:#f8fafc;font-size:12px;cursor:pointer;text-decoration:none;color:#374151}
    .day-btn.active{background:#1e293b;color:#fff;border-color:#1e293b}
</style>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div style="display:flex;align-items:center;gap:10px">
        <h4 style="margin:0">Visitor Analytics</h4>
        <span style="font-size:13px;color:#64748b">
            <span class="live-dot"></span>
            <strong id="live-count">{{ $liveVisitors }}</strong> live now
        </span>
    </div>
    <div style="display:flex;gap:6px">
        @foreach([1,7,30,90] as $d)
        <a href="{{ route('admin.visitors.index', ['days'=>$d]) }}"
           class="day-btn {{ $days==$d?'active':'' }}">
            {{ $d == 1 ? 'Today' : $d.'d' }}
        </a>
        @endforeach
    </div>
</div>

{{-- Overview Stats --}}
<div class="row mb-3">
    <div class="col-sm-2">
        <div class="stat-box">
            <div class="val">{{ number_format($totalVisitors) }}</div>
            <div class="lbl">Visitors</div>
        </div>
    </div>
    <div class="col-sm-2">
        <div class="stat-box">
            <div class="val">{{ number_format($totalPageviews) }}</div>
            <div class="lbl">Pageviews</div>
        </div>
    </div>
    <div class="col-sm-2">
        <div class="stat-box">
            <div class="val">{{ number_format($totalSearches) }}</div>
            <div class="lbl">Searches</div>
        </div>
    </div>
    <div class="col-sm-2">
        <div class="stat-box">
            <div class="val" style="color:#16a34a">{{ number_format($totalConversions) }}</div>
            <div class="lbl">RFQ Conversions</div>
        </div>
    </div>
    <div class="col-sm-2">
        <div class="stat-box">
            <div class="val" style="color:#0369a1">{{ $conversionRate }}%</div>
            <div class="lbl">Conv. Rate</div>
        </div>
    </div>
    <div class="col-sm-2">
        <div class="stat-box">
            <div class="val">
                {{ $totalVisitors > 0 ? round($totalPageviews/$totalVisitors,1) : 0 }}
            </div>
            <div class="lbl">Pages/Visitor</div>
        </div>
    </div>
</div>

{{-- Chart --}}
<div class="v-card">
    <div style="font-size:14px;font-weight:600;margin-bottom:14px">
        Visitors & Pageviews — last {{ $days }} days
    </div>
    <canvas id="visitorChart" height="80"></canvas>
</div>

<div class="row">

    {{-- Conversion Funnel --}}
    <div class="col-sm-6">
        <div class="v-card">
            <div style="font-size:14px;font-weight:600;margin-bottom:14px">Conversion Funnel</div>
            <div class="row">
                @php
                $steps = [
                    ['label'=>'Visited',       'val'=>$funnelVisitors,      'color'=>'#0369a1'],
                    ['label'=>'Searched',       'val'=>$funnelSearched,      'color'=>'#7c3aed'],
                    ['label'=>'Viewed Product', 'val'=>$funnelViewedProduct, 'color'=>'#ca8a04'],
                    ['label'=>'Submitted RFQ',  'val'=>$funnelConverted,     'color'=>'#16a34a'],
                ];
                @endphp
                @foreach($steps as $i => $step)
                <div class="col-sm-3">
                    <div class="funnel-step">
                        <div class="fn" style="color:{{ $step['color'] }}">
                            {{ number_format($step['val']) }}
                        </div>
                        <div class="fl">{{ $step['label'] }}</div>
                        @if($i > 0 && $steps[$i-1]['val'] > 0)
                        <div class="fp">
                            {{ round($step['val']/$steps[$i-1]['val']*100,1) }}%
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Traffic Sources --}}
    <div class="col-sm-6">
        <div class="v-card">
            <div style="font-size:14px;font-weight:600;margin-bottom:14px">Traffic Sources</div>
            @foreach($sources as $source => $count)
            @php $pct = $totalVisitors > 0 ? round($count/$totalVisitors*100) : 0; @endphp
            <div style="margin-bottom:10px">
                <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:3px">
                    <span>{{ $source }}</span>
                    <span style="font-weight:600">{{ number_format($count) }} ({{ $pct }}%)</span>
                </div>
                <div style="background:#e2e8f0;border-radius:99px;height:6px;overflow:hidden">
                    <div style="width:{{ $pct }}%;height:100%;background:#0369a1;border-radius:99px"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

</div>

<div class="row">

    {{-- Devices --}}
    <div class="col-sm-3">
        <div class="v-card">
            <div style="font-size:14px;font-weight:600;margin-bottom:14px">Devices</div>
            @foreach($devices as $device => $count)
            @php $pct = $totalVisitors > 0 ? round($count/$totalVisitors*100) : 0; @endphp
            <div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid #f1f5f9;font-size:13px">
                <span style="text-transform:capitalize">{{ $device ?? 'Unknown' }}</span>
                <span><strong>{{ $pct }}%</strong> <span style="color:#94a3b8">({{ number_format($count) }})</span></span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Browsers --}}
    <div class="col-sm-3">
        <div class="v-card">
            <div style="font-size:14px;font-weight:600;margin-bottom:14px">Browsers</div>
            @foreach($browsers as $browser => $count)
            @php $pct = $totalVisitors > 0 ? round($count/$totalVisitors*100) : 0; @endphp
            <div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid #f1f5f9;font-size:13px">
                <span>{{ $browser ?? 'Unknown' }}</span>
                <span><strong>{{ $pct }}%</strong> <span style="color:#94a3b8">({{ number_format($count) }})</span></span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Page Types --}}
    <div class="col-sm-3">
        <div class="v-card">
            <div style="font-size:14px;font-weight:600;margin-bottom:14px">Page Types</div>
            @foreach($pageTypes as $type => $count)
            @php $pct = $totalPageviews > 0 ? round($count/$totalPageviews*100) : 0; @endphp
            <div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid #f1f5f9;font-size:13px">
                <span style="text-transform:capitalize">{{ $type ?? 'other' }}</span>
                <span><strong>{{ $pct }}%</strong> <span style="color:#94a3b8">({{ number_format($count) }})</span></span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Top Searches --}}
    <div class="col-sm-3">
        <div class="v-card">
            <div style="font-size:14px;font-weight:600;margin-bottom:14px">Top Searches</div>
            @foreach($topSearches as $s)
            <div style="display:flex;justify-content:space-between;padding:5px 0;border-bottom:1px solid #f1f5f9;font-size:12px">
                <span style="font-weight:600;color:#1e293b">{{ $s->part_number }}</span>
                <span style="color:#64748b">{{ $s->searches }}x · {{ $s->unique_visitors }} visitors</span>
            </div>
            @endforeach
        </div>
    </div>

</div>

{{-- Top Products --}}
<div class="v-card">
    <div style="font-size:14px;font-weight:600;margin-bottom:14px">Top Products Viewed</div>
    <table style="width:100%;border-collapse:collapse;font-size:13px">
        <thead>
            <tr style="background:#f8fafc;border-bottom:1px solid #e5e7eb">
                <th style="padding:8px 12px;text-align:left">Part Number</th>
                <th style="padding:8px 12px;text-align:left">Manufacturer</th>
                <th style="padding:8px 12px;text-align:center">Views</th>
                <th style="padding:8px 12px;text-align:center">Unique Visitors</th>
                <th style="padding:8px 12px;text-align:center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($topProducts as $p)
            @php
                $mfSlug  = \Illuminate\Support\Str::slug($p->manufacturer ?? '');
                $partEnc = rawurlencode(str_replace(['/','#'],['__','--'],strtoupper($p->product_key ?? '')));
            @endphp
            <tr style="border-bottom:1px solid #f1f5f9">
                <td style="padding:8px 12px;font-weight:600">{{ $p->product_key }}</td>
                <td style="padding:8px 12px;color:#64748b">{{ $p->manufacturer }}</td>
                <td style="padding:8px 12px;text-align:center">{{ number_format($p->views) }}</td>
                <td style="padding:8px 12px;text-align:center">{{ number_format($p->unique_visitors) }}</td>
                <td style="padding:8px 12px;text-align:center">
                    <a href="{{ url('product/'.$mfSlug.'/'.$partEnc) }}" target="_blank"
                       style="font-size:12px;color:#0369a1;text-decoration:none">View</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- Recent Conversions --}}
@if($recentConversions->count())
<div class="v-card">
    <div style="font-size:14px;font-weight:600;margin-bottom:14px">Recent RFQ Conversions</div>
    <table style="width:100%;border-collapse:collapse;font-size:13px">
        <thead>
            <tr style="background:#f8fafc;border-bottom:1px solid #e5e7eb">
                <th style="padding:8px 12px;text-align:left">Name / Email</th>
                <th style="padding:8px 12px;text-align:left">Company</th>
                <th style="padding:8px 12px;text-align:left">Part</th>
                <th style="padding:8px 12px;text-align:left">Source</th>
                <th style="padding:8px 12px;text-align:center">Pages</th>
                <th style="padding:8px 12px;text-align:left">First Seen</th>
                <th style="padding:8px 12px;text-align:center">Detail</th>
            </tr>
        </thead>
        <tbody>
            @foreach($recentConversions as $c)
            <tr style="border-bottom:1px solid #f1f5f9">
                <td style="padding:8px 12px">
                    <div style="font-weight:600">{{ $c->name ?? '—' }}</div>
                    <div style="font-size:11px;color:#64748b">{{ $c->email ?? '—' }}</div>
                </td>
                <td style="padding:8px 12px;color:#64748b">{{ $c->company ?? '—' }}</td>
                <td style="padding:8px 12px;font-weight:600;color:#d35400">{{ $c->part_number ?? '—' }}</td>
                <td style="padding:8px 12px">
                    @php
                        $src = $c->first_utm_source ?? (
                            $c->first_referrer
                                ? (str_contains($c->first_referrer,'google') ? 'Google'
                                  : (str_contains($c->first_referrer,'bing') ? 'Bing' : 'Referral'))
                                : 'Direct'
                        );
                    @endphp
                    <span style="background:#e0f2fe;color:#0369a1;padding:2px 7px;border-radius:99px;font-size:11px">
                        {{ $src }}
                    </span>
                </td>
                <td style="padding:8px 12px;text-align:center">{{ $c->total_pageviews }}</td>
                <td style="padding:8px 12px;font-size:12px;color:#64748b">
                    {{ \Carbon\Carbon::parse($c->first_seen_at)->diffForHumans() }}
                </td>
                <td style="padding:8px 12px;text-align:center">
                    <a href="{{ route('admin.visitors.detail', $c->visitor_id) }}"
                       style="font-size:12px;color:#0369a1;text-decoration:none">View Journey</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

@endsection

@section('footer')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
const ctx = document.getElementById('visitorChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: @json($chartDates),
        datasets: [
            {
                label: 'Visitors',
                data: @json($chartVisitors),
                borderColor: '#0369a1',
                backgroundColor: 'rgba(3,105,161,0.08)',
                tension: 0.3,
                fill: true,
                pointRadius: 3,
            },
            {
                label: 'Pageviews',
                data: @json($chartPageviews),
                borderColor: '#16a34a',
                backgroundColor: 'rgba(22,163,74,0.06)',
                tension: 0.3,
                fill: true,
                pointRadius: 3,
            }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'top' } },
        scales: {
            y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
            x: { grid: { display: false } }
        }
    }
});

// Refresh live count every 30s
setInterval(() => {
    fetch('{{ route('admin.visitors.index') }}?live=1')
        .then(() => {})
        .catch(() => {});
}, 30000);
</script>
@endsection