@extends('includes.front')

@section('content')
<main class="main__content_wrapper">

<section class="breadcrumb__section breadcrumb__bg" style="position: relative;">
    <div style="position:absolute; inset:0; background:rgba(0,0,0,0.55); z-index:1;"></div>
    <div class="container position-relative" style="z-index:2;">
        <div class="row">
            <div class="col text-center">
                <div class="breadcrumb__content text-white page-hero">
                    <h1 class="breadcrumb__content--title mb-2" style="color:#fff;">
                        Contact Us
                    </h1>
                    <p class="lead mb-0" style="color:#fff;">
                        Global sourcing support with offices in USA & India
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
<h3 class="mb-4">Get in Touch</h3>

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
<strong>Why Simplytronix?</strong><br>
Global sourcing network<br>
RFQ response within 24 hours<br>
Support for hard-to-find components
</div>
</div>

<div class="col-md-5">

<div style="background:#fff;border-radius:8px;border:1px solid #e5e7eb;padding:20px;margin-bottom:15px;">



<div class="office-wrap" style="display:flex;gap:20px;">

<div style="flex:1;border-right:1px solid #eee;padding-right:15px;">
<h5 style="font-weight:700;margin-bottom:6px;">USA Office</h5>

<div style="font-size:13px;color:#333;margin-bottom:6px;">
<strong>Simplytronix LLC</strong>
</div>

<p style="font-size:13px;color:#555;">
1007 N Orange St, 4th Floor Suite #1382<br>
Wilmington, DE 19801<br>
United States
</p>

<p style="font-size:13px;"><strong>+1 302-600-2554</strong></p>

<p style="font-size:13px;">
<a href="mailto:info@simplytronix.com">info@simplytronix.com</a>
</p>
</div>

<div style="flex:1;padding-left:15px;">
<h5 style="font-weight:700;margin-bottom:4px;">India Office</h5>

<div style="font-size:13px;font-weight:600;color:#444;margin-bottom:6px;">
(Operated by Chipaxia Pvt Ltd)
</div>

<p style="font-size:13px;color:#555;">
3rd Floor, Westend Mall<br>
Rajendra Nagar, Indore<br>
Madhya Pradesh 452012<br>
India
</p>

<p style="font-size:13px;"><strong>+91 0731-4067829</strong></p>

<p style="font-size:13px;">
<a href="mailto:sales@simplytronix.com">sales@simplytronix.com</a>
</p>
</div>

</div>

</div>

<div style="background:#fff;border-radius:8px;border:1px solid #e5e7eb;padding:15px;">

<div style="display:flex;margin-bottom:10px;border-radius:6px;overflow:hidden;border:1px solid #e5e7eb;">

<button onclick="showMap('usa')" id="btn-usa"
style="flex:1;padding:8px;font-size:12px;border:none;background:#f59e0b;color:#fff;font-weight:600;">
USA
</button>

<button onclick="showMap('india')" id="btn-india"
style="flex:1;padding:8px;font-size:12px;border:none;background:#fff;color:#333;">
India
</button>

</div>

<div id="map-usa">
<iframe 
src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2916.3061246658103!2d-75.55989931562665!3d39.73709359821858!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c6fd66239de7f7%3A0xb89847a87ae22294!2s1007n%20Orange%20St%2C%20Wilmington%2C%20DE%2019801%2C%20USA!5e1!3m2!1sen!2sin!4v1777730965911!5m2!1sen!2sin"
width="100%" height="220"
style="border:0;border-radius:6px;" loading="lazy">
</iframe>
</div>

<div id="map-india" style="display:none;">
<iframe 
src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3499.5687862957548!2d75.82313477508056!3d22.66355062942702!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3962fd7ee72478e5%3A0x36d9764a0b325341!2sWestend%20Indore!5e1!3m2!1sen!2sin!4v1777729654198!5m2!1sen!2sin"
width="100%" height="220"
style="border:0;border-radius:6px;" loading="lazy">
</iframe>
</div>

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
function showMap(type){
document.getElementById('map-usa').style.display = type==='usa'?'block':'none';
document.getElementById('map-india').style.display = type==='india'?'block':'none';

document.getElementById('btn-usa').style.background = type==='usa'?'#f59e0b':'#fff';
document.getElementById('btn-usa').style.color = type==='usa'?'#fff':'#333';

document.getElementById('btn-india').style.background = type==='india'?'#f59e0b':'#fff';
document.getElementById('btn-india').style.color = type==='india'?'#fff':'#333';
}
</script>

</main>
@endsection