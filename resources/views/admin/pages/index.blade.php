@extends('admin.includes.masterpage-admin')

@section('content')

@php
    // Same feather-style line icon technique as the dashboard tiles —
    // 24x24 viewBox, stroke-based, currentColor.
    $icon = function ($name) {
        $icons = [
            'terms'   => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>',
            'upload'  => '<polyline points="16 16 12 12 8 16"/><line x1="12" y1="12" x2="12" y2="21"/><path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"/>',
            'users'   => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
            'mail'    => '<path d="M4 4h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z"/><polyline points="22,6 12,13 2,6"/>',
            'shield'  => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/>',
            'briefcase' => '<rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>',
            'pages'   => '<path d="M12 3l9 5-9 5-9-5 9-5z"/><path d="M3 13l9 5 9-5"/>',
        ];
        $body = $icons[$name] ?? $icons['pages'];
        return '<svg viewBox="0 0 24 24" class="tile-icon" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">' . $body . '</svg>';
    };

    // slug -> icon key, so new schema pages just fall back to the generic
    // "pages" icon instead of needing a code change here.
    $pageIcons = [
        'terms' => 'terms',
        'upload' => 'upload',
        'about-us' => 'users',
        'contact-us' => 'mail',
        'quality-assurance' => 'shield',
        'industries' => 'briefcase',
    ];
@endphp

<style>
    .sx-pages {
        --border: #E7EAF0;
        --text:   #1A1F2B;
        --muted:  #6B7280;
        --content-1: #F59E0B;

        font-family: 'Poppins', sans-serif;
        max-width: 720px;
    }
    .sx-pages h4 { font-weight: 600; margin-bottom: 4px; }
    .sx-pages .sx-pages-sub { color: var(--muted); font-size: 13px; margin-bottom: 20px; }

    .sx-pages .sx-page-row {
        display: flex;
        align-items: center;
        gap: 14px;
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 14px 16px;
        margin-bottom: 10px;
        text-decoration: none;
        color: var(--text);
        transition: box-shadow .12s ease, transform .12s ease;
    }
    .sx-pages .sx-page-row:hover {
        box-shadow: 0 6px 16px rgba(20,24,40,.08);
        transform: translateY(-1px);
        color: var(--text);
    }

    .sx-pages .tile-icon { width: 26px; height: 26px; flex: none; color: var(--content-1); }

    .sx-pages .sx-page-text { flex: 1; min-width: 0; }
    .sx-pages .sx-page-title { font-size: 12.5px; font-weight: 600; line-height: 1.2; }
    .sx-pages .sx-page-slug {
        font-size: 10px;
        color: var(--muted);
        line-height: 1.2;
        font-family: ui-monospace, Menlo, Consolas, monospace;
        margin-top: 2px;
    }

    .sx-pages .sx-badge {
        font-size: 11px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 999px;
        white-space: nowrap;
        flex: none;
    }
    .sx-pages .sx-badge--live { background: #DCFCE7; color: #16A34A; }
    .sx-pages .sx-badge--default { background: #F0F1F5; color: var(--muted); }
</style>

<div class="sx-pages">
    <h4>Pages</h4>
    <div class="sx-pages-sub">Edit the content of your static site pages. Changes go live immediately.</div>

    @foreach($schemas as $slug => $schema)
        <a href="{{ route('admin.pages.edit', $slug) }}" class="sx-page-row">
            {!! $icon($pageIcons[$slug] ?? 'pages') !!}
            <div class="sx-page-text">
                <div class="sx-page-title">{{ $schema['label'] }}</div>
                <div class="sx-page-slug">/{{ $slug }}</div>
            </div>
            @if(isset($pages[$slug]))
                <span class="sx-badge sx-badge--live">Customized</span>
            @else
                <span class="sx-badge sx-badge--default">Using defaults</span>
            @endif
        </a>
    @endforeach
</div>

@endsection
