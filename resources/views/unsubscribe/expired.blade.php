@extends('includes.front')
@section('seo')
<meta name="robots" content="noindex, nofollow">
<title>Unsubscribe - Simplytronix</title>
@stop
@section('content')
<style>
use-shared { /* placeholder to keep style block distinct, unused */ }
.use{
  --ink:#0B1220; --paper:#F5F7FA; --surface:#FFFFFF;
  --accent:#2F6FED; --muted:#64748B; --line:#E3E7EE;
  font-family:'Inter',system-ui,sans-serif;
  background:var(--paper); min-height:70vh; display:flex; align-items:center; padding:40px 20px;
}
.use-card{
  max-width:440px; margin:0 auto; background:var(--surface); border:1px solid var(--line);
  border-radius:12px; padding:32px; text-align:center; width:100%;
}
.use-icon{ font-size:32px; margin-bottom:10px; }
.use-card h1{ font-family:'Space Grotesk',sans-serif; font-size:20px; font-weight:700; margin:0 0 10px; color:var(--ink); }
.use-card p{ font-size:13.5px; color:var(--muted); line-height:1.6; margin:0 0 16px; }
.use-btn{
  display:inline-block; background:var(--accent); color:#fff; text-decoration:none;
  font-size:13.5px; font-weight:600; padding:10px 20px; border-radius:8px;
}
</style>
<div class="use">
  <div class="use-card">
    <div class="use-icon">⏱️</div>
    <h1>This link has expired</h1>
    <p>Sorry — this unsubscribe link is no longer valid. Email us directly and we'll remove you right away, no questions asked.</p>
    <a class="use-btn" href="mailto:info@simplytronix.com?subject=Unsubscribe%20request">Email us to unsubscribe</a>
  </div>
</div>
@stop
