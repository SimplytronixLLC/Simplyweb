@extends('includes.front')

@section('seo')
<title>Quality Assurance | Electronic Component Inspection – Simplytronix</title>
<meta name="description"
content="Simplytronix applies structured inspection procedures aligned with SAE AS6081, AS6171 and IDEA-STD-1010. Advanced testing is conducted by independent third-party laboratories to ensure authenticity and reliability.">
@stop

@section('content')

<main class="main__content_wrapper">
    
 <section class="breadcrumb__section breadcrumb__bg" style="position: relative;">
        <div style="position: absolute; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.5); z-index:1;"></div>
        <div class="container" style="position: relative; z-index: 2;">
            <div class="row row-cols-1">
                <div class="col text-center">
                    <div class="breadcrumb__content text-white py-2">
                        <h1 class="breadcrumb__content--title mb-3" style="color: #fff;">Quality Assurance</h1>
                        <p class="lead" style="color: #fff;">Authenticity Verification & Component Inspection</p>
                    </div>
                </div>
            </div>
        </div>
    </section>


<section class="py-4">
<div class="container">
<div class="row justify-content-center">
<div class="col-lg-9 text-center">

<p class="mb-2" style="line-height:1.7;">
At <strong>Simplytronix</strong>, quality control is embedded into our sourcing workflow.
Our inspection procedures are aligned with internationally recognized counterfeit
mitigation and verification standards to protect your supply chain.
</p>

<p class="mb-0">
Advanced analytical testing is conducted through <strong>independent third-party laboratories</strong>
to ensure unbiased verification and component authenticity.
</p>

</div>
</div>
</div>
</section>

<section class="py-4 bg-light">
<div class="container">
<div class="row justify-content-center">
<div class="col-lg-9 text-center">

<h2 class="fw-bold mb-3">Standards & Industry Alignment</h2>

<ul class="value-list mb-0">
    <li><strong>SAE AS6081</strong> – Counterfeit Electronic Parts Mitigation</li>
    <li><strong>AS6171</strong> – Counterfeit Detection Test Methods</li>
    <li><strong>IDEA-STD-1010</strong> – Electronic Component Acceptability</li>
    <li><strong>ISO 9001:2015 Principles</strong> – Quality Management Practices</li>
    <li><strong>JEDEC Standards</strong> – Semiconductor Reliability & Packaging</li>
    <li><strong>J-STD-033</strong> – Moisture Sensitivity Handling</li>
</ul>

</div>
</div>
</div>
</section>

<section class="py-4">
<div class="container">
<div class="row g-3">

<div class="col-md-6 col-lg-4">
    <div class="industry-card p-3 shadow-sm h-100 text-center">
        <h4 class="fw-bold mb-2">External Visual Inspection</h4>
        <p class="mb-0">
            40x microscopy inspection to detect resurfacing, remarking,
            oxidation, and marking inconsistencies.
        </p>
    </div>
</div>

<div class="col-md-6 col-lg-4">
    <div class="industry-card p-3 shadow-sm h-100 text-center">
        <h4 class="fw-bold mb-2">X-Ray Analysis</h4>
        <p class="mb-0">
            Non-destructive internal inspection verifying die structure,
            bonding integrity, and internal construction.
        </p>
    </div>
</div>

<div class="col-md-6 col-lg-4">
    <div class="industry-card p-3 shadow-sm h-100 text-center">
        <h4 class="fw-bold mb-2">Decapsulation & Die Verification</h4>
        <p class="mb-0">
            Controlled decapsulation confirming manufacturer logo
            and semiconductor authenticity.
        </p>
    </div>
</div>

<div class="col-md-6 col-lg-4">
    <div class="industry-card p-3 shadow-sm h-100 text-center">
        <h4 class="fw-bold mb-2">Solvent & Resurfacing Testing</h4>
        <p class="mb-0">
            Heated solvent testing to detect secondary coatings
            or surface tampering.
        </p>
    </div>
</div>

<div class="col-md-6 col-lg-4">
    <div class="industry-card p-3 shadow-sm h-100 text-center">
        <h4 class="fw-bold mb-2">Mechanical Verification</h4>
        <p class="mb-0">
            Dimensional validation against manufacturer specifications.
        </p>
    </div>
</div>

<div class="col-md-6 col-lg-4">
    <div class="industry-card p-3 shadow-sm h-100 text-center">
        <h4 class="fw-bold mb-2">Documentation Review</h4>
        <p class="mb-0">
            Verification of labeling, packaging integrity, and traceability records.
        </p>
    </div>
</div>

</div>
</div>
</section>

<section class="py-4 bg-light">
<div class="container">
<div class="row justify-content-center">
<div class="col-lg-10 text-center">

<h2 class="fw-bold mb-3">Sample Laboratory Report</h2>

<p class="mb-3">
Check out our sample laboratory report demonstrating our inspection methodology.

</p>

<button onclick="togglePdfViewer()" id="pdfToggleBtn" class="primary__btn">
    View Sample Report
</button>

<div id="pdfViewerWrapper" style="display:none;" class="mt-4">
    <iframe 
         src="{{ asset('public/uploads/test-reports/copy_watermark.pdf') }}#toolbar=0&navpanes=0&scrollbar=1"
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

<h2 class="fw-bold mb-2">Why Customers Trust Simplytronix</h2>

<ul class="value-list mb-3">
    <li>Independent third-party laboratory testing support</li>
    <li>Structured counterfeit risk mitigation procedures</li>
    <li>Full traceability and documentation assistance</li>
    <li>Hard-to-find and EOL sourcing expertise</li>
    <li>Responsive RFQ turnaround</li>
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