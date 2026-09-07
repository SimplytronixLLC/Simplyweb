@extends('admin.includes.masterpage-admin')

@section('content')
<style>
    .li-card{background:#fff;border-radius:8px;border:1px solid #e5e7eb;padding:20px;margin-bottom:20px}
    .stat-box{background:#f8fafc;border-radius:6px;padding:14px;text-align:center;border:1px solid #f1f5f9}
    .stat-box .val{font-size:24px;font-weight:700;color:#1e293b}
    .stat-box .lbl{font-size:12px;color:#64748b;margin-top:2px}
    .score-badge{display:inline-block;padding:2px 8px;border-radius:99px;font-size:11px;font-weight:700}
    .score-hot{background:#fee2e2;color:#dc2626}
    .score-warm{background:#fef3c7;color:#d97706}
    .score-cold{background:#e0f2fe;color:#0369a1}
    .tag{display:inline-block;padding:2px 7px;border-radius:99px;font-size:11px;font-weight:500}
    .filter-bar{background:#f8fafc;border:1px solid #e5e7eb;border-radius:8px;padding:14px 18px;margin-bottom:18px;display:flex;gap:12px;align-items:center;flex-wrap:wrap}
    .filter-bar select, .filter-bar input{border:1px solid #e2e8f0;border-radius:5px;padding:5px 10px;font-size:13px;background:#fff}
    table.leads-table{width:100%;border-collapse:collapse;font-size:13px}
    table.leads-table thead tr{background:#f8fafc;border-bottom:2px solid #e5e7eb}
    table.leads-table th{padding:9px 12px;text-align:left;font-weight:600;color:#374151;white-space:nowrap}
    table.leads-table td{padding:8px 12px;border-bottom:1px solid #f1f5f9;vertical-align:middle}
    table.leads-table tbody tr:hover{background:#fafafa}
    .btn-sm-action{padding:4px 10px;border-radius:5px;font-size:12px;border:1px solid;cursor:pointer;text-decoration:none;display:inline-block}
    .org-tag{background:#ede9fe;color:#7c3aed;padding:2px 8px;border-radius:4px;font-size:11px;font-weight:500}
</style>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 style="margin:0">Lead Intelligence</h4>
        <div style="font-size:12px;color:#64748b;margin-top:2px">
            Visitors ranked by engagement — use to identify and reach out to potential customers
        </div>
    </div>
    <div style="display:flex;gap:8px">
        <a href="{{ route('admin.leads.export', ['min_score' => $minScore]) }}"
           class="btn btn-sm btn-success">
            ↓ Export CSV
        </a>
    </div>
</div>

{{-- Summary Stats --}}
<div class="row mb-3">
    <div class="col-sm-3">
        <div class="stat-box">
            <div class="val">{{ number_format($totalProspects) }}</div>
            <div class="lbl">Total Visitors Tracked</div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="stat-box">
            <div class="val" style="color:#dc2626">{{ number_format($highEngagement) }}</div>
            <div class="lbl">High Engagement (Score ≥50)</div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="stat-box">
            <div class="val" style="color:#16a34a">
                {{ DB::table('visitor_contacts')->whereNotNull('email')->count() }}
            </div>
            <div class="lbl">Known Emails</div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="stat-box">
            <div class="val" style="color:#7c3aed">
                {{ DB::table('visitor_outreach_logs')->count() }}
            </div>
            <div class="lbl">Outreach Sent</div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-9">

        {{-- Filters --}}
        <form method="GET" action="{{ route('admin.leads.index') }}">
            <div class="filter-bar">
                <div>
                    <label style="font-size:12px;color:#64748b;display:block;margin-bottom:2px">Min Score</label>
                    <input type="number" name="min_score" value="{{ $minScore }}" style="width:80px">
                </div>
                <div>
                    <label style="font-size:12px;color:#64748b;display:block;margin-bottom:2px">Country</label>
                    <input type="text" name="country" value="{{ $country }}" placeholder="US, IN, GB..." style="width:80px">
                </div>
                <div>
                    <label style="font-size:12px;color:#64748b;display:block;margin-bottom:2px">Contact Status</label>
                    <select name="contact_status">
                        <option value="">All</option>
                        <option value="uncontacted" {{ $contact_status=='uncontacted'?'selected':'' }}>Uncontacted</option>
                        <option value="contacted" {{ $contact_status=='contacted'?'selected':'' }}>Has Email</option>
                    </select>
                </div>
                <div>
                    <label style="font-size:12px;color:#64748b;display:block;margin-bottom:2px">Sort By</label>
                    <select name="sort">
                        <option value="engagement_score" {{ $sortBy=='engagement_score'?'selected':'' }}>Engagement Score</option>
                        <option value="last_visit_at" {{ $sortBy=='last_visit_at'?'selected':'' }}>Last Visit</option>
                        <option value="product_views" {{ $sortBy=='product_views'?'selected':'' }}>Product Views</option>
                        <option value="total_visits" {{ $sortBy=='total_visits'?'selected':'' }}>Total Visits</option>
                    </select>
                </div>
                <div style="margin-top:16px">
                    <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                    <a href="{{ route('admin.leads.index') }}" class="btn btn-sm btn-light">Reset</a>
                </div>
            </div>
        </form>

        {{-- Leads Table --}}
        <div class="li-card" style="padding:0;overflow:hidden">
            <form id="bulk-form" method="POST" action="{{ route('admin.leads.bulk') }}">
                @csrf
                <div id="bulk-bar" style="display:none;padding:12px 16px;background:#fef9c3;border-bottom:1px solid #fde68a;align-items:center;gap:10px">
                    <span id="bulk-count" style="font-size:13px;font-weight:600"></span>
                    <input type="text" name="subject" placeholder="Email Subject" style="border:1px solid #e2e8f0;border-radius:5px;padding:5px 10px;font-size:13px;width:220px">
                    <textarea name="message" placeholder="Email message..." rows="1" style="border:1px solid #e2e8f0;border-radius:5px;padding:5px 10px;font-size:13px;width:300px;resize:none"></textarea>
                    <button type="submit" class="btn btn-sm btn-warning">Send Bulk Email</button>
                    <button type="button" onclick="clearSelection()" class="btn btn-sm btn-light">Cancel</button>
                </div>

                <table class="leads-table">
                    <thead>
                        <tr>
                            <th style="width:36px"><input type="checkbox" id="select-all"></th>
                            <th>Visitor</th>
                            <th>Organization</th>
                            <th>Location</th>
                            <th>Score</th>
                            <th>Activity</th>
                            <th>Contact</th>
                            <th>Last Seen</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leads as $lead)
                        @php
                            $score = $lead->engagement_score ?? 0;
                            $scoreClass = $score >= 50 ? 'score-hot' : ($score >= 20 ? 'score-warm' : 'score-cold');
                            $scoreLabel = $score >= 50 ? '🔥 Hot' : ($score >= 20 ? '⚡ Warm' : '❄️ Cold');
                        @endphp
                        <tr>
                            <td>
                                <input type="checkbox" name="visitor_ids[]"
                                       value="{{ $lead->visitor_id }}"
                                       class="lead-check">
                            </td>
                            <td>
                                <div style="font-size:12px;color:#94a3b8;font-family:monospace">
                                    {{ substr($lead->visitor_id, 0, 8) }}...
                                </div>
                                <div style="font-size:11px;margin-top:2px">
                                    <span style="color:#64748b">{{ $lead->device_type ?? '?' }}</span>
                                    · <span style="color:#64748b">{{ $lead->browser_name ?? '?' }}</span>
                                </div>
                                @if($lead->ip_address)
                                <div style="font-size:11px;color:#94a3b8;font-family:monospace">
                                    {{ $lead->ip_address }}
                                </div>
                                @endif
                            </td>
                            <td>
                                @if($lead->ip_org)
                                <span class="org-tag">{{ Str::limit($lead->ip_org, 30) }}</span>
                                @elseif($lead->company)
                                <span class="org-tag">{{ $lead->company }}</span>
                                @else
                                <span style="color:#cbd5e1;font-size:12px">Unknown</span>
                                @endif
                                @if(isset($lead->ip_isp) && $lead->ip_isp && $lead->ip_isp !== $lead->ip_org)
                                <div style="font-size:11px;color:#94a3b8;margin-top:2px">
                                    ISP: {{ Str::limit($lead->ip_isp ?? '', 25) }}
                                </div>
                                @endif
                            </td>
                            <td>
                                <div style="font-size:13px">{{ $lead->ip_city ?? '—' }}</div>
                                <div style="font-size:11px;color:#64748b">{{ $lead->ip_country ?? '' }}</div>
                            </td>
                            <td>
                                <span class="score-badge {{ $scoreClass }}">
                                    {{ $scoreLabel }} {{ $score }}
                                </span>
                            </td>
                            <td>
                                <div style="font-size:12px">
                                    <span title="Visits">👁 {{ $lead->total_visits ?? 0 }}</span>
                                    &nbsp;
                                    <span title="Product Views">📦 {{ $lead->product_views ?? 0 }}</span>
                                    &nbsp;
                                    <span title="Searches">🔍 {{ $lead->total_searches ?? 0 }}</span>
                                </div>
                                @if($lead->most_viewed_manufacturer)
                                <div style="font-size:11px;color:#7c3aed;margin-top:2px">
                                    Top: {{ $lead->most_viewed_manufacturer }}
                                </div>
                                @endif
                            </td>
                            <td>
                                @if($lead->email)
                                <div style="font-size:12px;font-weight:600;color:#0369a1">
                                    {{ $lead->email }}
                                </div>
                                @if($lead->name)
                                <div style="font-size:11px;color:#64748b">{{ $lead->name }}</div>
                                @endif
                                <span class="tag" style="background:#dcfce7;color:#15803d;margin-top:2px">
                                    ✓ Known
                                </span>
                                @else
                                <span style="color:#cbd5e1;font-size:12px">Anonymous</span>
                                @endif
                            </td>
                            <td style="font-size:12px;color:#64748b;white-space:nowrap">
                                @if($lead->last_visit_at)
                                {{ \Carbon\Carbon::parse($lead->last_visit_at)->diffForHumans() }}
                                @else
                                —
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.leads.detail', $lead->visitor_id) }}"
                                   class="btn-sm-action"
                                   style="background:#f0f9ff;color:#0369a1;border-color:#bae6fd">
                                    Profile →
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" style="text-align:center;padding:40px;color:#94a3b8">
                                No leads found with current filters
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </form>

            <div style="padding:12px 16px;border-top:1px solid #f1f5f9">
                {{ $leads->withQueryString()->links() }}
            </div>
        </div>

    </div>

    {{-- Sidebar --}}
    <div class="col-sm-3">

        {{-- Top Countries --}}
        <div class="li-card">
            <div style="font-size:14px;font-weight:600;margin-bottom:14px">Visitors by Country</div>
            @foreach($byCountry as $country => $count)
            @php $pct = $totalProspects > 0 ? round($count/$totalProspects*100) : 0; @endphp
            <div style="margin-bottom:8px">
                <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:3px">
                    <a href="{{ route('admin.leads.index', ['country'=>$country, 'min_score'=>0]) }}"
                       style="color:#0369a1;text-decoration:none">{{ $country }}</a>
                    <span style="color:#64748b">{{ number_format($count) }}</span>
                </div>
                <div style="background:#e2e8f0;border-radius:99px;height:5px;overflow:hidden">
                    <div style="width:{{ $pct }}%;height:100%;background:#0369a1;border-radius:99px"></div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Score Guide --}}
        <div class="li-card">
            <div style="font-size:14px;font-weight:600;margin-bottom:12px">Engagement Score Guide</div>
            <div style="font-size:12px;color:#64748b;line-height:1.8">
                <div>+1 per pageview</div>
                <div>+5 per part search</div>
                <div>+3 per product view</div>
                <div>+20 per RFQ submitted</div>
                <div>+10 if visited within 30 days</div>
                <hr style="border-color:#f1f5f9;margin:8px 0">
                <div><span class="score-badge score-hot">🔥 Hot</span> Score ≥ 50</div>
                <div style="margin-top:4px"><span class="score-badge score-warm">⚡ Warm</span> Score ≥ 20</div>
                <div style="margin-top:4px"><span class="score-badge score-cold">❄️ Cold</span> Score < 20</div>
            </div>
        </div>

    </div>
</div>

@endsection

@section('footer')
<script>
document.getElementById('select-all').addEventListener('change', function() {
    document.querySelectorAll('.lead-check').forEach(cb => cb.checked = this.checked);
    updateBulkBar();
});

document.querySelectorAll('.lead-check').forEach(cb => {
    cb.addEventListener('change', updateBulkBar);
});

function updateBulkBar() {
    const checked = document.querySelectorAll('.lead-check:checked');
    const bar = document.getElementById('bulk-bar');
    if (checked.length > 0) {
        bar.style.display = 'flex';
        document.getElementById('bulk-count').textContent = checked.length + ' selected';
    } else {
        bar.style.display = 'none';
    }
}

function clearSelection() {
    document.querySelectorAll('.lead-check').forEach(cb => cb.checked = false);
    document.getElementById('select-all').checked = false;
    document.getElementById('bulk-bar').style.display = 'none';
}
</script>
@endsection