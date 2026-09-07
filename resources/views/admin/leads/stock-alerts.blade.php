@extends('admin.includes.masterpage-admin')
@section('content')
<style>
    .li-card{background:#fff;border-radius:8px;border:1px solid #e5e7eb;padding:20px;margin-bottom:20px}
    .stat-box{background:#f8fafc;border-radius:6px;padding:14px;text-align:center;border:1px solid #f1f5f9}
    .stat-box .val{font-size:24px;font-weight:700;color:#1e293b}
    .stat-box .lbl{font-size:12px;color:#64748b;margin-top:2px}
    .filter-bar{background:#f8fafc;border:1px solid #e5e7eb;border-radius:8px;padding:14px 18px;margin-bottom:18px;display:flex;gap:12px;align-items:center;flex-wrap:wrap}
    .filter-bar input{border:1px solid #e2e8f0;border-radius:5px;padding:5px 10px;font-size:13px;background:#fff}
    table.leads-table{width:100%;border-collapse:collapse;font-size:13px}
    table.leads-table thead tr{background:#f8fafc;border-bottom:2px solid #e5e7eb}
    table.leads-table th{padding:9px 12px;text-align:left;font-weight:600;color:#374151;white-space:nowrap}
    table.leads-table td{padding:8px 12px;border-bottom:1px solid #f1f5f9;vertical-align:middle}
    table.leads-table tbody tr:hover{background:#fafafa}
</style>
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 style="margin:0">Stock Alert Requests</h4>
        <div style="font-size:12px;color:#64748b;margin-top:2px">
            Customers who asked to be notified when an out-of-stock part becomes available
        </div>
    </div>
</div>

<div class="li-card">
    <div class="stat-box" style="max-width:200px">
        <div class="val">{{ $totalAlerts }}</div>
        <div class="lbl">Total Requests</div>
    </div>
</div>

<div class="filter-bar">
    <form method="GET" style="display:flex;gap:10px;align-items:center">
        <input type="text" name="search" value="{{ $search }}" placeholder="Search email or part number...">
        <button type="submit" class="btn btn-sm btn-primary">Filter</button>
        @if($search)
            <a href="{{ route('leads.stock-alerts') }}" class="btn btn-sm btn-light">Clear</a>
        @endif
    </form>
</div>

<div class="li-card">
    <table class="leads-table">
        <thead>
            <tr>
                <th>Email</th>
                <th>Part Number</th>
                <th>Requested At</th>
            </tr>
        </thead>
        <tbody>
            @forelse($alerts as $alert)
                <tr>
                    <td>{{ $alert->email }}</td>
                    <td>{{ $alert->part_number ?? '—' }}</td>
                    <td>{{ \Carbon\Carbon::parse($alert->created_at)->format('M j, Y g:i A') }}</td>
                </tr>
            @empty
                <tr><td colspan="3" style="text-align:center;color:#94a3b8;padding:20px">No stock alert requests yet.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-3">{{ $alerts->appends(['search' => $search])->links() }}</div>
</div>
@endsection
