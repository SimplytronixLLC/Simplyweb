@extends('includes.front')

@php
    $page = \App\Models\Page::content('industries');
@endphp

@section('seo')
<title>Industries We Serve | Electronics Component Sourcing – Simplytronix</title>
<meta name="description" content="{{ $page->get('meta_description', 'Simplytronix supports OEMs and EMS providers across industrial, automotive, medical, telecom, data center, power, aerospace, and test & measurement industries with reliable electronic component sourcing.') }}">
@stop

@section('content')

<style>
/* ── reset ── */
*, *::before, *::after { box-sizing: border-box; }

/* ── hero ── */
.ind-hero {
    background: rgba(0,0,0,0.5);
    padding: 60px 32px 48px;
    text-align: center;
    position: relative;
    overflow: hidden;
}
.ind-hero canvas {
    position: absolute;
    inset: 0;
    pointer-events: none;
}
.ind-hero-inner { position: relative; z-index: 2; }
.ind-eyebrow {
    display: inline-block;
    background: rgba(255,255,255,0.08);
    border: 1px solid rgba(255,255,255,0.12);
    color: #94a3b8;
    font-size: 11px;
    padding: 4px 14px;
    border-radius: 99px;
    margin-bottom: 16px;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}
.ind-hero h1 {
    font-size: 36px;
    font-weight: 700;
    color: #fff;
    margin-bottom: 12px;
    line-height: 1.2;
}
.ind-hero p {
    font-size: 16px;
    color: #94a3b8;
    max-width: 520px;
    margin: 0 auto 32px;
    line-height: 1.7;
}
.ind-hero-stats {
    display: flex;
    justify-content: center;
    gap: 48px;
}
.ind-hero-stat .n {
    font-size: 28px;
    font-weight: 700;
    color: #fff;
}
.ind-hero-stat .l {
    font-size: 12px;
    color: #fff;
    margin-top: 3px;
}

/* ── intro ── */
.ind-intro {
    text-align: center;
    max-width: 640px;
    margin: 48px auto 0;
    font-size: 15px;
    color: #4b5563;
    line-height: 1.8;
    padding: 0 16px;
}

/* ── filter bar ── */
.ind-filter-bar {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    justify-content: center;
    padding: 36px 16px 24px;
}
.ind-filter-pill {
    padding: 7px 18px;
    border-radius: 99px;
    border: 1px solid #e2e8f0;
    background: #fff;
    color: #64748b;
    font-size: 13px;
    cursor: pointer;
    transition: all 0.15s;
    font-family: inherit;
}
.ind-filter-pill:hover {
    border-color: #93c5fd;
    color: #1e40af;
}
.ind-filter-pill.active {
    background: #0f172a;
    color: #fff;
    border-color: #0f172a;
}

/* ── grid ── */
.ind-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
    padding: 0 0 48px;
}
.ind-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    overflow: hidden;
    cursor: pointer;
    transition: transform 0.22s ease, border-color 0.2s, box-shadow 0.22s ease;
    position: relative;
}
.ind-card:hover {
    transform: translateY(-5px);
    border-color: #93c5fd;
    box-shadow: 0 8px 24px rgba(59,130,246,0.1);
}
.ind-card.ind-hidden { display: none; }
.ind-card.ind-fade-in { animation: indFadeUp 0.3s ease both; }
@keyframes indFadeUp {
    from { opacity: 0; transform: translateY(14px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* card image */
.ind-card-img-wrap {
    position: relative;
    overflow: hidden;
    height: 200px;
}
.ind-card-img-wrap img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform 0.45s ease;
    background: #1e293b;
}
.ind-card:hover .ind-card-img-wrap img {
    transform: scale(1.07);
}
.ind-card-overlay {
    position: absolute;
    inset: 0;
    background: #0f172a;
    opacity: 0;
    transition: opacity 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
}
.ind-card:hover .ind-card-overlay { opacity: 0.55; }
.ind-card-overlay-label {
    color: #fff;
    font-size: 13px;
    font-weight: 600;
    border: 1px solid rgba(255,255,255,0.45);
    padding: 7px 18px;
    border-radius: 99px;
    opacity: 0;
    transform: translateY(4px);
    transition: opacity 0.2s 0.08s, transform 0.2s 0.08s;
}
.ind-card:hover .ind-card-overlay-label {
    opacity: 1;
    transform: translateY(0);
}
.ind-card-tag {
    position: absolute;
    top: 12px;
    left: 12px;
    background: rgba(15,23,42,0.72);
    color: #e2e8f0;
    font-size: 10px;
    padding: 3px 9px;
    border-radius: 99px;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
}

/* card body */
.ind-card-body { padding: 16px 18px 18px; }
.ind-card-body h3 {
    font-size: 15px;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 6px;
    line-height: 1.35;
}
.ind-card-body p {
    font-size: 13px;
    color: #64748b;
    line-height: 1.65;
    margin-bottom: 12px;
}
.ind-app-tags { display: flex; flex-wrap: wrap; gap: 5px; }
.ind-app-tag {
    font-size: 11px;
    background: #f1f5f9;
    color: #475569;
    padding: 3px 8px;
    border-radius: 5px;
    border: 1px solid #e2e8f0;
}

/* ── modal ── */
.ind-modal-bg {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.55);
    z-index: 1000;
    align-items: center;
    justify-content: center;
    padding: 24px 16px;
    backdrop-filter: blur(2px);
    -webkit-backdrop-filter: blur(2px);
}
.ind-modal-bg.ind-open { display: flex; }
.ind-modal {
    background: #fff;
    border-radius: 16px;
    width: 100%;
    max-width: 560px;
    overflow: hidden;
    animation: indModalIn 0.2s ease;
    border: 1px solid #e5e7eb;
    max-height: 90vh;
    overflow-y: auto;
}
@keyframes indModalIn {
    from { opacity: 0; transform: scale(0.95) translateY(8px); }
    to   { opacity: 1; transform: scale(1) translateY(0); }
}
.ind-modal img {
    width: 100%;
    height: 220px;
    object-fit: cover;
    display: block;
    background: #1e293b;
}
.ind-modal-body { padding: 22px 26px; }
.ind-modal-tag {
    display: inline-block;
    font-size: 10px;
    background: #f1f5f9;
    color: #64748b;
    padding: 3px 10px;
    border-radius: 99px;
    border: 1px solid #e2e8f0;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    margin-bottom: 10px;
}
.ind-modal-body h2 {
    font-size: 20px;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 8px;
}
.ind-modal-body .ind-modal-desc {
    font-size: 14px;
    color: #475569;
    line-height: 1.7;
    margin-bottom: 20px;
}
.ind-modal-section-label {
    font-size: 11px;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    margin-bottom: 10px;
}
.ind-modal-apps {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
    margin-bottom: 18px;
}
.ind-modal-app {
    display: flex;
    align-items: center;
    gap: 7px;
    font-size: 13px;
    color: #374151;
}
.ind-modal-app i { font-size: 15px; color: #3b82f6; flex-shrink: 0; }
.ind-modal-note {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 13px 16px;
    font-size: 13px;
    color: #475569;
    line-height: 1.65;
}
.ind-modal-note strong { color: #1e293b; }
.ind-modal-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 14px 26px;
    border-top: 1px solid #f1f5f9;
    background: #fafafa;
}
.ind-modal-close {
    font-size: 13px;
    color: #64748b;
    background: #fff;
    border: 1px solid #e2e8f0;
    padding: 7px 16px;
    border-radius: 8px;
    cursor: pointer;
    font-family: inherit;
    transition: background 0.15s;
}
.ind-modal-close:hover { background: #f1f5f9; }
.ind-modal-cta {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: #fff;
    background: #0f172a;
    border: none;
    padding: 8px 18px;
    border-radius: 8px;
    cursor: pointer;
    font-family: inherit;
    font-weight: 600;
    transition: background 0.15s;
    text-decoration: none;
}
.ind-modal-cta:hover { background: #1e40af; color: #fff; }

/* ── nav arrows on modal ── */
.ind-modal-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 36px;
    height: 36px;
    background: rgba(255,255,255,0.9);
    border: 1px solid #e2e8f0;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 18px;
    color: #1e293b;
    transition: background 0.15s;
    z-index: 10;
}
.ind-modal-nav:hover { background: #fff; }
.ind-modal-nav.prev { left: -48px; }
.ind-modal-nav.next { right: -48px; }
.ind-modal-wrap { position: relative; }

/* ── value strip ── */
.ind-value {
    background: #f8fafc;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    padding: 28px 32px;
    margin-bottom: 28px;
}
.ind-value h2 {
    font-size: 18px;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 18px;
}
.ind-value-items {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 12px;
}
.ind-value-item {
    text-align: center;
    padding: 16px 12px;
    border-radius: 10px;
    border: 1px solid #e5e7eb;
    background: #fff;
    transition: border-color 0.15s, transform 0.15s, box-shadow 0.15s;
    cursor: default;
}
.ind-value-item:hover {
    border-color: #93c5fd;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(59,130,246,0.08);
}
.ind-value-item i {
    font-size: 22px;
    color: #3b82f6;
    display: block;
    margin-bottom: 8px;
}
.ind-value-item span {
    font-size: 12px;
    color: #475569;
    line-height: 1.4;
    display: block;
    font-weight: 600;
}

/* ── CTA ── */
.ind-cta {
    background: #0f172a;
    border-radius: 14px;
    padding: 48px 32px;
    text-align: center;
    margin-bottom: 48px;
}
.ind-cta h2 {
    font-size: 24px;
    font-weight: 700;
    color: #fff;
    margin-bottom: 10px;
}
.ind-cta p {
    font-size: 15px;
    color: #94a3b8;
    line-height: 1.7;
    margin-bottom: 26px;
    max-width: 420px;
    margin-left: auto;
    margin-right: auto;
}
.ind-cta-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #fff;
    color: #0f172a;
    font-size: 14px;
    font-weight: 700;
    padding: 12px 28px;
    border-radius: 99px;
    border: none;
    cursor: pointer;
    font-family: inherit;
    text-decoration: none;
    transition: transform 0.15s, box-shadow 0.15s;
}
.ind-cta-btn:hover {
    transform: scale(1.04);
    box-shadow: 0 4px 16px rgba(255,255,255,0.2);
    color: #0f172a;
}

/* ── responsive ── */
@media (max-width: 640px) {
    .ind-hero h1 { font-size: 26px; }
    .ind-hero-stats { gap: 24px; }
    .ind-grid { grid-template-columns: 1fr; }
    .ind-modal-nav { display: none; }
    .ind-modal-apps { grid-template-columns: 1fr; }
    .ind-value-items { grid-template-columns: 1fr 1fr; }
}
</style>

<main class="main__content_wrapper">

{{-- Hero --}}
<section class="ind-hero">
    <canvas id="indParticles"></canvas>
    <div class="ind-hero-inner">
               <h1>{{ $page->get('hero_title', 'Industries we serve') }}</h1>
        <p>{{ $page->get('hero_subtitle', 'Supporting OEMs, EMS providers, and technology-driven businesses worldwide with reliable electronic component sourcing.') }}</p>
        <div class="ind-hero-stats">
            <div class="ind-hero-stat">
                <div class="n" id="ind-count">0</div>
                <div class="l">Industries</div>
            </div>
            <div class="ind-hero-stat">
                <div class="n" id="ind-countries">0</div>
                <div class="l">Countries served</div>
            </div>
            <div class="ind-hero-stat">
                <div class="n" id="ind-parts">0</div>
                <div class="l">Parts sourced daily</div>
            </div>
        </div>
    </div>
</section>



<div class="container">

    {{-- Intro --}}
    <div class="ind-intro">
        {!! $page->get('intro_html', "<p>At <strong>Simplytronix</strong>, our experience across diverse applications lets us understand industry-specific requirements, compliance needs, and supply chain challenges — especially during shortages and constrained markets.</p><p><em>We don't just supply components. We help keep production moving.</em></p>") !!}
    </div>

    @php
        // Industry cards now come from the Pages Tool ('industries' page,
        // 'industries' repeater field) instead of being hardcoded here.
        // Each row's "apps" is stored as one application per line and
        // split into an array for display below and for the JS modal data.
        $rawIndustries = $page->get('industries', [
            ['tag'=>'Manufacturing', 'img'=>'industrial-automation.jfif',    'alt'=>'Industrial Automation',    'title'=>'Industrial & automation',       'desc'=>'Control systems, smart manufacturing, and automation equipment for factory floors worldwide.',              'apps'=>"PLCs and industrial controllers\nSensors and actuators\nPower management modules\nEmbedded control systems",    'note'=>'<strong>Why it matters:</strong> Reliability, long lifecycle components, and consistent supply are critical in industrial environments.'],
            ['tag'=>'Mobility',      'img'=>'automotive-electronics.jfif',   'alt'=>'Automotive Electronics',   'title'=>'Automotive & transportation',    'desc'=>'Components for modern vehicles, EV powertrains, and Tier 1 & 2 suppliers globally.',                         'apps'=>"Infotainment systems\nADAS and safety electronics\nPowertrain electronics\nBody control modules",                'note'=>'<strong>Our focus:</strong> Quality-conscious sourcing while navigating long lead times and obsolescence challenges.'],
            ['tag'=>'Healthcare',    'img'=>'medical-electronics.jfif',      'alt'=>'Medical Electronics',      'title'=>'Medical & healthcare',           'desc'=>'Accuracy, reliability, and compliance-grade components for life-critical medical electronics.',                 'apps'=>"Diagnostic equipment\nPatient monitoring systems\nImaging devices\nLaboratory instruments",                      'note'=>'<strong>What we prioritize:</strong> Stable sourcing, traceability, and components suitable for regulated environments.'],
            ['tag'=>'Connectivity',  'img'=>'telecom-networking.jfif',       'alt'=>'Telecommunications',       'title'=>'Telecommunications & networking', 'desc'=>'High-speed, always-on infrastructure components for telecom networks and next-gen connectivity.',             'apps'=>"Network switches and routers\nRF and wireless modules\nData transmission systems\nSignal integrity solutions",   'note'=>'<strong>Key value:</strong> Dependable component availability for fast-evolving technologies.'],
            ['tag'=>'Computing',     'img'=>'data-center-computing.jfif',    'alt'=>'Data Centers',             'title'=>'Data centers & computing',       'desc'=>'Enterprise and hyperscale data center computing hardware at any volume, on any timeline.',                    'apps'=>"Servers and storage systems\nPower supplies and cooling\nHigh-speed memory and processors\nNetworking ASICs",   'note'=>'<strong>Our advantage:</strong> Experience with memory, logic, and hard-to-find parts during demand spikes.'],
            ['tag'=>'Energy',        'img'=>'power-energy.jfif',             'alt'=>'Power and Energy',         'title'=>'Power & energy',                 'desc'=>'From grid infrastructure to renewable energy systems — powering the energy transition worldwide.',             'apps'=>"Power converters and inverters\nEnergy monitoring systems\nRenewable energy controllers\nSmart grid hardware",  'note'=>'<strong>What matters most:</strong> Component durability, efficiency ratings, and supply continuity.'],
            ['tag'=>'Defense',       'img'=>'aerospace-electronics.jfif',    'alt'=>'Aerospace Electronics',    'title'=>'Aerospace & defense',            'desc'=>'Precision sourcing for non-classified aerospace and defense-adjacent manufacturing applications.',              'apps'=>"Avionics support systems\nGround equipment electronics\nTest and measurement systems",                             'note'=>'<strong>Our approach:</strong> Precision sourcing with attention to quality documentation and traceability.'],
            ['tag'=>'Test & Measurement',       'img'=>'test-measurement.jfif',         'alt'=>'Test and Measurement',     'title'=>'Test & measurement',             'desc'=>'Specialized and low-volume components for R&D labs and production test environments worldwide.',               'apps'=>"Oscilloscopes and analyzers\nCalibration equipment\nIndustrial test systems",                                     'note'=>'<strong>Why customers choose us:</strong> Deep support for specialized and hard-to-source components.'],
        ]);

        $industries = array_map(function ($ind) {
            $apps = $ind['apps'] ?? '';
            $ind['apps'] = is_array($apps)
                ? $apps
                : array_values(array_filter(array_map('trim', explode("\n", $apps))));
            return $ind;
        }, $rawIndustries);

        $tags = array_unique(array_column($industries, 'tag'));

        $valueItems = $page->get('value_items', [
            ['icon' => 'ti ti-alert-triangle', 'label' => 'Shortage support'],
            ['icon' => 'ti ti-search', 'label' => 'EOL & hard-to-find parts'],
            ['icon' => 'ti ti-adjustments-horizontal', 'label' => 'Low MOQ flexibility'],
            ['icon' => 'ti ti-clock', 'label' => 'Fast RFQ response'],
            ['icon' => 'ti ti-headset', 'label' => 'Dedicated sales support'],
        ]);
    @endphp

    <div class="ind-filter-bar">
        <button class="ind-filter-pill active" data-tag="All">All industries</button>
        @foreach($tags as $tag)
        <button class="ind-filter-pill" data-tag="{{ $tag }}">{{ $tag }}</button>
        @endforeach
    </div>

    {{-- Cards grid --}}
    <div class="ind-grid" id="indGrid">
        @foreach($industries as $i => $ind)
        <div class="ind-card ind-fade-in" data-tag="{{ $ind['tag'] }}" data-index="{{ $i }}"
             style="animation-delay: {{ $i * 0.06 }}s">
            <div class="ind-card-img-wrap">
                <img src="{{ asset('public/assets/images/industries/' . $ind['img']) }}"
                     alt="{{ $ind['alt'] }}"
                     loading="{{ $i < 3 ? 'eager' : 'lazy' }}"
                     width="600" height="400">
                <div class="ind-card-overlay">
                    <span class="ind-card-overlay-label">View details</span>
                </div>
                <div class="ind-card-tag">{{ $ind['tag'] }}</div>
            </div>
            <div class="ind-card-body">
                <h3>{{ $ind['title'] }}</h3>
                <p>{{ $ind['desc'] }}</p>
                <div class="ind-app-tags">
                    @foreach(array_slice($ind['apps'], 0, 2) as $app)
                    <span class="ind-app-tag">{{ $app }}</span>
                    @endforeach
                    @if(count($ind['apps']) > 2)
                    <span class="ind-app-tag">+{{ count($ind['apps']) - 2 }} more</span>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Value strip --}}
    <div class="ind-value">
        <h2>{{ $page->get('value_heading', 'How we add value across industries') }}</h2>
        <div class="ind-value-items">
            @foreach($valueItems as $item)
            <div class="ind-value-item">
                <i class="{{ $item['icon'] ?? 'ti ti-check' }}" aria-hidden="true"></i>
                <span>{{ $item['label'] ?? '' }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- CTA --}}
    <div class="ind-cta">
        <h2>{{ $page->get('cta_heading', "Let's support your industry") }}</h2>
        <p>{{ $page->get('cta_text', "If your industry isn't listed above, chances are we can still help. Our sourcing expertise extends across a broad range of applications and markets.") }}</p>
        <a href="{{ route('get_a_quote') }}" class="ind-cta-btn">
            Request a quote <i class="ti ti-arrow-right" aria-hidden="true"></i>
        </a>
    </div>

</div>{{-- /container --}}

{{-- Modal --}}
<div class="ind-modal-bg" id="indModalBg" role="dialog" aria-modal="true" aria-labelledby="indModalTitle">
    <div class="ind-modal-wrap">
        <button class="ind-modal-nav prev" id="indModalPrev" aria-label="Previous industry">&#8592;</button>
        <div class="ind-modal" id="indModal">
            <img id="indModalImg" src="" alt="">
            <div class="ind-modal-body">
                <div class="ind-modal-tag" id="indModalTag"></div>
                <h2 id="indModalTitle"></h2>
                <p class="ind-modal-desc" id="indModalDesc"></p>
                <div class="ind-modal-section-label">Typical applications</div>
                <div class="ind-modal-apps" id="indModalApps"></div>
                <div class="ind-modal-note" id="indModalNote"></div>
            </div>
            <div class="ind-modal-footer">
                <button class="ind-modal-close" id="indModalClose">Close</button>
                <a href="{{ route('get_a_quote') }}" class="ind-modal-cta">
                    Get a quote <i class="ti ti-arrow-right" aria-hidden="true"></i>
                </a>
            </div>
        </div>
        <button class="ind-modal-nav next" id="indModalNext" aria-label="Next industry">&#8594;</button>
    </div>
</div>

</main>

@endsection

@section('footer')
<script>
(function () {

    const industries = @json($industries);
    let activeIndex = 0;

    /* ── particle canvas ── */
    const canvas = document.getElementById('indParticles');
    const ctx    = canvas.getContext('2d');
    let dots     = [];

    function initParticles() {
        const hero  = canvas.parentElement;
        canvas.width  = hero.offsetWidth;
        canvas.height = hero.offsetHeight;
        dots = Array.from({ length: 35 }, () => ({
            x : Math.random() * canvas.width,
            y : Math.random() * canvas.height,
            r : Math.random() * 1.5 + 0.5,
            dx: (Math.random() - 0.5) * 0.35,
            dy: (Math.random() - 0.5) * 0.35,
            o : Math.random() * 0.35 + 0.1
        }));
    }

    function drawParticles() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        dots.forEach(d => {
            d.x += d.dx; d.y += d.dy;
            if (d.x < 0 || d.x > canvas.width)  d.dx *= -1;
            if (d.y < 0 || d.y > canvas.height)  d.dy *= -1;
            ctx.beginPath();
            ctx.arc(d.x, d.y, d.r, 0, Math.PI * 2);
            ctx.fillStyle = `rgba(148,163,184,${d.o})`;
            ctx.fill();
        });
        dots.forEach((a, i) => {
            dots.slice(i + 1).forEach(b => {
                const dist = Math.hypot(a.x - b.x, a.y - b.y);
                if (dist < 90) {
                    ctx.beginPath();
                    ctx.moveTo(a.x, a.y);
                    ctx.lineTo(b.x, b.y);
                    ctx.strokeStyle = `rgba(148,163,184,${0.1 * (1 - dist / 90)})`;
                    ctx.lineWidth   = 0.5;
                    ctx.stroke();
                }
            });
        });
        requestAnimationFrame(drawParticles);
    }

    initParticles();
    drawParticles();
    window.addEventListener('resize', initParticles);

    /* ── hero counter animation ── */
    function animateCounter(id, end, suffix) {
        const el  = document.getElementById(id);
        let   val = 0;
        const step = Math.ceil(end / 30);
        const tick = () => {
            val = Math.min(val + step, end);
            el.textContent = val.toLocaleString() + (suffix || '');
            if (val < end) setTimeout(tick, 50);
        };
        setTimeout(tick, 300);
    }
    animateCounter('ind-count',     industries.length, '+');
    animateCounter('ind-countries', 50,  '+');
    animateCounter('ind-parts',     500, '+');

    /* ── filter pills ── */
    const pills = document.querySelectorAll('.ind-filter-pill');
    const cards = document.querySelectorAll('.ind-card');

    pills.forEach(pill => {
        pill.addEventListener('click', () => {
            pills.forEach(p => p.classList.remove('active'));
            pill.classList.add('active');
            const tag = pill.dataset.tag;
            let delay = 0;
            cards.forEach(card => {
                const show = tag === 'All' || card.dataset.tag === tag;
                if (show) {
                    card.classList.remove('ind-hidden');
                    card.style.animationDelay = (delay++ * 0.06) + 's';
                    card.classList.remove('ind-fade-in');
                    void card.offsetWidth;
                    card.classList.add('ind-fade-in');
                } else {
                    card.classList.add('ind-hidden');
                }
            });
        });
    });

    /* ── modal ── */
    const modalBg    = document.getElementById('indModalBg');
    const modalImg   = document.getElementById('indModalImg');
    const modalTag   = document.getElementById('indModalTag');
    const modalTitle = document.getElementById('indModalTitle');
    const modalDesc  = document.getElementById('indModalDesc');
    const modalApps  = document.getElementById('indModalApps');
    const modalNote  = document.getElementById('indModalNote');
    const modalClose = document.getElementById('indModalClose');
    const modalPrev  = document.getElementById('indModalPrev');
    const modalNext  = document.getElementById('indModalNext');

    function openModal(index) {
        activeIndex = index;
        const ind = industries[index];
        modalImg.src         = '/public/assets/images/industries/' + ind.img;
        modalImg.alt         = ind.alt;
        modalTag.textContent = ind.tag;
        modalTitle.textContent = ind.title;
        modalDesc.textContent  = ind.desc;
        modalApps.innerHTML    = ind.apps.map(a =>
            `<div class="ind-modal-app"><i class="ti ti-check" aria-hidden="true"></i>${a}</div>`
        ).join('');
        modalNote.innerHTML = ind.note;
        modalBg.classList.add('ind-open');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        modalBg.classList.remove('ind-open');
        document.body.style.overflow = '';
    }

    cards.forEach(card => {
        card.addEventListener('click', () => openModal(parseInt(card.dataset.index)));
    });

    modalBg.addEventListener('click', e => {
        if (e.target === modalBg) closeModal();
    });
    modalClose.addEventListener('click', closeModal);

    modalPrev.addEventListener('click', () => {
        openModal((activeIndex - 1 + industries.length) % industries.length);
    });
    modalNext.addEventListener('click', () => {
        openModal((activeIndex + 1) % industries.length);
    });

    document.addEventListener('keydown', e => {
        if (!modalBg.classList.contains('ind-open')) return;
        if (e.key === 'Escape')      closeModal();
        if (e.key === 'ArrowLeft')   openModal((activeIndex - 1 + industries.length) % industries.length);
        if (e.key === 'ArrowRight')  openModal((activeIndex + 1) % industries.length);
    });

})();
</script>
@endsection
