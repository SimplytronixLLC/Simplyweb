@extends('admin.includes.masterpage-admin')

@section('content')

@php
    // Feather-style line icons, one per tile, matched to what that tile actually does.
    // 24x24 viewBox, stroke-based, currentColor so category color theming still works.
    $icon = function ($name) {
        $icons = [
            // Products — box
            'box' => '<path d="M21 8l-9-5-9 5 9 5 9-5z"/><path d="M3 8v8l9 5 9-5V8"/><path d="M12 13v8"/>',
            // Leads — target
            'target' => '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1"/>',
            // Quote Requests — message/quote
            'quote' => '<path d="M21 15a2 2 0 0 1-2 2H8l-5 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>',
            // CRM Pipeline — kanban columns
            'kanban' => '<rect x="3" y="4" width="5" height="16" rx="1"/><rect x="9.5" y="4" width="5" height="10" rx="1"/><rect x="16" y="4" width="5" height="13" rx="1"/>',
            // Win-back — refresh / recycle
            'winback' => '<path d="M23 4v6h-6"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/>',
            // Blog / News — newspaper
            'blog' => '<path d="M4 5h13a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V5a2 2 0 0 0-2-2h2z"/><path d="M8 9h8"/><path d="M8 13h8"/><path d="M8 17h5"/>',
            // SEO Tools — magnifier
            'seo' => '<circle cx="10.5" cy="10.5" r="6.5"/><path d="M21 21l-4.9-4.9"/>',
            // Pages Tool — stacked layers
            'pages' => '<path d="M12 3l9 5-9 5-9-5 9-5z"/><path d="M3 13l9 5 9-5"/>',
            // API Usage — activity/gauge
            'api' => '<path d="M22 12h-4l-3 9-6-18-3 9H2"/>',
            // Specs Editor — pencil
            'edit' => '<path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/>',
            // DigiKey Sync — cloud sync
            'sync' => '<path d="M17 16l4-4-4-4"/><path d="M3 12h18"/><path d="M7 8l-4 4 4 4"/>',
            // Harvest Control — bolt
            'harvest' => '<path d="M13 2 3 14h7l-1 8 10-12h-7l1-8z"/>',
        ];
        $body = $icons[$name] ?? $icons['box'];
        return '<svg viewBox="0 0 24 24" class="tile-icon" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">' . $body . '</svg>';
    };
@endphp

<style>
    .sx-dashboard {
        --bg:            #F7F8FA;
        --surface:       #FFFFFF;
        --border:        #E7EAF0;
        --text:          #1A1F2B;
        --muted:         #6B7280;

        --sales-1:       #53ADD0;
        --sales-2:       #4FC2C7;
        --content-1:     #F59E0B;
        --system-1:      #4F46E5;

        --ok:            #16A34A;
        --danger:        #DC2626;

        font-family: 'Poppins', sans-serif;
        background: var(--bg);
        padding: 4px 2px 24px;
    }

    .sx-dashboard .sx-eyebrow {
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
        font-size: 12px;
        letter-spacing: .06em;
        text-transform: uppercase;
        color: var(--muted);
        margin: 22px 4px 10px;
    }
    .sx-dashboard .sx-eyebrow:first-child { margin-top: 6px; }
    .sx-dashboard .sx-dot {
        width: 7px; height: 7px; border-radius: 50%; flex: none;
    }
    .sx-dashboard .sx-dot--sales   { background: linear-gradient(135deg, var(--sales-1), var(--sales-2)); }
    .sx-dashboard .sx-dot--content { background: var(--content-1); }
    .sx-dashboard .sx-dot--system  { background: var(--system-1); }

    .sx-dashboard .sx-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        gap: 10px;
    }

    .sx-dashboard .tile {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        gap: 6px;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 16px 8px 12px;
        text-decoration: none;
        color: var(--text);
        min-height: 108px;
        justify-content: center;
        -webkit-tap-highlight-color: transparent;
        touch-action: manipulation;
        transition: transform .12s ease, box-shadow .12s ease, border-color .12s ease;
    }
    .sx-dashboard .tile:hover {
        border-color: var(--border);
        box-shadow: 0 6px 16px rgba(20, 24, 40, .08);
        transform: translateY(-2px);
        color: var(--text);
    }
    .sx-dashboard .tile:active { transform: scale(.96); }

    .sx-dashboard .tile--disabled {
        opacity: .55;
        cursor: default;
        pointer-events: none;
    }

    .sx-dashboard .tile-icon { width: 26px; height: 26px; }
    .sx-dashboard .tile--sales   .tile-icon { color: var(--sales-1); }
    .sx-dashboard .tile--content .tile-icon { color: var(--content-1); }
    .sx-dashboard .tile--system  .tile-icon { color: var(--system-1); }

    .sx-dashboard .tile-label {
        font-size: 12.5px;
        font-weight: 600;
        line-height: 1.2;
    }
    .sx-dashboard .tile-sub {
        font-size: 10px;
        color: var(--muted);
        line-height: 1.2;
    }

    /* ── Harvest widget ─────────────────────────────────────────── */
    .sx-dashboard .harvest {
        grid-column: 1 / -1;
        background: linear-gradient(180deg, #F4F0FF 0%, var(--surface) 55%);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 16px;
        text-align: left;
    }
    .sx-dashboard .harvest-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 12px;
    }
    .sx-dashboard .harvest-title {
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
        font-size: 13.5px;
    }
    .sx-dashboard .harvest-title .tile-icon { width: 22px; height: 22px; color: var(--system-1); }

    .sx-dashboard .harvest-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 11px;
        font-weight: 600;
        color: var(--muted);
        background: #F0F1F5;
        border-radius: 999px;
        padding: 4px 10px;
    }
    .sx-dashboard .harvest-status .pulse {
        width: 6px; height: 6px; border-radius: 50%; background: var(--muted);
    }

    .sx-dashboard .harvest-btns { display: flex; gap: 8px; margin-bottom: 14px; }
    .sx-dashboard .harvest-btns button {
        flex: 1;
        border: none;
        border-radius: 10px;
        padding: 10px 0;
        font-size: 13px;
        font-weight: 600;
        color: #fff;
        -webkit-tap-highlight-color: transparent;
        touch-action: manipulation;
        transition: transform .1s ease, opacity .1s ease;
    }
    .sx-dashboard .harvest-btns button:active { transform: scale(.96); }
    .sx-dashboard .btn-start { background: var(--ok); }
    .sx-dashboard .btn-stop  { background: var(--danger); }

    .sx-dashboard .harvest-stats {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 8px 14px;
        font-size: 12px;
    }
    .sx-dashboard .harvest-stats .row {
        display: flex;
        justify-content: space-between;
        border-bottom: 1px dashed var(--border);
        padding-bottom: 5px;
    }
    .sx-dashboard .harvest-stats .row span:first-child { color: var(--muted); }
    .sx-dashboard .harvest-stats .row b {
        font-family: ui-monospace, Menlo, Consolas, monospace;
        font-weight: 600;
    }

    @media (min-width: 560px) {
        .sx-dashboard .sx-grid { grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); }
        .sx-dashboard .harvest { grid-column: span 2; }
    }
    @media (min-width: 900px) {
        .sx-dashboard .sx-grid { grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); }
    }

    @media (prefers-reduced-motion: reduce) {
        .sx-dashboard .tile, .sx-dashboard .harvest-btns button { transition: none; }
    }
</style>

<div class="sx-dashboard">

    {{-- SALES --}}
    <div class="sx-eyebrow"><span class="sx-dot sx-dot--sales"></span> Revenue &amp; Sales</div>
    <div class="sx-grid">

        <a href="{{ url('admin/products') }}" class="tile tile--sales">
            {!! $icon('box') !!}
            <span class="tile-label">Products</span>
            <span class="tile-sub">Manage products</span>
        </a>

        <a href="{{ route('admin.leads.index') }}" class="tile tile--sales">
            {!! $icon('target') !!}
            <span class="tile-label">Leads</span>
            <span class="tile-sub">High-intent visitors</span>
        </a>

        <a href="{{ route('admin.quotation') }}" class="tile tile--sales">
            {!! $icon('quote') !!}
            <span class="tile-label">Quote Requests</span>
            <span class="tile-sub">Website RFQs</span>
        </a>

        <a href="{{ url('admin/crm') }}" class="tile tile--sales">
            {!! $icon('kanban') !!}
            <span class="tile-label">CRM Pipeline</span>
            <span class="tile-sub">Contacts &amp; stages</span>
        </a>

        <a href="{{ url('admin/crm/winback') }}" class="tile tile--sales">
            {!! $icon('winback') !!}
            <span class="tile-label">Win-back</span>
            <span class="tile-sub">Review &amp; send campaigns</span>
        </a>

    </div>


    {{-- CONTENT --}}
    <div class="sx-eyebrow"><span class="sx-dot sx-dot--content"></span> Content &amp; SEO</div>
    <div class="sx-grid">

        <a href="{{ route('admin.posts.index') }}" class="tile tile--content">
            {!! $icon('blog') !!}
            <span class="tile-label">Blog / News</span>
            <span class="tile-sub">Manage articles</span>
        </a>

        <div class="tile tile--content tile--disabled">
            {!! $icon('seo') !!}
            <span class="tile-label">SEO Tools</span>
            <span class="tile-sub">Meta &amp; schema</span>
        </div>

        <a href="{{ route('admin.pages.index') }}" class="tile tile--content">
            {!! $icon('pages') !!}
            <span class="tile-label">Pages Tool</span>
            <span class="tile-sub">Edit static page content</span>
        </a>

    </div>


    {{-- SYSTEM --}}
    <div class="sx-eyebrow"><span class="sx-dot sx-dot--system"></span> System &amp; Operations</div>
    <div class="sx-grid">

        <a href="{{ url('admin/api-usage') }}" class="tile tile--system">
            {!! $icon('api') !!}
            <span class="tile-label">API Usage</span>
            <span class="tile-sub">Daily quota monitoring</span>
        </a>

        <a href="{{ route('admin.specs.index') }}" class="tile tile--system">
            {!! $icon('edit') !!}
            <span class="tile-label">Specs Editor</span>
            <span class="tile-sub">Update individual part specs</span>
        </a>

        <a href="{{ route('admin.sync.index') }}" class="tile tile--system">
            {!! $icon('sync') !!}
            <span class="tile-label">DigiKey Sync</span>
            <span class="tile-sub">Specs sync control</span>
        </a>

        {{-- HARVEST WIDGET --}}
        <div class="harvest">
            <div class="harvest-top">
                <div class="harvest-title">
                    {!! $icon('harvest') !!}
                    Harvest Control
                </div>
                <span class="harvest-status"><span class="pulse" id="h-status-dot"></span> <span id="h-status">Idle</span></span>
            </div>

            <div class="harvest-btns">
                <button class="btn-start" onclick="startHarvest()">Start</button>
                <button class="btn-stop" onclick="stopHarvest()">Stop</button>
            </div>

            <div class="harvest-stats">
                <div class="row"><span>Keyword</span><b id="h-key">-</b></div>
                <div class="row"><span>Offset</span><b id="h-offset">0</b></div>
                <div class="row"><span>API calls</span><b id="h-api">0</b></div>
                <div class="row"><span>Fetched</span><b id="h-fetched">0</b></div>
                <div class="row"><span>Inserted</span><b id="h-inserted">0</b></div>
                <div class="row"><span>Stop reason</span><b id="h-stopreason">-</b></div>
            </div>
        </div>

    </div>

</div>

@endsection
