@extends('includes.front')

@php
    $page = \App\Models\Page::content('quality-assurance');

    $standards = $page->get('standards', [
        ['name' => 'SAE AS6081', 'description' => 'Counterfeit Electronic Parts Mitigation'],
        ['name' => 'AS6171', 'description' => 'Counterfeit Detection Test Methods'],
        ['name' => 'IDEA-STD-1010', 'description' => 'Electronic Component Acceptability'],
        ['name' => 'ISO 9001:2015 Principles', 'description' => 'Quality Management Practices'],
        ['name' => 'JEDEC Standards', 'description' => 'Semiconductor Reliability & Packaging'],
        ['name' => 'J-STD-033', 'description' => 'Moisture Sensitivity Handling'],
    ]);

    $inspectionCards = $page->get('inspection_cards', [
        ['title' => 'External Visual Inspection', 'description' => '40x microscopy inspection to detect resurfacing, remarking, oxidation, and marking inconsistencies.'],
        ['title' => 'X-Ray Analysis', 'description' => 'Non-destructive internal inspection verifying die structure, bonding integrity, and internal construction.'],
        ['title' => 'Decapsulation & Die Verification', 'description' => 'Controlled decapsulation confirming manufacturer logo and semiconductor authenticity.'],
        ['title' => 'Solvent & Resurfacing Testing', 'description' => 'Heated solvent testing to detect secondary coatings or surface tampering.'],
        ['title' => 'Mechanical Verification', 'description' => 'Dimensional validation against manufacturer specifications.'],
        ['title' => 'Documentation Review', 'description' => 'Verification of labeling, packaging integrity, and traceability records.'],
    ]);

    $whyTrustItems = $page->get('why_trust_items', [
        ['text' => 'Independent third-party laboratory testing support'],
        ['text' => 'Structured counterfeit risk mitigation procedures'],
        ['text' => 'Full traceability and documentation assistance'],
        ['text' => 'Hard-to-find and EOL sourcing expertise'],
        ['text' => 'Responsive RFQ turnaround'],
    ]);
@endphp

@section('seo')
<title>Quality Assurance | Electronic Component Inspection – Simplytronix</title>
<meta name="description"
content="{{ $page->get('meta_description', 'Simplytronix applies structured inspection procedures aligned with SAE AS6081, AS6171 and IDEA-STD-1010. Advanced testing is conducted by independent third-party laboratories to ensure authenticity and reliability.') }}">
@stop

@section('content')

<main class="main__content_wrapper">
    
 <section class="breadcrumb__section breadcrumb__bg" style="position: relative;">
        <div style="position: absolute; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.5); z-index:1;"></div>
        <div class="container" style="position: relative; z-index: 2;">
            <div class="row row-cols-1">
                <div class="col text-center">
                    <div class="breadcrumb__content text-white py-2">
                        <h1 class="breadcrumb__content--title mb-3" style="color: #fff;">{{ $page->get('hero_title', 'Quality Assurance') }}</h1>
                        <p class="lead" style="color: #fff;">{{ $page->get('hero_subtitle', 'Authenticity Verification & Component Inspection') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>


<section class="py-4">
<div class="container">
<div class="row justify-content-center">
<div class="col-lg-9 text-center">

{!! $page->get('intro_paragraph_1', '<p class="mb-2" style="line-height:1.7;">At <strong>Simplytronix</strong>, quality control is embedded into our sourcing workflow. Our inspection procedures are aligned with internationally recognized counterfeit mitigation and verification standards to protect your supply chain.</p>') !!}

{!! $page->get('intro_paragraph_2', '<p class="mb-0">Advanced analytical testing is conducted through <strong>independent third-party laboratories</strong> to ensure unbiased verification and component authenticity.</p>') !!}

</div>
</div>
</div>
</section>

<section class="py-4 bg-light">
<div class="container">
<div class="row justify-content-center">
<div class="col-lg-9 text-center">

<h2 class="fw-bold mb-3">{{ $page->get('standards_heading', 'Standards & Industry Alignment') }}</h2>

<ul class="value-list mb-0">
    @foreach($standards as $standard)
    <li><strong>{{ $standard['name'] ?? '' }}</strong> – {{ $standard['description'] ?? '' }}</li>
    @endforeach
</ul>

</div>
</div>
</div>
</section>

<section class="py-4">
<div class="container">
<div class="row g-3">

@foreach($inspectionCards as $card)
<div class="col-md-6 col-lg-4">
    <div class="industry-card p-3 shadow-sm h-100 text-center">
        <h4 class="fw-bold mb-2">{{ $card['title'] ?? '' }}</h4>
        <p class="mb-0">
            {{ $card['description'] ?? '' }}
        </p>
    </div>
</div>
@endforeach

</div>
</div>
</section>

<section class="py-4 bg-light">
<div class="container">
<div class="row justify-content-center">
<div class="col-lg-10 text-center">

<h2 class="fw-bold mb-3">{{ $page->get('sample_report_heading', 'Sample Laboratory Report') }}</h2>

<p class="mb-3">
{{ $page->get('sample_report_text', 'Check out our sample laboratory report demonstrating our inspection methodology.') }}
</p>

<button onclick="togglePdfViewer()" id="pdfToggleBtn" class="primary__btn">
    View Sample Report
</button>

<div id="pdfViewerWrapper" style="display:none;" class="mt-4">
    <iframe 
         src="{{ asset($page->get('sample_report_pdf', 'public/uploads/test-reports/copy_watermark.pdf')) }}#toolbar=0&navpanes=0&scrollbar=1"
        width="100%" 
        height="650"
        style="border:1px solid #ddd; border-radius:6px;">
    </iframe>
</div>

</div>
</div>
</div>
</section>

<section class="py-4">
<div class="container text-center">

<h2 class="fw-bold mb-2">{{ $page->get('why_trust_heading', 'Why Customers Trust Simplytronix') }}</h2>

<ul class="value-list mb-3">
    @foreach($whyTrustItems as $item)
    <li>{{ $item['text'] ?? '' }}</li>
    @endforeach
</ul>

<a href="{{ route('get_a_quote') }}" class="primary__btn">
    Request a Quote
</a>

</div>
</section>

</main>

<script>
function togglePdfViewer() {
    var viewer = document.getElementById('pdfViewerWrapper');
    var button = document.getElementById('pdfToggleBtn');

    if (viewer.style.display === 'none') {
        viewer.style.display = 'block';
        button.innerText = 'Hide Sample Report';
        viewer.scrollIntoView({ behavior: 'smooth' });
    } else {
        viewer.style.display = 'none';
        button.innerText = 'View Sample Report';
    }
}
</script>

@endsection
