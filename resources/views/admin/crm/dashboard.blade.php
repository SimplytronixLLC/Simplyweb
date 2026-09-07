@extends('admin.includes.masterpage-admin')

@section('content')

@php
    // ---- derive totals from what the controller already gives us ----
    $stageLabels = ['new' => 'New', 'contacted' => 'Contacted', 'quoted' => 'Quoted', 'won' => 'Won', 'lost' => 'Lost'];
    $stageColors = ['new' => '#64748b', 'contacted' => '#2563eb', 'quoted' => '#d97706', 'won' => '#16a34a', 'lost' => '#dc2626'];

    $actionLabels = [
        'rfq_received'   => 'RFQ Received',
        'quote_sent'     => 'Quote Sent',
        'quote_signed'   => 'Quote Signed',
        'quote_unsigned' => 'Quote Unsigned',
        'no_quote'       => 'No Quote',
        'invalid_rfq'    => 'Invalid RFQ',
    ];
    $actionColors = [
        'rfq_received'   => '#2563eb',
        'quote_sent'     => '#d97706',
        'quote_signed'   => '#16a34a',
        'quote_unsigned' => '#7c3aed',
        'no_quote'       => '#64748b',
        'invalid_rfq'    => '#dc2626',
    ];

    $totalContacts = $byStage->sum();
    $bouncedCount  = $cadenceFunnel['bounced'] ?? 0;
    $activeCount   = $totalContacts - $bouncedCount;
    $enrolledCount = $cadenceFunnel['enrolled'] ?? 0;
    $inProgress    = ($cadenceFunnel['step_1'] ?? 0) + ($cadenceFunnel['step_2'] ?? 0) + ($cadenceFunnel['step_3'] ?? 0);
    $completedCount= $cadenceFunnel['completed'] ?? 0;

    // fill last 12 months so the line chart doesn't have gaps for months with 0 leads
    $monthLabels = [];
    $monthValues = [];
    for ($i = 11; $i >= 0; $i--) {
        $ym = now()->subMonths($i)->format('Y-m');
        $monthLabels[] = now()->subMonths($i)->format('M Y');
        $monthValues[] = (int) ($leadsByMonth[$ym] ?? 0);
    }

    $sourceLabels = $bySource->keys()->map(fn($s) => $s ? ucwords(str_replace('_', ' ', $s)) : 'Unknown')->values();
    $sourceValues = $bySource->values();
    $sourcePalette = ['#2563eb', '#16a34a', '#d97706', '#7c3aed', '#0d9488', '#dc2626', '#64748b', '#0ea5e9'];
    $sourcePaletteSliced = array_slice($sourcePalette, 0, max($sourceLabels->count(), 1));

    $actionKeys = $manualActions->keys();
    $actionVals = $manualActions->values();
    $actionLabelsMapped = $actionKeys->map(fn($k) => $actionLabels[$k] ?? ucwords(str_replace('_', ' ', $k)));
    $actionColorsMapped = $actionKeys->map(fn($k) => $actionColors[$k] ?? '#64748b');

    $stageCounts = array_map(fn($k) => (int) ($byStage[$k] ?? 0), array_keys($stageLabels));

    $bounceRateDisplay = is_numeric($bounceRate) ? rtrim(rtrim(number_format($bounceRate, 1), '0'), '.') : $bounceRate;
@endphp

<style>
    .crm-app {
        --ink: #0f172a;
        --ink-soft: #475569;
        --muted: #94a3b8;
        --border: #e6e9ef;
        --surface: #ffffff;
        --bg: #f8fafc;
        --radius: 10px;
        --shadow: 0 1px 2px rgba(15,23,42,.04), 0 1px 1px rgba(15,23,42,.03);
        font-variant-numeric: tabular-nums;
        color: var(--ink);
    }
    .crm-app .crm-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
    }
    .crm-app .crm-card-header {
        padding: 16px 20px;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 10px;
        flex-wrap: wrap;
    }
    .crm-app .crm-title { font-size: 14.5px; font-weight: 600; color: var(--ink); margin: 0; }
    .crm-app .crm-subtitle { font-size: 12px; color: var(--ink-soft); margin: 3px 0 0; max-width: 46ch; }
    .crm-app .crm-card-body { padding: 20px; }
    .crm-app .crm-page-header { display: flex; align-items: flex-end; justify-content: space-between; flex-wrap: wrap; gap: 12px; margin-bottom: 20px; }
    .crm-app .crm-page-title { font-size: 20px; font-weight: 700; color: var(--ink); margin: 0; }
    .crm-app .crm-page-subtitle { font-size: 13px; color: var(--ink-soft); margin: 4px 0 0; }

    .crm-app .crm-stat-strip {
        display: flex;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }
    .crm-app .crm-stat {
        flex: 1 1 150px;
        padding: 16px 20px;
        border-right: 1px solid var(--border);
    }
    .crm-app .crm-stat:last-child { border-right: none; }
    .crm-app .crm-stat-value { font-size: 23px; font-weight: 700; line-height: 1.1; }
    .crm-app .crm-stat-label { font-size: 11.5px; color: var(--ink-soft); margin-top: 5px; letter-spacing: .01em; }
    .crm-app .crm-stat-sub { font-size: 10.5px; color: var(--muted); margin-top: 1px; }

    .crm-app .crm-empty { text-align: center; padding: 36px 8px; color: var(--muted); font-size: 12.5px; }
    .crm-app .crm-empty-icon { font-size: 22px; margin-bottom: 8px; opacity: .5; }

    @media (max-width: 767px) {
        .crm-app .crm-stat { flex: 1 1 50%; border-right: none; border-bottom: 1px solid var(--border); }
    }
</style>

<div class="crm-app">

    <div class="crm-page-header">
        <div>
            <h1 class="crm-page-title">Dashboard</h1>
            <p class="crm-page-subtitle">Pipeline health, cadence progress, and lead sources at a glance.</p>
        </div>
        <a href="{{ route('admin.crm.export') }}" class="btn btn-outline-primary btn-sm">
            <i class="fa fa-download mr-1"></i> Export contacts (CSV)
        </a>
    </div>

    {{-- ---------------- unified stat strip ---------------- --}}
    <div class="crm-card crm-stat-strip">
        <div class="crm-stat">
            <div class="crm-stat-value">{{ $totalContacts }}</div>
            <div class="crm-stat-label">Total contacts</div>
        </div>
        <div class="crm-stat">
            <div class="crm-stat-value" style="color:#16a34a;">{{ $activeCount }}</div>
            <div class="crm-stat-label">Active</div>
        </div>
        <div class="crm-stat">
            <div class="crm-stat-value" style="color:#dc2626;">{{ $bouncedCount }}</div>
            <div class="crm-stat-label">Bounced</div>
            <div class="crm-stat-sub">{{ $bounceRateDisplay }}% of total</div>
        </div>
        <div class="crm-stat">
            <div class="crm-stat-value" style="color:#2563eb;">{{ $enrolledCount }}</div>
            <div class="crm-stat-label">Enrolled in cadence</div>
        </div>
        <div class="crm-stat">
            <div class="crm-stat-value" style="color:#d97706;">{{ $inProgress }}</div>
            <div class="crm-stat-label">Mid-cadence</div>
        </div>
        <div class="crm-stat">
            <div class="crm-stat-value" style="color:#0d9488;">{{ $completedCount }}</div>
            <div class="crm-stat-label">Cadence completed</div>
        </div>
    </div>

    {{-- ---------------- pipeline + leads over time ---------------- --}}
    <div class="row">
        <div class="col-lg-6 mb-3">
            <div class="crm-card h-100">
                <div class="crm-card-header">
                    <div>
                        <h4 class="crm-title">Pipeline by stage</h4>
                        <p class="crm-subtitle">Where every active contact sits right now.</p>
                    </div>
                </div>
                <div class="crm-card-body">
                    <div style="position:relative; height:170px;"><canvas id="stageChart"></canvas></div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-3">
            <div class="crm-card h-100">
                <div class="crm-card-header">
                    <div>
                        <h4 class="crm-title">New leads</h4>
                        <p class="crm-subtitle">Last 12 months.</p>
                    </div>
                </div>
                <div class="crm-card-body">
                    <div style="position:relative; height:170px;"><canvas id="monthChart"></canvas></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ---------------- source + manual action ---------------- --}}
    <div class="row">
        <div class="col-lg-5 mb-3">
            <div class="crm-card h-100">
                <div class="crm-card-header">
                    <h4 class="crm-title">Lead source</h4>
                </div>
                <div class="crm-card-body">
                    @if($sourceValues->sum() > 0)
                        <div style="position:relative; height:190px;"><canvas id="sourceChart"></canvas></div>
                    @else
                        <div class="crm-empty">
                            <div class="crm-empty-icon">&#9679;</div>
                            No source data yet.
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-7 mb-3">
            <div class="crm-card h-100">
                <div class="crm-card-header">
                    <h4 class="crm-title">Manual action taken</h4>
                </div>
                <div class="crm-card-body">
                    @if($actionVals->sum() > 0)
                        <div style="position:relative; height:190px;"><canvas id="actionChart"></canvas></div>
                    @else
                        <div class="crm-empty">
                            <div class="crm-empty-icon">&#9679;</div>
                            No manual actions logged yet.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ---------------- cadence funnel ---------------- --}}
    <div class="row">
        <div class="col-12 mb-3">
            <div class="crm-card">
                <div class="crm-card-header">
                    <div>
                        <h4 class="crm-title">Cadence funnel</h4>
                        <p class="crm-subtitle">Contacts enrolled in the 0 / 7 / 14 day marketing cadence, by how far they've progressed.</p>
                    </div>
                </div>
                <div class="crm-card-body">
                    <div style="position:relative; height:110px;"><canvas id="cadenceChart"></canvas></div>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection

@section('footer')
<script src="{{ URL::asset('public/assets/admin/vendor/chartjs/chart.umd.min.js') }}"></script>
<script>
$(document).ready(function () {

    Chart.defaults.font.family = "inherit";
    Chart.defaults.color = "#64748b";
    Chart.defaults.font.size = 12;

    // ---- pipeline by stage ----
    new Chart(document.getElementById('stageChart'), {
        type: 'bar',
        data: {
            labels: @json(array_values($stageLabels)),
            datasets: [{
                data: @json($stageCounts),
                backgroundColor: @json(array_values($stageColors)),
                borderRadius: 4,
                maxBarThickness: 42,
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#f1f5f9' } },
                x: { grid: { display: false } }
            }
        }
    });

    // ---- leads by month ----
    new Chart(document.getElementById('monthChart'), {
        type: 'line',
        data: {
            labels: @json($monthLabels),
            datasets: [{
                label: 'New leads',
                data: @json($monthValues),
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37,99,235,0.08)',
                tension: 0.3,
                fill: true,
                pointRadius: 0,
                pointHoverRadius: 4,
                borderWidth: 2,
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#f1f5f9' } },
                x: { grid: { display: false } }
            }
        }
    });

    @if($sourceValues->sum() > 0)
    // ---- lead source ----
    new Chart(document.getElementById('sourceChart'), {
        type: 'doughnut',
        data: {
            labels: @json($sourceLabels),
            datasets: [{
                data: @json($sourceValues),
                backgroundColor: @json($sourcePaletteSliced),
                borderWidth: 2,
                borderColor: '#ffffff',
            }]
        },
        options: {
            maintainAspectRatio: false,
            cutout: '68%',
            plugins: { legend: { position: 'right', labels: { boxWidth: 10, padding: 14 } } }
        }
    });
    @endif

    @if($actionVals->sum() > 0)
    // ---- manual action breakdown ----
    new Chart(document.getElementById('actionChart'), {
        type: 'bar',
        data: {
            labels: @json($actionLabelsMapped),
            datasets: [{
                data: @json($actionVals),
                backgroundColor: @json($actionColorsMapped),
                borderRadius: 4,
                maxBarThickness: 22,
            }]
        },
        options: {
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: { legend: { display: false } },
            scales: {
                x: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#f1f5f9' } },
                y: { grid: { display: false } }
            }
        }
    });
    @endif

    // ---- cadence funnel ----
    new Chart(document.getElementById('cadenceChart'), {
        type: 'bar',
        data: {
            labels: ['Enrolled (Day 0)', 'Step 1 (Day 7)', 'Step 2', 'Step 3+', 'Completed', 'Bounced'],
            datasets: [{
                data: [
                    {{ $cadenceFunnel['enrolled'] ?? 0 }},
                    {{ $cadenceFunnel['step_1'] ?? 0 }},
                    {{ $cadenceFunnel['step_2'] ?? 0 }},
                    {{ $cadenceFunnel['step_3'] ?? 0 }},
                    {{ $cadenceFunnel['completed'] ?? 0 }},
                    {{ $cadenceFunnel['bounced'] ?? 0 }}
                ],
                backgroundColor: ['#2563eb', '#60a5fa', '#93c5fd', '#bfdbfe', '#16a34a', '#dc2626'],
                borderRadius: 4,
                maxBarThickness: 22,
            }]
        },
        options: {
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: { legend: { display: false } },
            scales: {
                x: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#f1f5f9' } },
                y: { grid: { display: false } }
            }
        }
    });

});
</script>
@endsection