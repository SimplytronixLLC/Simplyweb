@extends('includes.front')

@section('seo')
<meta name="robots" content="noindex, nofollow">
<title>Unsubscribe - Simplytronix</title>
@stop

@section('content')
<style>
.usc{
  --ink:#0B1220; --paper:#F5F7FA; --surface:#FFFFFF;
  --accent:#2F6FED; --accent-dark:#1E4FC4; --muted:#64748B; --line:#E3E7EE; --danger:#DC2626;
  font-family:'Inter',system-ui,sans-serif;
  background:var(--paper); min-height:70vh; display:flex; align-items:center; padding:40px 20px;
}
.usc-card{
  max-width:440px; margin:0 auto; background:var(--surface); border:1px solid var(--line);
  border-radius:12px; padding:32px; text-align:center; width:100%;
}
.usc-card h1{ font-family:'Space Grotesk',sans-serif; font-size:20px; font-weight:700; margin:0 0 10px; color:var(--ink); }
.usc-card p{ font-size:13.5px; color:var(--muted); line-height:1.6; margin:0 0 18px; }
.usc-email{ font-weight:600; color:var(--ink); }
.usc-btn{
  display:inline-block; padding:11px 22px; border-radius:6px; border:none;
  font-size:14px; font-weight:600; cursor:pointer; font-family:'Inter',sans-serif;
}
.usc-btn-confirm{ background:var(--danger); color:#fff; }
.usc-btn-confirm:hover{ background:#B91C1C; }
.usc-note{ font-size:11.5px; color:#94A3B8; margin-top:16px; }
.usc-already{ font-size:14px; color:var(--muted); }
</style>

<div class="usc">
  <div class="usc-card">
    @if($alreadyUnsubscribed)
      <h1>Already unsubscribed</h1>
      <p class="usc-already">The address <span class="usc-email">{{ $contact->email }}</span> is already unsubscribed from Simplytronix emails. No further action needed.</p>
    @else
      <h1>Unsubscribe from Simplytronix emails?</h1>
      <p>You're about to unsubscribe <span class="usc-email">{{ $contact->email }}</span> from future emails, including quote follow-ups and announcements.</p>
      <form method="POST" action="{{ $confirmUrl }}">
        @csrf
        <button type="submit" class="usc-btn usc-btn-confirm">Confirm unsubscribe</button>
      </form>
      <p class="usc-note">Changed your mind? Just close this page — nothing happens until you confirm.</p>
    @endif
  </div>
</div>
@stop
