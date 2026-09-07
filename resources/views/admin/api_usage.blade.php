@extends('admin.includes.masterpage-admin')

@section("content")

<style>
@media (max-width: 768px) {
    h2 { font-size: 1.25rem !important; }
    h3 { font-size: 1.05rem !important; }
    h4 { font-size: 1rem !important; }
    h5 { font-size: 0.9rem !important; }
    .card { padding: 0.65rem !important; }
    .card-header { padding: 0.5rem 0.75rem !important; }
    .table { font-size: 11px !important; }
    .table th, .table td { padding: 0.35rem !important; white-space: nowrap; }
    .badge { font-size: 9px !important; padding: 2px 5px !important; min-width: 0; }
    .progress { height: 6px !important; }
    .container { padding-left: 8px !important; padding-right: 8px !important; }
    .row.g-3 > div { margin-bottom: 0.5rem; }
    code { font-size: 10px !important; }
}
</style>

<div class="container mt-4">

    {{-- HEADER + PROVIDER TOGGLE --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h2 class="mb-0">API Usage Dashboard</h2>
            <p class="text-muted mb-0">
                Viewing usage for <strong>{{ ucfirst($provider) }}</strong>
                @if($provider === 'digikey')
                    &mdash; <span class="text-secondary">{{ $keyCount }} key{{ $keyCount !== 1 ? 's' : '' }} configured</span>
                @endif
            </p>
        </div>

        {{-- CARD TOGGLE --}}
        <div class="d-flex gap-3">

            <a href="?provider=mouser"
               class="card px-3 py-2 text-center text-decoration-none shadow-sm
               {{ $provider=='mouser' ? 'border-primary border-2' : '' }}"
               style="min-width:130px;">
                <strong>Mouser</strong>
                <div style="font-size:12px;">Electronics</div>
            </a>

            <a href="?provider=digikey"
               class="card px-3 py-2 text-center text-decoration-none shadow-sm
               {{ $provider=='digikey' ? 'border-success border-2' : '' }}"
               style="min-width:130px;">
                <strong>Digi-Key</strong>
                <div style="font-size:12px;">Components</div>
            </a>

        </div>
    </div>


    {{-- TODAY SUMMARY --}}
    <div class="row g-3 mb-4">

        {{-- Main usage card --}}
        <div class="col-md-5">
            <div class="card shadow p-4 h-100">
                <h5 class="text-muted mb-1">Today's Usage</h5>
                <h2 class="mb-0">{{ $today }} <span class="text-muted fs-5">/ {{ $limit }}</span></h2>
                <div class="mt-2">
                    <div class="progress" style="height:12px; border-radius:6px;">
                        <div class="progress-bar
                            {{ $percent >= 90 ? 'bg-danger' : ($percent >= 60 ? 'bg-warning' : 'bg-success') }}"
                            role="progressbar"
                            style="width: {{ min($percent, 100) }}%"
                            aria-valuenow="{{ $percent }}"
                            aria-valuemin="0"
                            aria-valuemax="100">
                        </div>
                    </div>
                    <small class="text-muted mt-1 d-block">{{ $percent }}% consumed</small>
                </div>
                @if($provider === 'digikey')
                    <small class="text-muted mt-2">
                        Limit = {{ $keyCount }} key{{ $keyCount !== 1 ? 's' : '' }} &times; 1,000 calls/day
                    </small>
                @endif
            </div>
        </div>

        {{-- Quick stats --}}
        <div class="col-md-7">
            <div class="row g-3 h-100">
                <div class="col-6">
                    <div class="card shadow p-3 h-100 text-center">
                        <div class="text-muted" style="font-size:12px;">Yesterday</div>
                        <h3 class="mb-0">{{ $yesterday }}</h3>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card shadow p-3 h-100 text-center">
                        <div class="text-muted" style="font-size:12px;">All-time Total</div>
                        <h3 class="mb-0">{{ number_format($total) }}</h3>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card shadow p-3 h-100 text-center">
                        <div class="text-muted" style="font-size:12px;">Remaining Today</div>
                        <h3 class="mb-0 {{ ($limit - $today) < 100 ? 'text-danger' : 'text-success' }}">
                            {{ max(0, $limit - $today) }}
                        </h3>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card shadow p-3 h-100 text-center">
                        <div class="text-muted" style="font-size:12px;">This Month</div>
                        <h3 class="mb-0">{{ number_format($monthlyUsage->sum()) }}</h3>
                    </div>
                </div>
            </div>
        </div>

    </div>


    @if($provider === 'digikey')
    {{-- PER-KEY BREAKDOWN --}}
    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Today's Usage by Key</h5>
            @if($exhaustionEvents->isNotEmpty())
                <span class="badge bg-danger">
                    {{ $exhaustionEvents->count() }} key{{ $exhaustionEvents->count() !== 1 ? 's' : '' }} exhausted today
                </span>
            @endif
        </div>
        <div class="card-body p-0">
            @if($keyBreakdown->isEmpty())
                <p class="text-center text-muted py-4 mb-0">No per-key data logged today yet.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Key</th>
                                <th class="text-end">Total Calls</th>
                                <th class="text-end">Successful</th>
                                <th class="text-end">429s (throttled)</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $exhaustedKeyIndexes = $exhaustionEvents->pluck('key_index')->unique();
                            @endphp
                            @for($i = 0; $i < $keyCount; $i++)
                                @php
                                    $row = $keyBreakdown->firstWhere('key_index', $i);
                                    $isExhausted = $exhaustedKeyIndexes->contains($i);
                                @endphp
                                <tr class="{{ $isExhausted ? 'table-danger' : '' }}">
                                    <td><strong>Key #{{ $i }}</strong></td>
                                    <td class="text-end">{{ $row->calls ?? 0 }}</td>
                                    <td class="text-end text-success">{{ $row->successful_calls ?? 0 }}</td>
                                    <td class="text-end {{ ($row->throttled_calls ?? 0) > 0 ? 'text-danger fw-bold' : 'text-muted' }}">
                                        {{ $row->throttled_calls ?? 0 }}
                                    </td>
                                    <td>
                                        @if($isExhausted)
                                            <span class="badge bg-danger">Exhausted (Retry-After &gt; 3h)</span>
                                        @elseif(($row->throttled_calls ?? 0) > 0)
                                            <span class="badge bg-warning text-dark">Throttled</span>
                                        @elseif(($row->calls ?? 0) > 0)
                                            <span class="badge bg-success">Healthy</span>
                                        @else
                                            <span class="badge bg-secondary">Unused today</span>
                                        @endif
                                    </td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>


    {{-- EXHAUSTION / THROTTLE EVENTS --}}
    @if($allThrottleEvents->isNotEmpty())
    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">429 Events Today</h5>
            <div class="d-flex align-items-center gap-3">
                <small class="text-muted">Exhausted = Retry-After &gt; {{ round($exhaustionThreshold / 3600, 1) }}h</small>
                <button class="btn btn-sm btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#throttleEventsTable">Show / Hide</button>
            </div>
        </div>
        <div id="throttleEventsTable" class="collapse">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered mb-0" style="font-size:13px;">
                    <thead class="table-light">
                        <tr>
                            <th>Time</th>
                            <th>Key</th>
                            <th>Caller / Function</th>
                            <th>Retry-After</th>
                            <th>Severity</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($allThrottleEvents as $event)
                            @php
                                $retrySeconds = $event->retry_after_seconds;
                                $isExhausted  = $retrySeconds !== null && $retrySeconds > $exhaustionThreshold;
                                $retryDisplay = $retrySeconds !== null
                                    ? gmdate($retrySeconds >= 3600 ? 'H\h i\m' : 'i\m s\s', $retrySeconds)
                                    : '—';
                            @endphp
                            <tr class="{{ $isExhausted ? 'table-danger' : '' }}">
                                <td class="text-nowrap">{{ $event->called_at->format('H:i:s') }}</td>
                                <td>Key #{{ $event->key_index ?? '?' }}</td>
                                <td><code>{{ $event->controller }}</code></td>
                                <td>{{ $retryDisplay }}</td>
                                <td>
                                    @if($isExhausted)
                                        <span class="badge bg-danger">Exhausted</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Short throttle</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        </div>
    </div>
    @endif
    @endif


    {{-- PER-FUNCTION BREAKDOWN (today) --}}
    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Today's Usage by Caller / Function</h5>
            <span class="badge bg-secondary">{{ $today }} total calls</span>
        </div>
        <div class="card-body p-0">
            @if($callerBreakdown->isEmpty())
                <p class="text-center text-muted py-4 mb-0">No API calls logged today.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width:40%">Caller / Function</th>
                                <th style="width:15%">Endpoint</th>
                                <th style="width:10%" class="text-end">Calls</th>
                                <th style="width:35%">Share of today</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($callerBreakdown as $row)
                                @php
                                    $pct = $today > 0 ? round(($row->calls / $today) * 100, 1) : 0;
                                    $barColor = match(true) {
                                        $pct >= 50 => 'bg-danger',
                                        $pct >= 25 => 'bg-warning',
                                        default    => 'bg-info',
                                    };
                                @endphp
                                <tr>
                                    <td>
                                        <code class="text-dark">{{ $row->controller }}</code>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">{{ $row->endpoint }}</span>
                                    </td>
                                    <td class="text-end fw-bold">{{ $row->calls }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1" style="height:8px; border-radius:4px;">
                                                <div class="progress-bar {{ $barColor }}"
                                                     role="progressbar"
                                                     style="width: {{ $pct }}%">
                                                </div>
                                            </div>
                                            <small class="text-muted" style="min-width:36px;">{{ $pct }}%</small>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>


    {{-- TODAY API CALLS (collapsible log) --}}
    <div class="card shadow mb-4">

        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Today's API Call Log</h5>
            <button class="btn btn-sm btn-primary"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#todayCallsTable">
                Show / Hide
            </button>
        </div>

        <div id="todayCallsTable" class="collapse">

            <div class="p-3" style="overflow-x:auto;">
                <table class="table table-bordered mb-0" style="font-size:13px;">
                    <thead class="table-light">
                        <tr>
                            <th>Time</th>
                            <th>Caller / Function</th>
                            <th>Key</th>
                            <th>Endpoint</th>
                            <th>Status</th>
                            <th>Query (MPN)</th>
                            <th>IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($todayCalls as $call)
                        <tr class="{{ $call->status_code === 429 ? 'table-warning' : '' }}">
                            <td class="text-nowrap">{{ $call->called_at->format('H:i:s') }}</td>
                            <td><code>{{ $call->controller }}</code></td>
                            <td>{{ $call->key_index !== null ? '#' . $call->key_index : '—' }}</td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $call->endpoint }}</span>
                            </td>
                            <td>
                                @if($call->status_code)
                                    <span class="badge {{ $call->status_code === 429 ? 'bg-danger' : ($call->status_code >= 200 && $call->status_code < 300 ? 'bg-success' : 'bg-secondary') }}">
                                        {{ $call->status_code }}
                                    </span>
                                @else
                                    —
                                @endif
                            </td>
                            <td>{{ $call->query }}</td>
                            <td class="text-muted">{{ $call->ip_address }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-3">No API calls today</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>


    {{-- MONTHLY CALENDAR --}}
    <div class="card shadow p-4">
        <h4 class="mb-3">This Month</h4>

        <div style="display:grid; grid-template-columns: repeat(7, 1fr); gap:10px;">

            {{-- WEEKDAY HEADER --}}
            @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $day)
                <div style="text-align:center; font-weight:bold; font-size:13px;">{{ $day }}</div>
            @endforeach

            @php
                $start    = now()->startOfMonth();
                $end      = now()->endOfMonth();
                $current  = $start->copy();
                $startDay = $start->dayOfWeek;
                $todayDay = now()->day;
            @endphp

            {{-- EMPTY CELLS --}}
            @for ($i = 0; $i < $startDay; $i++)
                <div></div>
            @endfor

            {{-- DAYS --}}
            @while($current <= $end)

                @php
                    $dateKey  = $current->format('Y-m-d');
                    $count    = $monthlyUsage[$dateKey] ?? 0;
                    $isToday  = $current->day === $todayDay;

                    $color = '#f0f4f8';                      // empty / zero
                    if ($count > 700) $color = '#ff4d4d';    // over limit
                    elseif ($count > 400) $color = '#ffa726';
                    elseif ($count > 100) $color = '#4caf50';
                    elseif ($count > 0)   $color = '#b2dfdb';
                @endphp

                <div style="
                    background: {{ $color }};
                    padding: 12px 8px;
                    border-radius: 10px;
                    text-align: center;
                    min-height: 72px;
                    transition: transform 0.15s;
                    {{ $isToday ? 'outline: 2px solid #0d6efd; outline-offset: 2px;' : '' }}
                "
                onmouseover="this.style.transform='scale(1.06)'"
                onmouseout="this.style.transform='scale(1)'"
                title="{{ $dateKey }}: {{ $count }} calls"
                >
                    <strong style="font-size:14px;">{{ $current->day }}</strong>
                    <div style="font-size:12px; margin-top:4px;">{{ $count > 0 ? $count : '—' }}</div>
                </div>

                @php $current->addDay(); @endphp

            @endwhile

        </div>

        {{-- Legend --}}
        <div class="d-flex gap-3 mt-3 flex-wrap" style="font-size:12px;">
            <span><span style="display:inline-block;width:14px;height:14px;border-radius:3px;background:#f0f4f8;border:1px solid #ccc;"></span> No calls</span>
            <span><span style="display:inline-block;width:14px;height:14px;border-radius:3px;background:#b2dfdb;"></span> 1–100</span>
            <span><span style="display:inline-block;width:14px;height:14px;border-radius:3px;background:#4caf50;"></span> 101–400</span>
            <span><span style="display:inline-block;width:14px;height:14px;border-radius:3px;background:#ffa726;"></span> 401–700</span>
            <span><span style="display:inline-block;width:14px;height:14px;border-radius:3px;background:#ff4d4d;"></span> 700+</span>
            <span><span style="display:inline-block;width:14px;height:14px;border-radius:3px;border:2px solid #0d6efd;"></span> Today</span>
        </div>
    </div>

</div>

@endsection