@extends('includes.front')

@section('seo')
<meta name="robots" content="noindex, nofollow">
<title>Unsubscribed - Simplytronix</title>
@stop

@section('content')
<style>
usd-shared { /* placeholder to keep style block distinct, unused */ }
.usd{
  --ink:#0B1220; --paper:#F5F7FA; --surface:#FFFFFF;
  --accent:#2F6FED; --muted:#64748B; --line:#E3E7EE; --signal:#00B884;
  font-family:'Inter',system-ui,sans-serif;
  background:var(--paper); min-height:70vh; display:flex; align-items:center; padding:40px 20px;
}
.usd-card{
  max-width:440px; margin:0 auto; background:var(--surface); border:1px solid var(--line);
  border-radius:12px; padding:32px; text-align:center; width:100%;
}
.usd-icon{ font-size:32px; margin-bottom:10px; }
.usd-card h1{ font-family:'Space Grotesk',sans-serif; font-size:20px; font-weight:700; margin:0 0 10px; color:var(--ink); }
.usd-card p{ font-size:13.5px; color:var(--muted); line-height:1.6; margin:0; }
.usd-email{ font-weight:600; color:var(--ink); }
</style>

<div class="usd">
  <div class="usd-card">
    <div class="usd-icon">✅</div>
    <h1>You're unsubscribed</h1>
    <p><span class="usd-email">{{ $contact->email }}</span> won't receive further emails from Simplytronix. If this was a mistake, just reply to any previous email from us and we'll re-add you.</p>
  </div>
</div>
@stop
