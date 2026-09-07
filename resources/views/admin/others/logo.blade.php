@extends('admin.includes.masterpage-admin')

@section('content')
<div class="sx-settings">
    <div class="sx-eyebrow"><span class="sx-dot sx-dot--sales"></span> GENERAL OPTIONS</div>
    <h1 class="sx-title">Logo &amp; Branding</h1>

    @if(Session::has('message'))
        <div class="sx-alert sx-alert--ok">{{ Session::get('message') }}</div>
    @endif

    <form method="POST" action="{{ url('admin/update_logo') }}" enctype="multipart/form-data">
        @csrf

        <div class="sx-logo-grid">
            <div class="sx-card sx-logo-card">
                <div class="sx-logo-eyebrow"><span class="sx-dot sx-dot--sales"></span> HEADER LOGO</div>
                <div class="sx-logo-preview">
                    @if($data->logo)
                        <img id="preview-logo" src="{{ url('/') }}/public/uploads/{{ $data->logo }}">
                    @else
                        <img id="preview-logo" src="{{ url('public/uploads/no-image.png') }}">
                    @endif
                </div>
                <input class="sx-file-input" accept="image/*" id="file-logo" name="logo" type="file" data-preview="preview-logo" data-label="label-logo">
                <button type="button" class="sx-upload-btn" data-target="file-logo">
                    <span id="label-logo">Change Header Logo</span>
                </button>
            </div>

            <div class="sx-card sx-logo-card">
                <div class="sx-logo-eyebrow"><span class="sx-dot sx-dot--content"></span> FOOTER LOGO</div>
                <div class="sx-logo-preview">
                    @if($data->footer_logo)
                        <img id="preview-footer_logo" src="{{ url('/') }}/public/uploads/{{ $data->footer_logo }}">
                    @else
                        <img id="preview-footer_logo" src="{{ url('public/uploads/no-image.png') }}">
                    @endif
                </div>
                <input class="sx-file-input" accept="image/*" id="file-footer_logo" name="footer_logo" type="file" data-preview="preview-footer_logo" data-label="label-footer_logo">
                <button type="button" class="sx-upload-btn" data-target="file-footer_logo">
                    <span id="label-footer_logo">Change Footer Logo</span>
                </button>
            </div>

            <div class="sx-card sx-logo-card">
                <div class="sx-logo-eyebrow"><span class="sx-dot sx-dot--system"></span> FAVICON</div>
                <div class="sx-logo-preview">
                    @if($data->icon)
                        <img id="preview-icon" src="{{ url('/') }}/public/uploads/{{ $data->icon }}">
                    @else
                        <img id="preview-icon" src="{{ url('public/uploads/no-image.png') }}">
                    @endif
                </div>
                <input class="sx-file-input" accept="image/*" id="file-icon" name="icon" type="file" data-preview="preview-icon" data-label="label-icon">
                <button type="button" class="sx-upload-btn" data-target="file-icon">
                    <span id="label-icon">Change Favicon</span>
                </button>
            </div>
        </div>

        <button type="submit" class="sx-save">Update Logo</button>
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
        max-width: 900px;
    }
    .sx-settings .sx-eyebrow {
        display: flex; align-items: center; gap: 8px;
        font-weight: 600; font-size: 12px; letter-spacing: .06em;
        color: var(--muted); margin: 22px 4px 6px;
    }
    .sx-settings .sx-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; flex-shrink: 0; }
    .sx-settings .sx-dot--sales { background: var(--sales-1); }
    .sx-settings .sx-dot--content { background: var(--content-1); }
    .sx-settings .sx-dot--system { background: var(--system-1); }
    .sx-settings .sx-title { font-size: 22px; font-weight: 700; color: var(--text); margin: 0 4px 20px; }
    .sx-settings .sx-alert { margin: 0 4px 16px; padding: 10px 14px; border-radius: 8px; font-size: 14px; font-weight: 500; }
    .sx-settings .sx-alert--ok { background: rgba(22,163,74,.1); color: var(--ok); border: 1px solid rgba(22,163,74,.25); }
    .sx-settings .sx-card { background: var(--surface); border: 1px solid var(--border); border-radius: 12px; padding: 18px; }
    .sx-logo-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin: 0 4px;
    }
    .sx-logo-eyebrow {
        display: flex; align-items: center; gap: 7px;
        font-weight: 600; font-size: 11px; letter-spacing: .05em;
        color: var(--muted); margin-bottom: 12px;
    }
    .sx-logo-preview {
        background: var(--bg);
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 110px;
        margin-bottom: 12px;
    }
    .sx-logo-preview img { max-height: 90px; max-width: 100%; object-fit: contain; }
    .sx-file-input { display: none; }
    .sx-upload-btn {
        width: 100%;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 9px 12px;
        font-family: inherit;
        font-size: 13px;
        font-weight: 600;
        color: var(--sales-1);
        cursor: pointer;
        transition: background .15s, border-color .15s;
    }
    .sx-upload-btn:hover { background: var(--bg); border-color: var(--sales-1); }
    .sx-settings .sx-save {
        margin: 20px 4px 0;
        background: var(--sales-1); color: #fff; border: none; border-radius: 8px;
        padding: 11px 24px; font-family: inherit; font-size: 14px; font-weight: 600;
        cursor: pointer; transition: background .15s;
    }
    .sx-settings .sx-save:hover { background: var(--sales-2); }
    @media (max-width: 720px) {
        .sx-logo-grid { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('footer')
<script>
    document.querySelectorAll('.sx-upload-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById(btn.getAttribute('data-target')).click();
        });
    });

    document.querySelectorAll('.sx-file-input').forEach(function (input) {
        input.addEventListener('change', function () {
            var previewId = input.getAttribute('data-preview');
            var labelId = input.getAttribute('data-label');
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById(previewId).src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
                document.getElementById(labelId).textContent = input.files[0].name;
            }
        });
    });
</script>
@endsection
