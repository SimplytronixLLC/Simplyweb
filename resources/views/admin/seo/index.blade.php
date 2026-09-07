@extends('admin.includes.masterpage-admin')

@section('content')
<style>
    .seo-card { background:#fff; border-radius:8px; border:1px solid #e5e7eb; padding:20px; margin-bottom:20px; }
    .seo-card-header { font-size:14px; font-weight:600; color:#1e293b; margin-bottom:16px; padding-bottom:10px; border-bottom:1px solid #f1f5f9; display:flex; justify-content:space-between; align-items:center; }
    .field-btn { padding:4px 10px; border-radius:5px; border:1px solid #e2e8f0; background:#f8fafc; font-size:12px; cursor:pointer; color:#374151; margin:3px 2px; }
    .field-btn:hover { background:#1e293b; color:#fff; border-color:#1e293b; }
    .char-bar { height:5px; border-radius:99px; background:#e2e8f0; margin-top:4px; overflow:hidden; }
    .char-fill { height:100%; border-radius:99px; transition:width .2s, background .2s; }
    .serp-box { background:#fff; border:1px solid #e2e8f0; border-radius:8px; padding:16px; font-family: arial,sans-serif; }
    .serp-url { font-size:13px; color:#4d5156; margin:2px 0 3px; }
    .serp-title { font-size:20px; color:#1a0dab; line-height:1.3; cursor:pointer; }
    .serp-title:hover { text-decoration:underline; }
    .serp-desc { font-size:14px; color:#4d5156; line-height:1.5; margin-top:4px; }
    .serp-breadcrumb { font-size:12px; color:#4d5156; margin-bottom:2px; }
    .stat-pill { display:inline-flex; align-items:center; gap:5px; padding:4px 10px; border-radius:99px; font-size:12px; font-weight:500; }
    .pill-ok   { background:#dcfce7; color:#15803d; }
    .pill-warn { background:#fef9c3; color:#854d0e; }
    .pill-bad  { background:#fee2e2; color:#dc2626; }
    .template-stat { text-align:center; padding:10px; background:#f8fafc; border-radius:6px; }
    .template-stat .val { font-size:20px; font-weight:700; }
    .template-stat .lbl { font-size:11px; color:#64748b; margin-top:2px; }
    label { font-size:13px; color:#64748b; font-weight:500; margin-bottom:4px; display:block; }
    .form-control { font-size:13px; }
    .alert-success { font-size:13px; }
</style>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 style="margin:0">SEO Tools</h4>
</div>

@if(session('message'))
<div class="alert alert-success">{{ session('message') }}</div>
@endif

<div class="row">

    {{-- LEFT COLUMN --}}
    <div class="col-lg-7">

        {{-- Global SEO --}}
        <div class="seo-card">
            <div class="seo-card-header">
                Global SEO
            </div>
            <form method="POST" action="{{ route('admin.seo.saveGlobal') }}">
                @csrf
                <div class="mb-3">
                    <label>Site Title</label>
                    <input name="site_title" class="form-control" value="{{ $seo->site_title ?? '' }}"
                           id="global-title" oninput="updateGlobalCounter(this,'gtc',60)">
                    <div style="display:flex;justify-content:space-between;margin-top:4px">
                        <div class="char-bar" style="flex:1;margin-right:10px">
                            <div class="char-fill" id="gtc-bar" style="width:0%;background:#16a34a"></div>
                        </div>
                        <span style="font-size:11px;color:#94a3b8" id="gtc">0/60</span>
                    </div>
                </div>
                <div class="mb-3">
                    <label>Site Description</label>
                    <textarea name="site_description" class="form-control" rows="2"
                              id="global-desc" oninput="updateGlobalCounter(this,'gdc',160)">{{ $seo->site_description ?? '' }}</textarea>
                    <div style="display:flex;justify-content:space-between;margin-top:4px">
                        <div class="char-bar" style="flex:1;margin-right:10px">
                            <div class="char-fill" id="gdc-bar" style="width:0%;background:#16a34a"></div>
                        </div>
                        <span style="font-size:11px;color:#94a3b8" id="gdc">0/160</span>
                    </div>
                </div>
                <button class="btn btn-primary btn-sm">Save Global SEO</button>
            </form>
        </div>

        {{-- Template Builder --}}
        <div class="seo-card">
            <div class="seo-card-header">
                SEO Template Builder
                <button onclick="if(confirm('Reset template?')) document.getElementById('reset-form').submit();"
                        style="padding:4px 10px;background:#fee2e2;color:#dc2626;border:1px solid #fecaca;border-radius:5px;font-size:12px;cursor:pointer">
                    Reset
                </button>
                <form id="reset-form" method="POST" action="{{ route('admin.seo.resetTemplate') }}" style="display:none">@csrf</form>
            </div>

            <div class="mb-3">
                <div style="font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">Insert into Title:</div>
                @foreach(['manufacturer','mpn','category','price','stock','rohs','lifecycle'] as $f)
                <button type="button" class="field-btn insert-btn" data-target="title" data-field="{{ $f }}">
                    {{ strtoupper($f) }}
                </button>
                @endforeach
            </div>

            <div class="mb-3">
                <div style="font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">Insert into Description:</div>
                @foreach(['manufacturer','mpn','category','digikey_desc','rohs','stock','price','description'] as $f)
                <button type="button" class="field-btn insert-btn" data-target="desc" data-field="{{ $f }}">
                    {{ strtoupper($f) }}
                </button>
                @endforeach
            </div>

            <form method="POST" action="{{ route('admin.seo.saveTemplate') }}">
                @csrf

                <div class="mb-3">
                    <label>Title Template <span style="color:#94a3b8;font-weight:400">(recommended: 50–60 chars)</span></label>
                    <input type="text" name="seo_title_template" id="title_template"
                           class="form-control" value="{{ $seo->seo_title_template ?? '' }}"
                           oninput="updateCounter(this,'tc',60); render()">
                    <div style="display:flex;justify-content:space-between;margin-top:4px">
                        <div class="char-bar" style="flex:1;margin-right:10px">
                            <div class="char-fill" id="tc-bar" style="width:0%"></div>
                        </div>
                        <span style="font-size:11px;color:#94a3b8" id="tc">0/60</span>
                    </div>
                    <div style="font-size:11px;color:#94a3b8;margin-top:3px">
                        Resolved length with current preview product: <strong id="title-resolved-len">—</strong> chars
                    </div>
                </div>

                <div class="mb-3">
                    <label>Description Template <span style="color:#94a3b8;font-weight:400">(recommended: 120–160 chars)</span></label>
                    <textarea name="seo_description_template" id="desc_template"
                              class="form-control" rows="3"
                              oninput="updateCounter(this,'dc',160); render()">{{ $seo->seo_description_template ?? '' }}</textarea>
                    <div style="display:flex;justify-content:space-between;margin-top:4px">
                        <div class="char-bar" style="flex:1;margin-right:10px">
                            <div class="char-fill" id="dc-bar" style="width:0%"></div>
                        </div>
                        <span style="font-size:11px;color:#94a3b8" id="dc">0/160</span>
                    </div>
                    <div style="font-size:11px;color:#94a3b8;margin-top:3px">
                        Resolved length with current preview product: <strong id="desc-resolved-len">—</strong> chars
                    </div>
                </div>

                <button class="btn btn-success btn-sm">Save Template</button>
            </form>
        </div>

        {{-- Template Analysis --}}
        @if($templateStats)
        <div class="seo-card">
            <div class="seo-card-header">
                Template Analysis
                <span style="font-size:12px;color:#94a3b8;font-weight:400">Based on {{ $templateStats['sample'] }} sampled products</span>
            </div>

            <div class="row mb-3">
                <div class="col-sm-4">
                    <div class="template-stat">
                        <div class="val" style="color:{{ $templateStats['title_ok_pct'] >= 70 ? '#16a34a' : '#dc2626' }}">
                            {{ $templateStats['title_ok_pct'] }}%
                        </div>
                        <div class="lbl">Titles 20–60 chars</div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="template-stat">
                        <div class="val" style="color:{{ $templateStats['title_over'] <= 10 ? '#16a34a' : '#dc2626' }}">
                            {{ $templateStats['title_over'] }}%
                        </div>
                        <div class="lbl">Titles over 60</div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="template-stat">
                        <div class="val" style="color:{{ $templateStats['title_under'] <= 10 ? '#16a34a' : '#ca8a04' }}">
                            {{ $templateStats['title_under'] }}%
                        </div>
                        <div class="lbl">Titles under 20</div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-4">
                    <div class="template-stat">
                        <div class="val" style="color:{{ $templateStats['desc_ok_pct'] >= 70 ? '#16a34a' : '#dc2626' }}">
                            {{ $templateStats['desc_ok_pct'] }}%
                        </div>
                        <div class="lbl">Descs 50–160 chars</div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="template-stat">
                        <div class="val" style="color:{{ $templateStats['desc_over'] <= 10 ? '#16a34a' : '#dc2626' }}">
                            {{ $templateStats['desc_over'] }}%
                        </div>
                        <div class="lbl">Descs over 160</div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="template-stat">
                        <div class="val" style="color:{{ $templateStats['desc_under'] <= 10 ? '#16a34a' : '#ca8a04' }}">
                            {{ $templateStats['desc_under'] }}%
                        </div>
                        <div class="lbl">Descs under 50</div>
                    </div>
                </div>
            </div>
        </div>
        @endif

    </div>

    {{-- RIGHT COLUMN --}}
    <div class="col-lg-5">

        {{-- Product Switcher --}}
        <div class="seo-card">
            <div class="seo-card-header">Preview Product</div>

            <div style="display:flex;gap:8px;margin-bottom:12px">
                <input type="text" id="mpn-search" placeholder="Enter part number..."
                       style="flex:1;padding:7px 10px;border:1px solid #e2e8f0;border-radius:6px;font-size:13px">
                <button onclick="loadPreview()"
                        style="padding:7px 14px;background:#1e293b;color:#fff;border:none;border-radius:6px;font-size:13px;cursor:pointer">
                    Load
                </button>
            </div>

            <div style="font-size:12px;color:#94a3b8;margin-bottom:8px">Recent products:</div>
            <div style="display:flex;flex-wrap:wrap;gap:5px">
                @foreach($recentProducts as $key)
                <button onclick="quickLoad('{{ $key }}')"
                        style="padding:3px 8px;border:1px solid #e2e8f0;border-radius:4px;background:#f8fafc;font-size:11px;cursor:pointer;color:#374151">
                    {{ $key }}
                </button>
                @endforeach
            </div>
        </div>

        {{-- SERP Preview --}}
        <div class="seo-card">
            <div class="seo-card-header">Google SERP Preview</div>

            <div class="serp-box">
                <div class="serp-breadcrumb">
                    simplytronix.com ›
                    <span id="serp-mf" style="color:#4d5156">product</span> ›
                    <span id="serp-mpn-bc" style="color:#4d5156">—</span>
                </div>
                <div class="serp-title" id="serp-title">—</div>
                <div class="serp-url">simplytronix.com/product/...</div>
                <div class="serp-desc" id="serp-desc">—</div>
            </div>

            <div style="margin-top:12px">
                <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:3px">
                    <span style="color:#64748b">Title</span>
                    <span id="serp-title-status"></span>
                </div>
                <div class="char-bar">
                    <div class="char-fill" id="serp-title-bar" style="width:0%"></div>
                </div>
                <div style="display:flex;justify-content:space-between;font-size:12px;margin-top:8px;margin-bottom:3px">
                    <span style="color:#64748b">Description</span>
                    <span id="serp-desc-status"></span>
                </div>
                <div class="char-bar">
                    <div class="char-fill" id="serp-desc-bar" style="width:0%"></div>
                </div>
            </div>
        </div>

        {{-- Product Data Panel --}}
        <div class="seo-card" id="product-data-panel" style="{{ $preview ? '' : 'display:none' }}">
            <div class="seo-card-header">Current Preview Data</div>
            <table style="width:100%;font-size:12px;border-collapse:collapse">
                @foreach(['manufacturer','mpn','category','price','stock','rohs','lifecycle','description'] as $field)
                <tr style="border-bottom:1px solid #f1f5f9">
                    <td style="padding:5px 0;color:#64748b;width:110px">{{ $field }}</td>
                    <td style="padding:5px 0;color:#1e293b;word-break:break-all" id="pd-{{ $field }}">
                        {{ $preview[$field] ?? '—' }}
                    </td>
                </tr>
                @endforeach
            </table>
        </div>

    </div>

</div>

@endsection

@section('footer')
<script>
let map = @json($preview ?? []);

// ── Character counters ─────────────────────────────────────────────────────

function updateCounter(el, id, max) {
    const len = el.value.length;
    const pct = Math.min(100, len / max * 100);
    const color = len > max ? '#dc2626' : (len >= max * 0.8 ? '#16a34a' : '#ca8a04');
    document.getElementById(id).textContent = len + '/' + max;
    document.getElementById(id + '-bar').style.width  = pct + '%';
    document.getElementById(id + '-bar').style.background = color;
}

function updateGlobalCounter(el, id, max) {
    updateCounter(el, id, max);
}

// ── Render SERP preview ───────────────────────────────────────────────────

function resolve(template) {
    let out = template;
    for (let k in map) {
        out = out.replaceAll('{' + k + '}', map[k] ?? '');
    }
    return out.replace(/\s+/g, ' ').trim();
}

function barColor(len, min, max) {
    if (len > max)  return '#dc2626';
    if (len >= min) return '#16a34a';
    return '#ca8a04';
}

function render() {
    const titleTpl = document.getElementById('title_template').value;
    const descTpl  = document.getElementById('desc_template').value;

    const title = resolve(titleTpl);
    const desc  = resolve(descTpl);

    // SERP box
    document.getElementById('serp-title').textContent = title || '—';
    document.getElementById('serp-desc').textContent  = desc  || '—';
    document.getElementById('serp-mf').textContent    = (map.manufacturer || 'product').toLowerCase().replace(/\s+/g,'-');
    document.getElementById('serp-mpn-bc').textContent = map.mpn || '—';

    // Title bar
    const tLen = title.length;
    document.getElementById('serp-title-bar').style.width      = Math.min(100, tLen / 60 * 100) + '%';
    document.getElementById('serp-title-bar').style.background = barColor(tLen, 30, 60);
    document.getElementById('serp-title-status').textContent   = tLen + ' chars ' + (tLen > 60 ? '⚠ too long' : tLen < 20 ? '⚠ too short' : '✓ good');
    document.getElementById('serp-title-status').style.color   = barColor(tLen, 20, 60);

    // Desc bar
    const dLen = desc.length;
    document.getElementById('serp-desc-bar').style.width      = Math.min(100, dLen / 160 * 100) + '%';
    document.getElementById('serp-desc-bar').style.background = barColor(dLen, 50, 160);
    document.getElementById('serp-desc-status').textContent   = dLen + ' chars ' + (dLen > 160 ? '⚠ too long' : dLen < 50 ? '⚠ too short' : '✓ good');
    document.getElementById('serp-desc-status').style.color   = barColor(dLen, 50, 160);

    // Resolved length hints
    document.getElementById('title-resolved-len').textContent = tLen;
    document.getElementById('title-resolved-len').style.color = barColor(tLen, 20, 60);
    document.getElementById('desc-resolved-len').textContent  = dLen;
    document.getElementById('desc-resolved-len').style.color  = barColor(dLen, 50, 160);

    // Template counters (template length, not resolved)
    updateCounter(document.getElementById('title_template'), 'tc', 60);
    updateCounter(document.getElementById('desc_template'),  'dc', 160);
}

// ── Insert field buttons ──────────────────────────────────────────────────

document.querySelectorAll('.insert-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        const field  = this.dataset.field;
        const target = this.dataset.target;
        const el     = document.getElementById(target === 'title' ? 'title_template' : 'desc_template');
        const token  = '{' + field + '}';
        if (!el.value.includes(token)) {
            const pos = el.selectionStart ?? el.value.length;
            el.value = el.value.slice(0, pos) + ' ' + token + el.value.slice(pos);
        }
        render();
        el.focus();
    });
});

// ── Live preview AJAX ─────────────────────────────────────────────────────

function loadPreview() {
    const mpn = document.getElementById('mpn-search').value.trim();
    if (!mpn) return;
    fetchPreview(mpn);
}

function quickLoad(mpn) {
    document.getElementById('mpn-search').value = mpn;
    fetchPreview(mpn);
}

function fetchPreview(mpn) {
    fetch('{{ route('admin.seo.previewAjax') }}?mpn=' + encodeURIComponent(mpn))
        .then(r => r.json())
        .then(data => {
            if (data.error) { alert(data.error); return; }
            map = data;
            render();
            // Update product data panel
            document.getElementById('product-data-panel').style.display = 'block';
            ['manufacturer','mpn','category','price','stock','rohs','lifecycle','description'].forEach(f => {
                const el = document.getElementById('pd-' + f);
                if (el) el.textContent = data[f] || '—';
            });
        });
}

// ── Init ──────────────────────────────────────────────────────────────────

render();
updateGlobalCounter(document.getElementById('global-title'), 'gtc', 60);
updateGlobalCounter(document.getElementById('global-desc'),  'gdc', 160);
</script>
@endsection