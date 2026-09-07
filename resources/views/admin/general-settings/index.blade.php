@extends('admin.includes.masterpage-admin')
@section('content')
<div class="sx-settings">
    <div class="sx-eyebrow"><span class="sx-dot sx-dot--sales"></span> GENERAL OPTIONS</div>
    <h1 class="sx-title">Settings</h1>

    @if (session('success'))
        <div class="sx-alert sx-alert--ok">{{ session('success') }}</div>
    @endif

    <div class="sx-tabs" id="sxTabs">
        <button type="button" class="sx-tab active" data-tab="company">Company Info</button>
        <button type="button" class="sx-tab" data-tab="seo">SEO Defaults</button>
    </div>

    <form method="POST" action="{{ route('admin.general-settings.update') }}">
        @csrf

        <div class="sx-panel" data-panel="company">
            <div class="sx-card">
                <div class="sx-field">
                    <label>Company / Site Title</label>
                    <input type="text" name="title" value="{{ old('title', $settings->title ?? '') }}">
                </div>
                <div class="sx-field">
                    <label>Address</label>
                    <textarea name="address" rows="3">{{ old('address', $settings->address ?? '') }}</textarea>
                </div>
                <div class="sx-row">
                    <div class="sx-field">
                        <label>City</label>
                        <input type="text" name="city" value="{{ old('city', $settings->city ?? '') }}">
                    </div>
                    <div class="sx-field">
                        <label>State</label>
                        <input type="text" name="state" value="{{ old('state', $settings->state ?? '') }}">
                    </div>
                </div>
                <div class="sx-row">
                    <div class="sx-field">
                        <label>Country</label>
                        <input type="text" name="country" value="{{ old('country', $settings->country ?? '') }}">
                    </div>
                    <div class="sx-field">
                        <label>Zipcode</label>
                        <input type="text" name="zipcode" value="{{ old('zipcode', $settings->zipcode ?? '') }}">
                    </div>
                </div>
                <div class="sx-row">
                    <div class="sx-field">
                        <label>Phone</label>
                        <input type="text" name="phone" value="{{ old('phone', $settings->phone ?? '') }}">
                    </div>
                    <div class="sx-field">
                        <label>Email</label>
                        <input type="email" name="email" value="{{ old('email', $settings->email ?? '') }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="sx-panel" data-panel="seo" hidden>
            <div class="sx-card">
                <div class="sx-field">
                    <label>Meta Title</label>
                    <input type="text" name="meta_title" value="{{ old('meta_title', $settings->meta_title ?? '') }}">
                </div>
                <div class="sx-field">
                    <label>Meta Description</label>
                    <textarea name="meta_description" rows="2">{{ old('meta_description', $settings->meta_description ?? '') }}</textarea>
                </div>
                <div class="sx-field">
                    <label>Meta Keywords</label>
                    <input type="text" name="meta_keyword" value="{{ old('meta_keyword', $settings->meta_keyword ?? '') }}">
                </div>
            </div>
        </div>

        <button type="submit" class="sx-save">Save Settings</button>
    </form>
</div>

<style>
    .sx-settings {
        --bg:            #F7F8FA;
        --surface:       #FFFFFF;
        --border:        #E7EAF0;
        --text:          #1A1F2B;
        --muted:         #6B7280;
        --sales-1:       #53ADD0;
        --sales-2:       #4FC2C7;
        --content-1:     #F59E0B;
        --system-1:      #4F46E5;
        --ok:            #16A34A;
        --danger:        #DC2626;
        font-family: 'Poppins', sans-serif;
        background: var(--bg);
        padding: 4px 2px 32px;
        max-width: 720px;
    }
    .sx-settings .sx-eyebrow {
        display: flex; align-items: center; gap: 8px;
        font-weight: 600; font-size: 12px; letter-spacing: .06em;
        color: var(--muted); margin: 22px 4px 6px;
    }
    .sx-settings .sx-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; }
    .sx-settings .sx-dot--sales { background: var(--sales-1); }
    .sx-settings .sx-dot--content { background: var(--content-1); }
    .sx-settings .sx-title { font-size: 22px; font-weight: 700; color: var(--text); margin: 0 4px 20px; }
    .sx-settings .sx-alert { margin: 0 4px 16px; padding: 10px 14px; border-radius: 8px; font-size: 14px; font-weight: 500; }
    .sx-settings .sx-alert--ok { background: rgba(22,163,74,.1); color: var(--ok); border: 1px solid rgba(22,163,74,.25); }
    .sx-settings .sx-tabs { display: flex; gap: 4px; margin: 0 4px 18px; border-bottom: 1px solid var(--border); }
    .sx-settings .sx-tab {
        background: none; border: none; padding: 10px 16px; font-family: inherit;
        font-size: 14px; font-weight: 600; color: var(--muted); cursor: pointer;
        border-bottom: 2px solid transparent; margin-bottom: -1px; transition: color .15s, border-color .15s;
    }
    .sx-settings .sx-tab.active { color: var(--sales-1); border-bottom-color: var(--sales-1); }
    .sx-settings .sx-tab:hover:not(.active) { color: var(--text); }
    .sx-settings .sx-card { background: var(--surface); border: 1px solid var(--border); border-radius: 12px; padding: 20px; margin: 0 4px; }
    .sx-settings .sx-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
    .sx-settings .sx-field { margin-bottom: 14px; }
    .sx-settings .sx-field:last-child { margin-bottom: 0; }
    .sx-settings .sx-field label { display: block; font-size: 12px; font-weight: 600; color: var(--muted); margin-bottom: 5px; }
    .sx-settings .sx-field input, .sx-settings .sx-field textarea {
        width: 100%; border: 1px solid var(--border); border-radius: 8px; padding: 9px 12px;
        font-family: inherit; font-size: 14px; color: var(--text); background: var(--surface); transition: border-color .15s;
    }
    .sx-settings .sx-field input:focus, .sx-settings .sx-field textarea:focus { outline: none; border-color: var(--sales-1); }
    .sx-settings .sx-save {
        margin: 20px 4px 0; background: var(--sales-1); color: #fff; border: none; border-radius: 8px;
        padding: 11px 24px; font-family: inherit; font-size: 14px; font-weight: 600; cursor: pointer; transition: background .15s;
    }
    .sx-settings .sx-save:hover { background: var(--sales-2); }
    @media (max-width: 560px) {
        .sx-settings .sx-row { grid-template-columns: 1fr; }
    }
</style>

<script>
    document.querySelectorAll('#sxTabs .sx-tab').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.querySelectorAll('#sxTabs .sx-tab').forEach(function (b) { b.classList.remove('active'); });
            btn.classList.add('active');
            var target = btn.getAttribute('data-tab');
            document.querySelectorAll('.sx-panel').forEach(function (p) {
                p.hidden = p.getAttribute('data-panel') !== target;
            });
        });
    });
</script>
@endsection
