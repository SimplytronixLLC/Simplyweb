@extends('admin.includes.masterpage-admin')

@section('content')
<style>
    .audit-card{background:#fff;border-radius:8px;border:1px solid #e5e7eb;padding:16px;margin-bottom:20px}
    .stat-box{background:#f8fafc;border-radius:6px;padding:12px;text-align:center}
    .stat-box .val{font-size:22px;font-weight:700;color:#1e293b}
    .stat-box .lbl{font-size:11px;color:#64748b;margin-top:2px}
    .badge-ok{background:#dcfce7;color:#15803d;padding:2px 8px;border-radius:99px;font-size:11px;font-weight:600}
    .badge-fail{background:#fee2e2;color:#dc2626;padding:2px 8px;border-radius:99px;font-size:11px;font-weight:600}
    .badge-warn{background:#fef9c3;color:#854d0e;padding:2px 8px;border-radius:99px;font-size:11px;font-weight:600}
    .score-bar{height:6px;border-radius:99px;background:#e2e8f0;overflow:hidden}
    .score-fill{height:100%;border-radius:99px}
    .filter-btn{padding:5px 12px;border-radius:5px;border:1px solid #e2e8f0;background:#f8fafc;font-size:12px;cursor:pointer;text-decoration:none;color:#374151;display:inline-block}
    .filter-btn.active{background:#1e293b;color:#fff;border-color:#1e293b}
</style>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 style="margin:0">SEO Audit</h4>
    <div style="display:flex;gap:8px;align-items:center">
        <span style="font-size:12px;color:#94a3b8">Last run: {{ $lastRun ? \Carbon\Carbon::parse($lastRun)->diffForHumans() : 'Never' }}</span>
        <button onclick="runAudit()" id="btn-audit"
                style="padding:7px 16px;background:#16a34a;color:#fff;border:none;border-radius:6px;font-size:13px;cursor:pointer">
            ▶ Run Audit Now
        </button>
        <a href="{{ route('admin.seo.audit.export', request()->query()) }}"
           style="padding:7px 16px;background:#0369a1;color:#fff;border:none;border-radius:6px;font-size:13px;text-decoration:none">
            ↓ Export CSV
        </a>
    </div>
</div>

{{-- Summary Stats --}}
@if($stats && $stats->total > 0)
<div class="audit-card">
    <div class="row mb-3">
        <div class="col-sm-3">
            <div class="stat-box">
                <div class="val">{{ number_format($stats->total) }}</div>
                <div class="lbl">Total Products</div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="stat-box">
                <div class="val" style="color:#16a34a">{{ $stats->avg_score }}</div>
                <div class="lbl">Avg SEO Score</div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="stat-box">
                <div class="val" style="color:#16a34a">{{ number_format($stats->good) }}</div>
                <div class="lbl">Good (≥80)</div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="stat-box">
                <div class="val" style="color:#dc2626">{{ number_format($stats->poor) }}</div>
                <div class="lbl">Poor (&lt;50)</div>
            </div>
        </div>
    </div>

    <div class="row">
        @php
        $checks = [
            ['label'=>'Has Image',     'val'=>$stats->has_image,     'total'=>$stats->total, 'filter'=>'no_image'],
            ['label'=>'Has Datasheet', 'val'=>$stats->has_datasheet, 'total'=>$stats->total, 'filter'=>'no_datasheet'],
            ['label'=>'Has Specs',     'val'=>$stats->has_specs,     'total'=>$stats->total, 'filter'=>'no_specs'],
            ['label'=>'Has Desc',      'val'=>$stats->has_description,'total'=>$stats->total,'filter'=>'no_desc'],
            ['label'=>'Meta Title',    'val'=>$stats->has_meta_title, 'total'=>$stats->total, 'filter'=>'no_meta'],
            ['label'=>'Schema OK',     'val'=>$stats->schema_ok,     'total'=>$stats->total, 'filter'=>'schema_fail'],
            ['label'=>'Title Length',  'val'=>$stats->title_ok,      'total'=>$stats->total, 'filter'=>'bad_title'],
            ['label'=>'Desc Length',   'val'=>$stats->desc_ok,       'total'=>$stats->total, 'filter'=>'bad_desc'],
        ];
        @endphp

        @foreach($checks as $c)
        @php $pct = $stats->total > 0 ? round($c['val'] / $c['total'] * 100) : 0; @endphp
        <div class="col-sm-3 mb-3">
            <a href="{{ route('admin.seo.audit', array_merge(request()->query(), ['filter'=>$c['filter']])) }}"
               style="text-decoration:none;color:inherit;display:block">
                <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:4px">
                    <span style="color:#64748b">{{ $c['label'] }}</span>
                    <span style="font-weight:600;color:{{ $pct >= 80 ? '#16a34a' : ($pct >= 50 ? '#ca8a04' : '#dc2626') }}">
                        {{ $pct }}%
                    </span>
                </div>
                <div class="score-bar">
                    <div class="score-fill" style="width:{{ $pct }}%;background:{{ $pct >= 80 ? '#16a34a' : ($pct >= 50 ? '#ca8a04' : '#dc2626') }}"></div>
                </div>
                <div style="font-size:11px;color:#94a3b8;margin-top:3px">
                    {{ number_format($c['total'] - $c['val']) }} missing
                </div>
            </a>
        </div>
        @endforeach
    </div>
</div>
@else
<div class="audit-card" style="text-align:center;padding:40px;color:#94a3b8">
    No audit data yet. Click "Run Audit Now" to start.
</div>
@endif

{{-- Filters --}}
<div class="audit-card" style="padding:12px 16px">
    <form method="GET" style="display:flex;gap:8px;flex-wrap:wrap;align-items:center">
        <input name="search" value="{{ request('search') }}" placeholder="Search part number..."
               style="padding:5px 10px;border:1px solid #e2e8f0;border-radius:5px;font-size:13px;width:180px">

        <select name="manufacturer" style="padding:5px 10px;border:1px solid #e2e8f0;border-radius:5px;font-size:13px">
            <option value="">All Manufacturers</option>
            @foreach($manufacturers as $m)
            <option value="{{ $m }}" {{ request('manufacturer') == $m ? 'selected' : '' }}>{{ $m }}</option>
            @endforeach
        </select>

        <select name="filter" style="padding:5px 10px;border:1px solid #e2e8f0;border-radius:5px;font-size:13px">
            <option value="">All Products</option>
            <option value="no_image"     {{ request('filter')=='no_image'     ? 'selected':'' }}>Missing Image</option>
            <option value="no_desc"      {{ request('filter')=='no_desc'      ? 'selected':'' }}>Missing Description</option>
            <option value="no_specs"     {{ request('filter')=='no_specs'     ? 'selected':'' }}>Missing Specs</option>
            <option value="no_datasheet" {{ request('filter')=='no_datasheet' ? 'selected':'' }}>Missing Datasheet</option>
            <option value="no_meta"      {{ request('filter')=='no_meta'      ? 'selected':'' }}>Missing Meta Title</option>
            <option value="bad_title"    {{ request('filter')=='bad_title'    ? 'selected':'' }}>Bad Title Length</option>
            <option value="bad_desc"     {{ request('filter')=='bad_desc'     ? 'selected':'' }}>Bad Desc Length</option>
            <option value="schema_fail"  {{ request('filter')=='schema_fail'  ? 'selected':'' }}>Schema Failing</option>
            <option value="low_score"    {{ request('filter')=='low_score'    ? 'selected':'' }}>Low Score (&lt;50)</option>
        </select>

        <select name="sort" style="padding:5px 10px;border:1px solid #e2e8f0;border-radius:5px;font-size:13px">
            <option value="score"        {{ request('sort')=='score'       ?'selected':'' }}>Sort: Score</option>
            <option value="product_key"  {{ request('sort')=='product_key' ?'selected':'' }}>Sort: Part Number</option>
            <option value="manufacturer" {{ request('sort')=='manufacturer'?'selected':'' }}>Sort: Manufacturer</option>
        </select>

        <select name="dir" style="padding:5px 10px;border:1px solid #e2e8f0;border-radius:5px;font-size:13px">
            <option value="asc"  {{ request('dir')=='asc'  ?'selected':'' }}>Asc</option>
            <option value="desc" {{ request('dir')=='desc' ?'selected':'' }}>Desc</option>
        </select>

        <button type="submit"
                style="padding:5px 14px;background:#1e293b;color:#fff;border:none;border-radius:5px;font-size:13px;cursor:pointer">
            Filter
        </button>
        <a href="{{ route('admin.seo.audit') }}"
           style="padding:5px 14px;background:#f1f5f9;color:#374151;border:1px solid #e2e8f0;border-radius:5px;font-size:13px;text-decoration:none">
            Reset
        </a>
    </form>
</div>

{{-- Table --}}
<div class="audit-card" style="padding:0;overflow:hidden">
    <table style="width:100%;border-collapse:collapse;font-size:13px">
        <thead>
            <tr style="background:#f8fafc;border-bottom:1px solid #e5e7eb">
                <th style="padding:10px 14px;text-align:left;font-weight:600;color:#374151">Part Number</th>
                <th style="padding:10px 14px;text-align:left;font-weight:600;color:#374151">Manufacturer</th>
                <th style="padding:10px 8px;text-align:center;font-weight:600;color:#374151">Score</th>
                <th style="padding:10px 8px;text-align:center;font-weight:600;color:#374151">Image</th>
                <th style="padding:10px 8px;text-align:center;font-weight:600;color:#374151">Desc</th>
                <th style="padding:10px 8px;text-align:center;font-weight:600;color:#374151">Specs</th>
                <th style="padding:10px 8px;text-align:center;font-weight:600;color:#374151">Datasheet</th>
                <th style="padding:10px 8px;text-align:center;font-weight:600;color:#374151">Meta</th>
                <th style="padding:10px 8px;text-align:center;font-weight:600;color:#374151">Schema</th>
                <th style="padding:10px 8px;text-align:center;font-weight:600;color:#374151">Title</th>
                <th style="padding:10px 8px;text-align:center;font-weight:600;color:#374151">Desc Len</th>
                <th style="padding:10px 14px;text-align:center;font-weight:600;color:#374151">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $p)
            @php
                $scoreColor = $p->score >= 80 ? '#16a34a' : ($p->score >= 50 ? '#ca8a04' : '#dc2626');
                $mfSlug = \Illuminate\Support\Str::slug($p->manufacturer ?? '');
                $partEnc = rawurlencode(str_replace(['/','#'],['__','--'], strtoupper($p->product_key)));
            @endphp
            <tr style="border-bottom:1px solid #f1f5f9;{{ $loop->even ? 'background:#fafafa' : '' }}">
                <td style="padding:10px 14px;font-weight:600;color:#1e293b">{{ $p->product_key }}</td>
                <td style="padding:10px 14px;color:#64748b">{{ $p->manufacturer }}</td>
                <td style="padding:10px 8px;text-align:center">
                    <span style="font-weight:700;color:{{ $scoreColor }}">{{ $p->score }}</span>
                </td>
                <td style="padding:10px 8px;text-align:center">
                    <span class="{{ $p->has_image ? 'badge-ok' : 'badge-fail' }}">{{ $p->has_image ? '✓' : '✗' }}</span>
                </td>
                <td style="padding:10px 8px;text-align:center">
                    <span class="{{ $p->has_description ? 'badge-ok' : 'badge-fail' }}">{{ $p->has_description ? '✓' : '✗' }}</span>
                </td>
                <td style="padding:10px 8px;text-align:center">
                    <span class="{{ $p->has_specs ? 'badge-ok' : 'badge-warn' }}">{{ $p->has_specs ? '✓' : '✗' }}</span>
                </td>
                <td style="padding:10px 8px;text-align:center">
                    <span class="{{ $p->has_datasheet ? 'badge-ok' : 'badge-warn' }}">{{ $p->has_datasheet ? '✓' : '✗' }}</span>
                </td>
                <td style="padding:10px 8px;text-align:center">
                    <span class="{{ $p->has_meta_title ? 'badge-ok' : 'badge-warn' }}">{{ $p->has_meta_title ? '✓' : '✗' }}</span>
                </td>
                <td style="padding:10px 8px;text-align:center">
                    <span class="{{ $p->schema_ok ? 'badge-ok' : 'badge-fail' }}">{{ $p->schema_ok ? '✓' : '✗' }}</span>
                </td>
                <td style="padding:10px 8px;text-align:center">
                    <span class="{{ $p->title_ok ? 'badge-ok' : 'badge-warn' }}">{{ $p->title_ok ? '✓' : '✗' }}</span>
                </td>
                <td style="padding:10px 8px;text-align:center">
                    <span class="{{ $p->desc_ok ? 'badge-ok' : 'badge-warn' }}">{{ $p->desc_ok ? '✓' : '✗' }}</span>
                </td>
                <td style="padding:10px 14px;text-align:center">
                    <a href="{{ url('product/'.$mfSlug.'/'.$partEnc) }}" target="_blank"
                       style="font-size:12px;color:#0369a1;text-decoration:none;margin-right:6px">View</a>
                    <a href="{{ url('admin/specs-editor?mpn='.$p->product_key) }}"
                       style="font-size:12px;color:#d35400;text-decoration:none">Edit</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="12" style="padding:30px;text-align:center;color:#94a3b8">
                    No products found. Try adjusting your filters or run the audit first.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

   <div style="display:flex;justify-content:center;padding:14px 16px;border-top:1px solid #f1f5f9">
    {{ $products->onEachSide(1)->links('pagination::simple-bootstrap-4') }}
    </div>
    <style>.pagination { display:flex;gap:4px;list-style:none;padding:0;margin:0;flex-wrap:wrap;align-items:center }
.pagination li a, .pagination li span { display:inline-flex;align-items:center;justify-content:center;min-width:32px;height:32px;padding:0 10px;border-radius:6px;border:1px solid #e2e8f0;background:#f8fafc;color:#374151;font-size:13px;text-decoration:none }
.pagination li.active span { background:#1e293b;color:#fff;border-color:#1e293b }
.pagination li.disabled span { color:#cbd5e1;cursor:default }
.pagination li a:hover { background:#e2e8f0 }</style>
</div>

@endsection

@section('footer')
<script>
const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

function runAudit() {
    const btn = document.getElementById('btn-audit');
    btn.textContent = 'Running...';
    btn.disabled = true;

    fetch('{{ route('admin.seo.audit.run') }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json' }
    })
    .then(r => r.json())
    .then(d => {
        alert(d.message);
        if (d.success) {
            btn.textContent = 'Running in background...';
            setTimeout(() => location.reload(), 3000);
        } else {
            btn.textContent = '▶ Run Audit Now';
            btn.disabled = false;
        }
    });
}
</script>
@endsection