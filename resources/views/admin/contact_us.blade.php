@extends('includes.front')

@php
    $page = \App\Models\Page::content('contact-us');

    $offices = $page->get('offices', [
        [
            'region_label' => 'USA Office',
            'company_line' => 'Simplytronix LLC',
            'address' => "1007 N Orange St, 4th Floor Suite #1382\nWilmington, DE 19801\nUnited States",
            'phone' => '+1 302-600-2554',
            'email' => 'info@simplytronix.com',
            'map_embed_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2916.3061246658103!2d-75.55989931562665!3d39.73709359821858!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c6fd66239de7f7%3A0xb89847a87ae22294!2s1007n%20Orange%20St%2C%20Wilmington%2C%20DE%2019801%2C%20USA!5e1!3m2!1sen!2sin!4v1777730965911!5m2!1sen!2sin',
        ],
        [
            'region_label' => 'India Office',
            'company_line' => '(Operated by Chipaxia Pvt Ltd)',
            'address' => "3rd Floor, Westend Mall\nRajendra Nagar, Indore\nMadhya Pradesh 452012\nIndia",
            'phone' => '+91 0731-4067829',
            'email' => 'sales@simplytronix.com',
            'map_embed_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3499.5687862957548!2d75.82313477508056!3d22.66355062942702!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3962fd7ee72478e5%3A0x36d9764a0b325341!2sWestend%20Indore!5e1!3m2!1sen!2sin!4v1777729654198!5m2!1sen!2sin',
        ],
    ]);

    $whyBodyLines = array_filter(array_map('trim', explode("\n", $page->get('why_body', "Global sourcing network\nRFQ response within 24 hours\nSupport for hard-to-find components"))));
@endphp

@section('content')
<main class="main__content_wrapper">

<section class="breadcrumb__section breadcrumb__bg" style="position: relative;">
    <div style="position:absolute; inset:0; background:rgba(0,0,0,0.55); z-index:1;"></div>
    <div class="container position-relative" style="z-index:2;">
        <div class="row">
            <div class="col text-center">
                <div class="breadcrumb__content text-white page-hero">
                    <h1 class="breadcrumb__content--title mb-2" style="color:#fff;">
                        {{ $page->get('hero_title', 'Contact Us') }}
                    </h1>
                    <p class="lead mb-0" style="color:#fff;">
                        {{ $page->get('hero_subtitle', 'Global sourcing support with offices in USA & India') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section--padding" style="background:#f6f7f9;">
<div class="container">

<div class="row g-5">

<div class="col-md-7">
<div class="p-4 bg-white rounded shadow-sm" style="border:1px solid #e5e7eb;">
<h3 class="mb-4">{{ $page->get('form_heading', 'Get in Touch') }}</h3>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<form method="POST" action="{{ route('contact_us_submit') }}">
@csrf

<div class="row">
<div class="col-md-6 mb-3">
<label class="form-label">Your Name</label>
<input type="text" name="name" class="form-control" required>
</div>

<div class="col-md-6 mb-3">
<label class="form-label">Email Address</label>
<input type="email" name="email" class="form-control" required>
</div>
</div>

<div class="mb-3">
<label class="form-label">Subject</label>
<input type="text" name="subject" class="form-control" required>
</div>

<div class="mb-3">
<label class="form-label">Message</label>
<textarea name="message" class="form-control" rows="5" required></textarea>
</div>

<button type="submit" class="btn w-100" style="background:#f59e0b;color:#fff;font-weight:600;">
Send Message
</button>

</form>
</div>

<div style="margin-top:15px;background:#fff;border:1px solid #e5e7eb;border-radius:8px;padding:15px;font-size:13px;">
<strong>{{ $page->get('why_heading', 'Why Simplytronix?') }}</strong><br>
@foreach($whyBodyLines as $line)
{{ $line }}<br>
@endforeach
</div>
</div>

<div class="col-md-5">

<div style="background:#fff;border-radius:8px;border:1px solid #e5e7eb;padding:20px;margin-bottom:15px;">

<div class="office-wrap" style="display:flex;gap:20px;">

@foreach($offices as $i => $office)
<div style="flex:1; {{ $i === 0 ? 'border-right:1px solid #eee;padding-right:15px;' : 'padding-left:15px;' }}">
<h5 style="font-weight:700;margin-bottom:6px;">{{ $office['region_label'] ?? '' }}</h5>

@if(!empty($office['company_line']))
<div style="font-size:13px;color:#333;margin-bottom:6px;">
<strong>{{ $office['company_line'] }}</strong>
</div>
@endif

<p style="font-size:13px;color:#555;">
{!! nl2br(e($office['address'] ?? '')) !!}
</p>

<p style="font-size:13px;"><strong>{{ $office['phone'] ?? '' }}</strong></p>

<p style="font-size:13px;">
<a href="mailto:{{ $office['email'] ?? '' }}">{{ $office['email'] ?? '' }}</a>
</p>
</div>
@endforeach

</div>

</div>

<div style="background:#fff;border-radius:8px;border:1px solid #e5e7eb;padding:15px;">

<div style="display:flex;margin-bottom:10px;border-radius:6px;overflow:hidden;border:1px solid #e5e7eb;">

@foreach($offices as $i => $office)
<button onclick="showMap('office{{ $i }}')" id="btn-office{{ $i }}"
style="flex:1;padding:8px;font-size:12px;border:none;{{ $i === 0 ? 'background:#f59e0b;color:#fff;font-weight:600;' : 'background:#fff;color:#333;' }}">
{{ $office['region_label'] ?? 'Office ' . ($i + 1) }}
</button>
@endforeach

</div>

@foreach($offices as $i => $office)
<div id="map-office{{ $i }}" style="{{ $i === 0 ? '' : 'display:none;' }}">
<iframe 
src="{{ $office['map_embed_url'] ?? '' }}"
width="100%" height="220"
style="border:0;border-radius:6px;" loading="lazy">
</iframe>
</div>
@endforeach

</div>

</div>

</div>
</div>
</section>

<style>
@media(max-width:768px){
.office-wrap{
flex-direction:column;
}
.office-wrap > div{
border-right:none !important;
padding-right:0 !important;
margin-bottom:15px;
}
}
</style>

<script>
const officeCount = {{ count($offices) }};

function showMap(activeId){
for (let i = 0; i < officeCount; i++) {
    const id = 'office' + i;
    const mapEl = document.getElementById('map-' + id);
    const btnEl = document.getElementById('btn-' + id);
    if (!mapEl || !btnEl) continue;

    const isActive = id === activeId;
    mapEl.style.display = isActive ? 'block' : 'none';
    btnEl.style.background = isActive ? '#f59e0b' : '#fff';
    btnEl.style.color = isActive ? '#fff' : '#333';
    btnEl.style.fontWeight = isActive ? '600' : 'normal';
}
}
</script>

</main>
@endsection
