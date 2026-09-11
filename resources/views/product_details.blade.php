@extends('includes.front')

@section('content')

@php

if (!function_exists('s')) {
    function s($v){
        return (is_string($v) || is_numeric($v)) ? trim((string)$v) : '';
    }
}

if (!function_exists('replaceSource')) {
    function replaceSource($text){
        if(!$text) return $text;
        return str_ireplace(['digikey','digi-key','digi key'],'Simplytronix',$text);
    }
}

$mpn = s($product['ManufacturerPartNumber'] ?? $product['MouserPartNumber'] ?? $product['name'] ?? '');
$manufacturer = s($product['Manufacturer'] ?? '');
$description = !empty($cached->description) ? replaceSource(s($cached->description)) : ($description ?? '');
$category = s(is_array($product['Category'] ?? null)
    ? ($product['Category']['Name'] ?? '')
    : ($product['Category'] ?? '')
);

$rohs = s($product['ROHSStatus'] ?? '');
$lifecycle = s($product['LifecycleStatus'] ?? '');
$leadTime = s($product['LeadTime'] ?? '');

$image = $cached->image
    ?? $product['ImagePath']
    ?? $product['PhotoUrl']
    ?? null;

$attrs = collect($product['ProductAttributes'] ?? []);
$packaging = s($attrs->firstWhere('AttributeName','Packaging')['AttributeValue'] ?? '');
$stdPack   = s($attrs->firstWhere('AttributeName','Standard Pack Qty')['AttributeValue'] ?? '');

$alternate   = s($product['AlternatePackagings'][0]['APMfrPN'] ?? '');
$replacement = s($product['SuggestedReplacement'] ?? '');

$datasheet = $product['DataSheetUrl'] ?? $product['datasheet'] ?? null;
$stock = (int)($quantity ?? 0);

$topSpecs = array_slice($specs ?? [], 0, 2);

$specText = '';
foreach($topSpecs as $s){
    if(!empty($s['value'])){
        $specText .= $s['value'].' ';
    }
}

$intentText = $stock > 0 ? 'In Stock' : 'Available';
$priceText = 'Contact for Pricing';

@endphp


@section('seo')

<title>{{ $seoTitle }}</title>

<meta name="description" content="{{ $seoDesc }}">

<meta name="robots" content="{{ $robotsTag ?? 'index, follow' }}, max-snippet:-1, max-image-preview:large, max-video-preview:-1">

@php
$canonicalManufacturer = \Illuminate\Support\Str::slug($manufacturer);

$canonicalPart = rawurlencode(
    str_replace(
        ['/','#'],
        ['__','--'],
        strtoupper($mpn)
    )
);
@endphp

<link rel="canonical"
href="{{ url('product/'.$canonicalManufacturer.'/'.$canonicalPart) }}">

<meta name="keywords" content="{{ $mpn }}, {{ $manufacturer }}, {{ $category }}, electronic components, datasheet, buy {{ $mpn }}, {{ $mpn }} price">

<meta property="og:type" content="product">
<meta property="og:title" content="{{ $seoTitle }}">
<meta property="og:description" content="{{ $seoDesc }}">
<meta property="og:url"
content="{{ url('product/'.$canonicalManufacturer.'/'.$canonicalPart) }}">
<meta property="og:image" content="{{ $image }}">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seoTitle }}">
<meta name="twitter:description" content="{{ $seoDesc }}">

@endsection


@section('Schema')
@php
$ldProduct = [
    '@context' => 'https://schema.org/',
    '@type' => 'Product',
    '@id' => url()->current() . '#product',
    'name' => trim($mpn . ' ' . $manufacturer),
    'image' => [$image],
    'description' => strip_tags($seoDesc),
    'sku' => $mpn,
    'mpn' => $mpn,
    'brand' => [
        '@type' => 'Brand',
        'name' => $manufacturer,
    ],
    'category' => $category,
    'url' => url()->current(),
];

if (!empty($specs)) {
    $ldProduct['additionalProperty'] = collect(array_slice($specs, 0, 8))
        ->map(fn($spec) => [
            '@type' => 'PropertyValue',
            'name'  => $spec['name'],
            'value' => $spec['value'],
        ])
        ->values()
        ->all();
}



$ldBreadcrumb = [
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => $category, 'item' => url('/available-stock?manufacturer=' . urlencode($manufacturer))],
        ['@type' => 'ListItem', 'position' => 3, 'name' => $mpn, 'item' => url()->current()],
    ],
];

if ($datasheet) {
    $ldDatasheet = [
        '@context' => 'https://schema.org',
        '@type' => 'TechArticle',
        'name' => $mpn . ' Datasheet',
        'url' => $datasheet,
        'about' => [
            '@type' => 'Product',
            'name' => $mpn,
            'brand' => ['@type' => 'Brand', 'name' => $manufacturer],
        ],
    ];
}

$jsonFlags = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
@endphp

<script type="application/ld+json">
{!! json_encode($ldProduct, $jsonFlags) !!}
</script>

<script type="application/ld+json">
{!! json_encode($ldBreadcrumb, $jsonFlags) !!}
</script>

@if($datasheet)
<script type="application/ld+json">
{!! json_encode($ldDatasheet, $jsonFlags) !!}
</script>
@endif
@endsection

<style>
/* ── Design tokens ─────────────────────────────────────────── */
:root {
    --cream:        #f8f5ef;
    --cream-mid:    #f2ede4;
    --cream-card:   #faf9f6;
    --border:       #e4ded4;
    --border-light: #ede9e1;
    --orange:       #d35400;
    --orange-light: #f59e0b;
    --orange-bg:    #fff7ed;
    --orange-border:#fed7aa;
    --text-main:    #1a1714;
    --text-muted:   #6b6560;
    --text-light:   #9c9590;
    --green:        #16a34a;
    --green-bg:     #ecfdf5;
    --green-border: #bbf7d0;
    --red:          #dc2626;
    --red-bg:       #fef2f2;
    --red-border:   #fecaca;
    --radius-sm:    6px;
    --radius-md:    10px;
    --radius-lg:    14px;
    --shadow-sm:    0 1px 3px rgba(0,0,0,.06), 0 1px 2px rgba(0,0,0,.04);
    --shadow-md:    0 4px 12px rgba(0,0,0,.08), 0 2px 4px rgba(0,0,0,.04);
    --shadow-card:  0 2px 8px rgba(180,120,60,.07);
}

/* ── Base ──────────────────────────────────────────────────── */
* { box-sizing: border-box; }

.pp-wrap {
    background: var(--cream);
    padding: 28px 0 40px;
    min-height: 100vh;
}

.pp-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

/* ── Breadcrumb ────────────────────────────────────────────── */
.pp-breadcrumb {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12.5px;
    color: var(--text-muted);
    margin-bottom: 20px;
    flex-wrap: wrap;
}
.pp-breadcrumb a {
    color: var(--orange);
    text-decoration: none;
    transition: opacity .15s;
}
.pp-breadcrumb a:hover { opacity: .75; }
.pp-breadcrumb .sep { color: var(--text-light); }

/* ── Layout grid ───────────────────────────────────────────── */
.pp-grid {
    display: grid;
    grid-template-columns: 1fr 340px;
    gap: 20px;
    align-items: start;
}
@media (max-width: 900px) {
    .pp-grid { grid-template-columns: 1fr; }
}

/* ── Cards ─────────────────────────────────────────────────── */
.pp-card {
    background: var(--cream-card);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-card);
    margin-bottom: 16px;
    overflow: hidden;
}

.pp-card-header {
    padding: 11px 18px;
    font-weight: 700;
    font-size: 13px;
    letter-spacing: .3px;
    color: var(--text-main);
    border-bottom: 1px solid var(--border-light);
    background: linear-gradient(to bottom, #fdfcfa, var(--cream-card));
    display: flex;
    align-items: center;
    gap: 8px;
}

.pp-card-header .icon {
    width: 18px;
    height: 18px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: var(--orange-bg);
    border-radius: 4px;
    color: var(--orange);
    font-size: 11px;
    flex-shrink: 0;
}

/* ── Hero card ─────────────────────────────────────────────── */
.pp-hero {
    display: flex;
    gap: 20px;
    padding: 20px;
}

.pp-hero-img {
    width: 180px;
    min-width: 180px;
    height: 160px;
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: #fff;
    box-shadow: var(--shadow-sm);
    overflow: hidden;
}

.pp-hero-img img {
    max-width: 160px;
    max-height: 130px;
    object-fit: contain;
    display: block;
}

.pp-hero-img .img-note {
    font-size: 11px;
    color: var(--text-light);
    margin-top: 6px;
    text-align: center;
    padding: 0 8px;
    line-height: 1.3;
}

.pp-hero-info { flex: 1; min-width: 0; }

.pp-hero-info h1 {
    font-size: 22px;
    font-weight: 800;
    color: var(--text-main);
    margin: 0 0 10px;
    letter-spacing: -.2px;
    line-height: 1.2;
}

.pp-badges {
    display: flex;
    gap: 7px;
    flex-wrap: wrap;
    margin-bottom: 12px;
}

.pp-badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    border: 1px solid;
    text-decoration: none;
    display: inline-block;
    transition: opacity .15s;
}
.pp-badge:hover { opacity: .8; }

.pp-badge-mfr {
    background: var(--cream-mid);
    border-color: var(--border);
    color: var(--text-main);
}

.pp-badge-active {
    background: var(--green-bg);
    border-color: var(--green-border);
    color: var(--green);
}

.pp-badge-inactive {
    background: var(--red-bg);
    border-color: var(--red-border);
    color: var(--red);
}

.pp-hero-desc {
    font-size: 13.5px;
    color: var(--text-muted);
    line-height: 1.6;
}

@media (max-width: 600px) {
    .pp-hero { flex-direction: column; }
    .pp-hero-img { width: 100%; min-width: unset; height: 140px; }
}

/* ── Tables ────────────────────────────────────────────────── */
.pp-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13.5px;
}

.pp-table td {
    padding: 10px 16px;
    border-bottom: 1px solid var(--border-light);
    vertical-align: top;
    line-height: 1.5;
}

.pp-table tr:last-child td { border-bottom: none; }

.pp-table td:first-child {
    width: 40%;
    color: var(--text-muted);
    font-weight: 500;
    background: linear-gradient(to right, var(--cream-mid), transparent);
}

.pp-table td:last-child { color: var(--text-main); }

.pp-table tr:hover td { background: rgba(211,84,0,.03); }

/* ── Sidebar quote card ────────────────────────────────────── */
.pp-quote-card {
    background: var(--orange-bg);
    border: 1.5px solid var(--orange-border);
    border-radius: var(--radius-lg);
    padding: 20px 18px 18px;
    margin-bottom: 16px;
    position: sticky;
    top: 20px;
    box-shadow: 0 4px 16px rgba(211,84,0,.1);
}

.pp-stock-block {
    text-align: center;
    margin-bottom: 16px;
    padding-bottom: 16px;
    border-bottom: 1px solid var(--orange-border);
}

.pp-stock-number {
    font-size: 36px;
    font-weight: 800;
    color: var(--orange);
    line-height: 1;
    letter-spacing: -1px;
}

.pp-stock-label {
    font-size: 12px;
    color: var(--text-muted);
    margin-top: 4px;
    font-weight: 500;
    letter-spacing: .5px;
    text-transform: uppercase;
}

.pp-stock-dot {
    display: inline-block;
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: var(--green);
    margin-right: 5px;
    box-shadow: 0 0 0 2px rgba(22,163,74,.2);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { box-shadow: 0 0 0 2px rgba(22,163,74,.2); }
    50%       { box-shadow: 0 0 0 5px rgba(22,163,74,.08); }
}

.pp-packaging-tag {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: #fff;
    border: 1px solid var(--orange-border);
    border-radius: 20px;
    padding: 4px 12px;
    font-size: 12px;
    color: var(--text-muted);
    font-weight: 500;
    margin-top: 8px;
}

.pp-quote-btn {
    display: block;
    background: linear-gradient(135deg, var(--orange-light) 0%, #e8920a 100%);
    color: #fff;
    text-align: center;
    padding: 12px;
    border-radius: var(--radius-md);
    font-weight: 700;
    text-decoration: none;
    font-size: 14px;
    letter-spacing: .2px;
    box-shadow: 0 3px 10px rgba(245,158,11,.35);
    transition: transform .15s, box-shadow .15s;
    margin-bottom: 14px;
}

.pp-quote-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 5px 16px rgba(245,158,11,.45);
    color: #fff;
    text-decoration: none;
}

.pp-trust-list {
    list-style: none;
    padding: 0;
    margin: 0 0 14px;
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.pp-trust-list li {
    font-size: 12.5px;
    color: var(--text-muted);
    display: flex;
    align-items: center;
    gap: 7px;
}

.pp-trust-list li::before {
    content: '✓';
    color: var(--green);
    font-weight: 700;
    font-size: 13px;
    flex-shrink: 0;
}

.pp-alert-toggle {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 12.5px;
    color: var(--orange);
    font-weight: 600;
    cursor: pointer;
    background: none;
    border: none;
    padding: 0;
    margin-bottom: 12px;
}
.pp-alert-toggle:hover { text-decoration: underline; }

.pp-alert-form {
    display: none;
    gap: 6px;
    margin-bottom: 12px;
}
.pp-alert-form.is-open { display: flex; }

.pp-alert-input {
    flex: 1;
    padding: 8px 10px;
    border: 1px solid var(--orange-border);
    border-radius: var(--radius-md);
    font-size: 12.5px;
}

.pp-alert-submit {
    background: var(--orange);
    color: #fff;
    border: none;
    border-radius: var(--radius-md);
    padding: 8px 12px;
    font-size: 12.5px;
    font-weight: 600;
    cursor: pointer;
    white-space: nowrap;
}

.pp-alert-msg {
    font-size: 12px;
    margin-bottom: 12px;
    display: none;
    color: var(--text-muted);
}
.pp-alert-msg.is-visible { display: block; }

.pp-report-link {
    font-size: 12.5px;
    color: var(--text-muted);
    line-height: 1.5;
    padding-top: 12px;
    border-top: 1px solid var(--orange-border);
}

.pp-report-link a {
    color: var(--orange);
    text-decoration: underline;
    font-weight: 600;
}

/* ── Key specs sidebar card ────────────────────────────────── */
.pp-key-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}

.pp-key-table td {
    padding: 9px 14px;
    border-bottom: 1px solid var(--border-light);
    vertical-align: top;
    line-height: 1.4;
}

.pp-key-table tr:last-child td { border-bottom: none; }

.pp-key-table td:first-child {
    width: 45%;
    color: var(--text-muted);
    font-weight: 500;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: .3px;
}

.pp-key-table td:last-child {
    color: var(--text-main);
    font-weight: 600;
    font-size: 13px;
}

/* ── Datasheet button ──────────────────────────────────────── */
.pp-datasheet-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    border: 1.5px solid var(--orange);
    text-align: center;
    padding: 9px;
    border-radius: var(--radius-sm);
    color: var(--orange);
    font-weight: 700;
    text-decoration: none;
    background: var(--cream-card);
    font-size: 13.5px;
    transition: background .15s, color .15s;
    margin: 14px 14px 14px;
}

.pp-datasheet-btn:hover {
    background: var(--orange);
    color: #fff;
    text-decoration: none;
}

/* ── Compliance table ──────────────────────────────────────── */
.pp-compliance-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 12.5px;
}

.pp-compliance-table th {
    padding: 8px 14px;
    border-bottom: 1px solid var(--border);
    text-align: left;
    font-size: 11.5px;
    text-transform: uppercase;
    letter-spacing: .4px;
    color: var(--text-muted);
    background: var(--cream-mid);
}

.pp-compliance-table td {
    padding: 8px 14px;
    border-bottom: 1px solid var(--border-light);
    color: var(--text-main);
    line-height: 1.4;
}

.pp-compliance-table tr:last-child td { border-bottom: none; }

/* ── Product overview ──────────────────────────────────────── */
.pp-overview-body {
    padding: 16px 18px;
    font-size: 13.5px;
    color: #444;
    line-height: 1.7;
}

.pp-overview-body p { margin: 0 0 12px; }
.pp-overview-body p:last-child { margin-bottom: 0; }

#moreContent {
    max-height: 0;
    overflow: hidden;
    transition: max-height .3s ease;
}

.pp-read-more {
    color: var(--orange);
    font-weight: 600;
    text-decoration: none;
    font-size: 13px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    margin-top: 4px;
}

.pp-read-more:hover { text-decoration: underline; }

/* ── Alternate parts links ─────────────────────────────────── */
.pp-part-link {
    color: var(--orange);
    font-weight: 600;
    text-decoration: none;
}
.pp-part-link:hover { text-decoration: underline; }

/* ── Similar products ──────────────────────────────────────── */
.pp-similar-section {
    background: var(--cream);
    border-top: 1px solid var(--border);
    margin-top: 28px;
    padding: 28px 0 36px;
}

.pp-similar-title {
    font-weight: 700;
    margin-bottom: 16px;
    font-size: 15px;
    color: var(--text-main);
    letter-spacing: -.1px;
}

.pp-similar-scroll {
    display: flex;
    gap: 14px;
    overflow-x: auto;
    padding-bottom: 8px;
    scrollbar-width: thin;
    scrollbar-color: var(--border) transparent;
}

.pp-similar-scroll::-webkit-scrollbar { height: 4px; }
.pp-similar-scroll::-webkit-scrollbar-track { background: transparent; }
.pp-similar-scroll::-webkit-scrollbar-thumb { background: var(--border); border-radius: 4px; }

.pp-similar-card {
    min-width: 175px;
    max-width: 175px;
    background: var(--cream-card);
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    padding: 12px;
    flex-shrink: 0;
    transition: box-shadow .2s, transform .2s;
    cursor: pointer;
}

.pp-similar-card:hover {
    box-shadow: var(--shadow-md);
    transform: translateY(-2px);
}

.pp-similar-img {
    width: 100%;
    height: 95px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #fff;
    border-radius: var(--radius-sm);
    margin-bottom: 10px;
    border: 1px solid var(--border-light);
}

.pp-similar-img img {
    max-width: 100%;
    max-height: 80px;
    object-fit: contain;
}

.pp-similar-mpn {
    color: var(--orange);
    font-weight: 700;
    font-size: 13px;
    display: block;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-bottom: 3px;
    text-decoration: none;
}

.pp-similar-mfr {
    font-size: 11.5px;
    color: var(--text-muted);
    margin-bottom: 5px;
    display: block;
    text-decoration: none;
}

.pp-similar-desc {
    font-size: 11.5px;
    color: var(--text-light);
    line-height: 1.4;
    height: 32px;
    overflow: hidden;
}

/* ── Divider utility ───────────────────────────────────────── */
.pp-divider {
    height: 1px;
    background: var(--border-light);
    margin: 0;
}
</style>

<div class="pp-wrap">
<div class="pp-container">

{{-- Breadcrumb --}}
<nav class="pp-breadcrumb" aria-label="Breadcrumb">
    <a href="{{ url('/') }}">Home</a>
    <span class="sep">›</span>
    <a href="{{ url('/available-stock?manufacturer='.urlencode($manufacturer)) }}">{{ $category ?: 'Products' }}</a>
    <span class="sep">›</span>
    <span>{{ $mpn }}</span>
</nav>

<div class="pp-grid">

{{-- ════════════════════════════════════════
     LEFT COLUMN
     ════════════════════════════════════════ --}}
<div>

    {{-- Hero card --}}
    <div class="pp-card">
        <div class="pp-hero">
            <div class="pp-hero-img">
                @if($image)
                    <img src="{{ str_starts_with($image,'http') ? $image : asset($image) }}"
                         alt="{{ $mpn }} {{ $manufacturer }}">
                @else
                    <span style="color:#999;font-size:12px;">No Image</span>
                @endif
                <div class="img-note">*For representation only.</div>
            </div>

            <div class="pp-hero-info">
                <h1>{{ $mpn }}</h1>

                @php
                $rawArr = is_array($cached->digikey_raw) ? $cached->digikey_raw : json_decode($cached->digikey_raw, true);
                $digikeySeoDesc = isset($rawArr['Product']['Category']['SeoDescription'])
                    ? replaceSource($rawArr['Product']['Category']['SeoDescription'])
                    : (isset($rawArr['Product']['Description']['DetailedDescription'])
                        ? replaceSource($rawArr['Product']['Description']['DetailedDescription'])
                        : "");
                $status = $rawArr['Product']['ProductStatus']['Status'] ?? null;
                @endphp

                <div class="pp-badges">
                    <a href="{{ url('/available-stock') }}?manufacturer={{ urlencode($manufacturer) }}"
                       class="pp-badge pp-badge-mfr">
                        {{ $manufacturer }}
                    </a>

                    @if($status)
                    <span class="pp-badge {{ strtolower($status) == 'active' ? 'pp-badge-active' : 'pp-badge-inactive' }}">
                        {{ $status }}
                    </span>
                    @endif
                </div>

                <div class="pp-hero-desc">{{ $description }}</div>
            </div>
        </div>
    </div>

    {{-- Technical Specifications --}}
    @if(!empty($specs))
    <div class="pp-card">
        <div class="pp-card-header">
            <span class="icon">⚙</span>
            Technical Specifications
        </div>
        <table class="pp-table">
            <tbody>
            @foreach($specs as $spec)
            <tr>
                <td>{{ replaceSource($spec['name']) }}</td>
                <td>{{ replaceSource($spec['value']) }}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Product Attributes --}}
    <div class="pp-card">
        <div class="pp-card-header">
            <span class="icon">📦</span>
            Product Attributes
        </div>
        <table class="pp-table">
            <tbody>
            @if($packaging !== 'NA')
            <tr>
                <td>Packaging</td>
                <td>{{ replaceSource($packaging) }}</td>
            </tr>
            @endif
            @if($stdPack !== 'NA')
            <tr>
                <td>Standard Pack Qty</td>
                <td>{{ replaceSource($stdPack) }}</td>
            </tr>
            @endif
            </tbody>
        </table>
    </div>

    {{-- Alternate / Replacement Parts --}}
    <div class="pp-card">
        <div class="pp-card-header">
            <span class="icon">🔁</span>
            Alternate / Replacement Parts
        </div>
        @php $slug = \Illuminate\Support\Str::slug($manufacturer); @endphp
        <table class="pp-table">
            <tbody>
            <tr>
                <td>Alternate Packaging</td>
                <td>
                    @if($alternate !== 'NA')
                        <a class="pp-part-link"
                           href="{{ url('product/'.$slug.'/'.rawurlencode(str_replace(['/','#'],['__','--'],$alternate))) }}">
                            {{ $alternate }}
                        </a>
                    @else
                        <span style="color:var(--text-light);">NA</span>
                    @endif
                </td>
            </tr>
            <tr>
                <td>Suggested Replacement</td>
                <td>
                    @if($replacement !== 'NA')
                        <a class="pp-part-link"
                           href="{{ url('product/'.$slug.'/'.rawurlencode(str_replace(['/','#'],['__','--'],$replacement))) }}">
                            {{ $replacement }}
                        </a>
                    @else
                        <span style="color:var(--text-light);">NA</span>
                    @endif
                </td>
            </tr>
            </tbody>
        </table>
    </div>

</div>{{-- /left col --}}

{{-- ════════════════════════════════════════
     RIGHT COLUMN
     ════════════════════════════════════════ --}}
<div>

    {{-- Quote / Stock card --}}
    <div class="pp-quote-card">
        <div class="pp-stock-block">
            {{-- FIX: labelled as "Units In Stock" so Google won't scrape as price --}}
            <div class="pp-stock-number" aria-label="{{ number_format($stock) }} units in stock">
                {{ number_format($stock) }}
            </div>
            <div class="pp-stock-label">
                <span class="pp-stock-dot" aria-hidden="true"></span>
                Units In Stock
            </div>

            @if($packaging !== 'NA')
            <div>
                <span class="pp-packaging-tag">
                    📦 {{ replaceSource($packaging) }}
                </span>
            </div>
            @endif
        </div>

        <a href="{{ url('/get-a-quote') }}?name={{ urlencode($mpn) }}"
           class="pp-quote-btn">
            Request a Quote →
        </a>

        <ul class="pp-trust-list">
            <li>24-hour response</li>
            <li>Global shipping</li>
            <li>Genuine parts guaranteed</li>
        </ul>

        <button type="button" class="pp-alert-toggle" id="pp-alert-toggle-btn">
            🔔 Get notified if price or stock changes
        </button>
        <form id="pp-alert-form" class="pp-alert-form">
            <input type="hidden" name="mpn" value="{{ $mpn }}">
            <input type="email" name="email" class="pp-alert-input" placeholder="Your email" required>
            <button type="submit" class="pp-alert-submit">Notify me</button>
        </form>
        <div class="pp-alert-msg" id="pp-alert-msg"></div>

        <div class="pp-report-link">
            Test report available upon request &mdash;
            <a href="{{ url('/quality-assurance') }}">view sample report</a>
        </div>
    </div>

    {{-- Key Specifications --}}
    <div class="pp-card">
        <div class="pp-card-header">
            <span class="icon">🔑</span>
            Key Specifications
        </div>
        <table class="pp-key-table">
            <tbody>
            <tr>
                <td>Category</td>
                <td>{{ $category }}</td>
            </tr>
            <tr>
                <td>RoHS</td>
                <td>{{ $rohs }}</td>
            </tr>
            <tr>
                <td>Lifecycle</td>
                <td>{{ $lifecycle }}</td>
            </tr>
            <tr>
                <td>Lead Time</td>
                <td>{{ $leadTime !== 'NA' ? $leadTime . ' (from factory)' : 'NA' }}</td>
            </tr>
            </tbody>
        </table>
    </div>

    {{-- Documentation --}}
    <div class="pp-card">
        <div class="pp-card-header">
            <span class="icon">📄</span>
            Documentation
        </div>
        @if($datasheet)
            <a href="{{ $datasheet }}" target="_blank" class="pp-datasheet-btn">
                ⬇ Download Datasheet
            </a>
        @else
            <div style="padding:14px 16px;font-size:12.5px;color:var(--text-light);">
                Datasheet not available
            </div>
        @endif
    </div>

    {{-- Compliance --}}
    @if(!empty($product['ProductCompliance']))
    <div class="pp-card">
        <div class="pp-card-header">
            <span class="icon">🌐</span>
            Compliance &amp; Export
        </div>
        <table class="pp-compliance-table">
            <thead>
                <tr>
                    <th>Country / Standard</th>
                    <th>Code</th>
                </tr>
            </thead>
            <tbody>
            @foreach($product['ProductCompliance'] ?? [] as $c)
            <tr>
                <td>{{ replaceSource(s($c['ComplianceName'])) }}</td>
                <td>{{ replaceSource(s($c['ComplianceValue'])) }}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Product Overview --}}
    @if(!empty($dynamicOverview))
    <div class="pp-card">
        <div class="pp-card-header">
            <span class="icon">ℹ</span>
            Product Overview
        </div>
        <div class="pp-overview-body">
            <p>{{ $dynamicOverview }}</p>
        </div>
    </div>
    @endif

</div>{{-- /right col --}}

</div>{{-- /grid --}}

</div>{{-- /container --}}
</div>{{-- /wrap --}}

{{-- Similar Products --}}
@if(!empty($similarProducts))
<div class="pp-similar-section">
<div class="pp-container">

    <div class="pp-similar-title">Similar Products</div>

    <div class="pp-similar-scroll">
    @foreach($similarProducts as $item)
    @php
    $slug    = \Illuminate\Support\Str::slug($item->manufacturer);
    $safePart = rawurlencode(str_replace(['/','#'],['__','--'],$item->product_key));
    @endphp

    <div class="pp-similar-card">
        <div class="pp-similar-img">
            @if(!empty($item->image))
                <img src="{{ str_starts_with($item->image,'http') ? $item->image : asset($item->image) }}"
                     alt="{{ $item->product_key }}">
            @else
                <span style="font-size:11px;color:#bbb;">No Image</span>
            @endif
        </div>

        <a href="{{ url('product/'.$slug.'/'.$safePart) }}"
           class="pp-similar-mpn">
            {{ $item->product_key }}
        </a>

        <a href="{{ url('/available-stock') }}?manufacturer={{ urlencode($item->manufacturer) }}"
           class="pp-similar-mfr">
            {{ $item->manufacturer }}
        </a>

        <div class="pp-similar-desc">
            {{ \Illuminate\Support\Str::limit(replaceSource($item->description ?? ''), 60) }}
        </div>
    </div>
    @endforeach
    </div>

</div>
</div>
@endif


<script>
document.addEventListener('DOMContentLoaded', function () {
    var toggleBtn = document.getElementById('pp-alert-toggle-btn');
    var form = document.getElementById('pp-alert-form');
    var msgEl = document.getElementById('pp-alert-msg');
    if (!toggleBtn || !form) return;

    toggleBtn.addEventListener('click', function () {
        form.classList.add('is-open');
        toggleBtn.style.display = 'none';
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        var email = form.email.value;
        var mpn = form.mpn.value;
        fetch("{{ route('stock_alert_signup') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ email: email, mpn: mpn })
        })
        .then(function (r) { return r.json(); })
        .then(function () {
            form.style.display = 'none';
            msgEl.textContent = "Got it — we will email you if anything changes.";
            msgEl.classList.add('is-visible');
        })
        .catch(function () {
            msgEl.textContent = "Something went wrong, please try again.";
            msgEl.classList.add('is-visible');
        });
    });
});
</script>

@stop