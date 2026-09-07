{{-- resources/views/get-a-quote.blade.php --}}
@extends('includes.front')

@section('seo')
<link rel="canonical" href="{{ url('/get-a-quote') }}">
<meta name="robots" content="noindex, follow">
<title>Request a Fast Quote - {{ $settings->meta_title ?? '' }}</title>
<meta name="description" content="{{ $settings->meta_description ?? '' }}">
<meta name="keywords" content="{{ $settings->meta_keyword ?? '' }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600&family=IBM+Plex+Mono:wght@500;600&display=swap" rel="stylesheet">
@stop

@section('content')
<style>
html,body{height:100%;}
.sqf{
  --ink:#0B1220;
  --paper:#F5F7FA;
  --surface:#FFFFFF;
  --accent:#2F6FED;
  --accent-dark:#1E4FC4;
  --signal:#00B884;
  --muted:#64748B;
  --line:#E3E7EE;
  --danger:#DC2626;
  font-family:'Inter',system-ui,sans-serif;
  background:var(--paper);
  padding:20px 0;
  color:var(--ink);
  min-height:100vh;
  display:flex;
  align-items:center;
}
.sqf *{box-sizing:border-box;}
.sqf-container{max-width:1080px;margin:0 auto;padding:0 20px;width:100%;}

/* Header */
.sqf-eyebrow{
  display:inline-flex;align-items:center;gap:7px;
  font-family:'IBM Plex Mono',monospace;
  font-size:11px;font-weight:600;letter-spacing:.1em;
  color:var(--accent-dark);
  background:#EAF0FE;
  border:1px solid #D6E2FC;
  padding:4px 10px;border-radius:4px;
  margin-bottom:10px;
}
.sqf-eyebrow::before{
  content:"";width:6px;height:6px;background:var(--accent);border-radius:50%;
  display:inline-block;
}
.sqf-header{max-width:680px;margin-bottom:16px;}
.sqf-header h1{
  font-family:'Space Grotesk',sans-serif;
  font-size:clamp(20px,2.6vw,26px);
  font-weight:700;
  line-height:1.15;
  margin:0 0 6px;
  color:var(--ink);
}
.sqf-header p{
  font-size:14px;line-height:1.5;color:var(--muted);margin:0;
}

/* Grid */
.sqf-grid{
  display:grid;
  grid-template-columns:minmax(260px,340px) 1fr;
  gap:18px;
  align-items:start;
}
@media (max-width:900px){
  .sqf-grid{grid-template-columns:1fr;}
}

/* Pin strip signature - echoes the DIP-chip motif used across the admin dashboard */
.sqf-pins{display:flex;gap:5px;padding:0 20px;margin-bottom:-1px;}
.sqf-pins span{
  width:7px;height:9px;
  background:var(--ink);
  opacity:.12;
  border-radius:1px 1px 0 0;
}

/* Cards */
.sqf-card{
  background:var(--surface);
  border:1px solid var(--line);
  border-radius:10px;
  overflow:hidden;
}
.sqf-card-body{padding:18px 20px;}
@media (max-width:520px){ .sqf-card-body{padding:16px;} }

.sqf-spec-card{position:sticky;top:16px;}
@media (max-width:900px){ .sqf-spec-card{position:static;} }

.sqf-label{
  font-family:'IBM Plex Mono',monospace;
  font-size:10px;font-weight:600;letter-spacing:.08em;
  color:var(--muted);text-transform:uppercase;
  margin-bottom:6px;
}
.sqf-partnum{
  font-family:'IBM Plex Mono',monospace;
  font-size:16px;font-weight:600;
  color:var(--ink);
  word-break:break-word;
  padding:8px 12px;
  background:var(--paper);
  border:1px solid var(--line);
  border-radius:6px;
  margin-bottom:12px;
}
.sqf-details{font-size:13px;line-height:1.5;color:var(--muted);margin-bottom:12px;}

.sqf-divider{height:1px;background:var(--line);margin:12px 0;}

.sqf-spec-table{width:100%;border-collapse:collapse;}
.sqf-spec-table tr{border-bottom:1px dashed var(--line);}
.sqf-spec-table tr:last-child{border-bottom:none;}
.sqf-spec-table td{padding:6px 0;font-size:12.5px;vertical-align:top;}
.sqf-spec-table td:first-child{
  color:var(--muted);width:42%;padding-right:12px;
}
.sqf-spec-table td:last-child{
  color:var(--ink);font-weight:500;
}

.sqf-note{
  margin-top:12px;
  padding:10px 12px;
  background:#F0FBF7;
  border-left:3px solid var(--signal);
  border-radius:0 6px 6px 0;
  font-size:12px;line-height:1.45;
}
.sqf-note strong{display:block;margin-bottom:2px;color:var(--ink);font-size:12.5px;}

/* Alerts */
.sqf-alert{
  padding:10px 14px;border-radius:6px;font-size:13px;margin-bottom:12px;
  border:1px solid transparent;
}
.sqf-alert-success{background:#ECFDF5;border-color:#A7F3D0;color:#065F46;}
.sqf-alert-fail{background:#FEF2F2;border-color:#FECACA;color:#991B1B;}

/* Form */
.sqf-form-title{
  font-family:'Space Grotesk',sans-serif;
  font-size:16px;font-weight:600;margin:0 0 2px;
}
.sqf-form-sub{font-size:12.5px;color:var(--muted);margin:0 0 12px;}

.sqf-field{margin-bottom:10px;}
.sqf-row{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
@media (max-width:520px){ .sqf-row{grid-template-columns:1fr;} }

.sqf-field label{
  display:block;font-size:12.5px;font-weight:600;color:var(--ink);margin-bottom:4px;
}
.sqf-field label .req{color:var(--danger);margin-left:2px;}
.sqf-field label .opt{color:var(--muted);font-weight:400;}

.sqf-input{
  width:100%;height:38px;padding:0 12px;
  border:1px solid var(--line);border-radius:6px;
  font-size:14px;font-family:'Inter',sans-serif;color:var(--ink);
  background:var(--surface);
  transition:border-color .15s, box-shadow .15s;
}
.sqf-input:focus{
  outline:none;border-color:var(--accent);
  box-shadow:0 0 0 3px rgba(47,111,237,.15);
}
textarea.sqf-input{height:54px;padding:9px 12px;resize:vertical;}

.sqf-submit{
  width:100%;height:42px;border:none;border-radius:6px;
  background:var(--accent);color:#fff;
  font-size:14.5px;font-weight:600;font-family:'Inter',sans-serif;
  cursor:pointer;
  display:flex;align-items:center;justify-content:center;gap:8px;
  transition:background .15s;
  margin-top:2px;
}
.sqf-submit:hover{background:var(--accent-dark);}
.sqf-submit svg{width:14px;height:14px;}

.sqf-confidential{
  text-align:center;font-size:11.5px;color:var(--muted);margin:8px 0 0;
  display:flex;align-items:center;justify-content:center;gap:6px;
}

.sqf-error{color:var(--danger);font-size:12px;margin-top:6px;}
</style>

<div class="sqf">
<div class="sqf-container">

  <div class="sqf-header">
    <h1>Get pricing without the back-and-forth.</h1>
    <p>Tell us the part and quantity. Our sourcing team replies with pricing, lead time and available stock — usually within one business day.</p>
  </div>

  <div class="sqf-grid">

    <!-- Spec / info card -->
    <div>
      
      <div class="sqf-card sqf-spec-card">
        <div class="sqf-card-body">

          <div class="sqf-label" style="margin-top:4px;">What you get</div>
          <table class="sqf-spec-table">
            <tr><td>Response window</td><td>2–24 business hours</td></tr>
            <tr><td>Sourcing network</td><td>Global, OEM &amp; independent stock</td></tr>
            <tr><td>Documentation</td><td>Test Report &amp; COC on request</td></tr>
            <tr><td>Confidentiality</td><td>Your RFQ is never shared</td></tr>
            <tr><td>Obligation</td><td>None — quotes are free</td></tr>
          </table>

          <div class="sqf-note">
            <strong>Looking for a hard-to-find part?</strong>
            We specialize in sourcing obsolete, EOL and allocated components that other distributors won't touch.
          </div>

        </div>
      </div>
    </div>

    <!-- Form card -->
    <div>
      
      <div class="sqf-card">
        <div class="sqf-card-body">

          <div class="sqf-form-title">Your details</div>
          <p class="sqf-form-sub">Fields marked <span style="color:var(--danger)">*</span> are required.</p>

          @if(Session::has('success'))
            <div class="sqf-alert sqf-alert-success">{{ Session::get('success') }}</div>
          @endif
          @if(Session::has('fail'))
            <div class="sqf-alert sqf-alert-fail">{{ Session::get('fail') }}</div>
          @endif

          <form method="POST" action="{{ route('get_a_quote_submit') }}">
            @csrf

            <div class="sqf-field">
              <label for="sqf-name">Name<span class="req">*</span></label>
              <input type="text" id="sqf-name" class="sqf-input" name="name" required>
            </div>

            <div class="sqf-field">
              <label for="sqf-email">Business email<span class="req">*</span></label>
              <input type="email" id="sqf-email" class="sqf-input" name="email" required>
            </div>

            <div class="sqf-field">
              <label for="sqf-company">Company name<span class="req">*</span></label>
              <input type="text" id="sqf-company" class="sqf-input" name="company" required>
            </div>

            <div class="sqf-field">
              <label for="sqf-partnum">Part number <span class="opt">(optional)</span></label>
              <input type="text" id="sqf-partnum" class="sqf-input" name="part_number" value="{{ $name ?? '' }}" placeholder="e.g. LM317T">
            </div>

            <div class="sqf-row">
              <div class="sqf-field">
                <label for="sqf-phone">Phone <span class="opt">(optional)</span></label>
                <input type="text" id="sqf-phone" class="sqf-input" name="phone">
              </div>
              <div class="sqf-field">
                <label for="sqf-qty">Quantity<span class="req">*</span></label>
                <input type="number" id="sqf-qty" class="sqf-input" value="1" min="1" name="quantity" required>
              </div>
            </div>

            <div class="sqf-field">
              <label for="sqf-comments">Additional requirements</label>
              <textarea id="sqf-comments" class="sqf-input" name="comments"
                placeholder="Target price, date code, packaging, shipping destination or any additional requirements..."></textarea>
            </div>

            <div class="sqf-field">
              <div class="g-recaptcha" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"></div>
              @if($errors->has('g-recaptcha-response'))
                <div class="sqf-error">{{ $errors->first('g-recaptcha-response') }}</div>
              @endif
            </div>

            <button type="submit" class="sqf-submit">
              Send quote request
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
            </button>

            <p class="sqf-confidential">🔒 Kept confidential — used only to respond to this request.</p>

          </form>

        </div>
      </div>
    </div>

  </div>
</div>
</div>

<script src="https://www.google.com/recaptcha/api.js" async defer></script>
@stop

@section('footer')
@stop