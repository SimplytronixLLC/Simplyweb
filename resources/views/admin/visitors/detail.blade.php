@extends('admin.includes.masterpage-admin')

@section('content')
<style>
    .v-card{background:#fff;border-radius:8px;border:1px solid #e5e7eb;padding:20px;margin-bottom:20px}
    .timeline-dot{width:10px;height:10px;border-radius:50%;display:inline-block;margin-right:8px;flex-shrink:0;margin-top:4px}
</style>

<div class="d-flex align-items-center gap-3 mb-3">
    <a href="{{ route('admin.visitors.index') }}"
       style="font-size:13px;color:#64748b;text-decoration:none">← Back</a>
    <h4 style="margin:0">Visitor Journey</h4>
    @if($session->converted_to_quote)
    <span style="background:#dcfce7;color:#15803d;padding:3px 10px;border-radius:99px;font-size:12px;font-weight:600">
        ✓ Converted to RFQ
    </span>
    @endif
</div>

<div class="row">
    <div class="col-sm-4">

        {{-- Visitor Info --}}
        <div class="v-card">
            <div style="font-size:14px;font-weight:600;margin-bottom:14px">Visitor Info</div>
            <table style="font-size:13px;width:100%">
                <tr><td style="color:#64748b;padding:5px 0;width:120px">Visitor ID</td>
                    <td style="font-size:11px;word-break:break-all">{{ $visitorId }}</td></tr>
                <tr><td style="color:#64748b;padding:5px 0">First seen</td>
                    <td>{{ \Carbon\Carbon::parse($session->first_seen_at)->format('M d Y, H:i') }}</td></tr>
                <tr><td style="color:#64748b;padding:5px 0">Last seen</td>
                    <td>{{ \Carbon\Carbon::parse($session->last_seen_at)->format('M d Y, H:i') }}</td></tr>
                <tr><td style="color:#64748b;padding:5px 0">Device</td>
                    <td style="text-transform:capitalize">{{ $session->device ?? '—' }}</td></tr>
                <tr><td style="color:#64748b;padding:5px 0">Browser</td>
                    <td>{{ $session->browser ?? '—' }}</td></tr>
                <tr><td style="color:#64748b;padding:5px 0">Total pages</td>
                    <td>{{ $session->total_pageviews }}</td></tr>
                <tr><td style="color:#64748b;padding:5px 0">Source</td>
                    <td>{{ $session->first_utm_source ?? ($session->first_referrer ? 'Referral' : 'Direct') }}</td></tr>
                @if($session->first_referrer)
                <tr><td style="color:#64748b;padding:5px 0">Referrer</td>
                    <td style="font-size:11px;word-break:break-all">{{ $session->first_referrer }}</td></tr>
                @endif
                <tr><td style="color:#64748b;padding:5px 0">Landing page</td>
                    <td style="font-size:11px;word-break:break-all">{{ $session->first_landing_page }}</td></tr>
            </table>
        </div>

        {{-- Lead / Quote info --}}
        @if($lead)
        <div class="v-card">
            <div style="font-size:14px;font-weight:600;margin-bottom:14px">Lead Info</div>
            <table style="font-size:13px;width:100%">
                <tr><td style="color:#64748b;padding:5px 0;width:80px">Email</td>
                    <td>{{ $lead->email }}</td></tr>
            </table>

            @foreach($quotes as $q)
            <div style="margin-top:12px;padding:10px;background:#f8fafc;border-radius:6px;font-size:12px">
                <div style="font-weight:600;color:#d35400">{{ $q->part_number }}</div>
                <div style="color:#64748b">{{ $q->name }} · {{ $q->company }}</div>
                <div style="color:#64748b">Qty: {{ $q->quantity }}</div>
                <div style="color:#94a3b8;font-size:11px">{{ \Carbon\Carbon::parse($q->created_at)->format('M d Y H:i') }}</div>
            </div>
            @endforeach
        </div>
        @endif

        {{-- Searches --}}
        @if($searches->count())
        <div class="v-card">
            <div style="font-size:14px;font-weight:600;margin-bottom:14px">
                Searches ({{ $searches->count() }})
            </div>
            @foreach($searches as $s)
            <div style="display:flex;justify-content:space-between;padding:5px 0;border-bottom:1px solid #f1f5f9;font-size:13px">
                <span style="font-weight:600">{{ $s->part_number }}</span>
                <span style="color:#94a3b8;font-size:11px">
                    {{ \Carbon\Carbon::parse($s->created_at)->format('M d, H:i') }}
                </span>
            </div>
            @endforeach
        </div>
        @endif

    </div>

    <div class="col-sm-8">
        {{-- Page journey timeline --}}
        <div class="v-card">
            <div style="font-size:14px;font-weight:600;margin-bottom:14px">
                Page Journey ({{ $pageviews->count() }} pages)
            </div>

            @foreach($pageviews as $pv)
            @php
                $color = match($pv->page_type) {
                    'product'   => '#d35400',
                    'quote'     => '#16a34a',
                    'category'  => '#7c3aed',
                    'home'      => '#0369a1',
                    default     => '#94a3b8',
                };
            @endphp
            <div style="display:flex;align-items:flex-start;padding:8px 0;border-bottom:1px solid #f8fafc;font-size:13px">
                <span class="timeline-dot" style="background:{{ $color }}"></span>
                <div style="flex:1;min-width:0">
                    <div style="display:flex;justify-content:space-between;align-items:center">
                        <span style="background:{{ $color }}20;color:{{ $color }};padding:1px 7px;border-radius:99px;font-size:11px;font-weight:600;text-transform:capitalize">
                            {{ $pv->page_type }}
                        </span>
                        <span style="font-size:11px;color:#94a3b8">
                            {{ \Carbon\Carbon::parse($pv->created_at)->format('M d, H:i:s') }}
                        </span>
                    </div>
                    <div style="margin-top:3px;font-size:12px;color:#64748b;word-break:break-all">
                        {{ $pv->url }}
                    </div>
                    @if($pv->product_key)
                    <div style="margin-top:2px;font-size:12px;font-weight:600;color:#d35400">
                        {{ $pv->product_key }}
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection