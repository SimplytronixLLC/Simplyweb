@extends('admin.includes.masterpage-admin')

@section('content')

<style>
    .sync-card { background: #fff; border-radius: 8px; border: 1px solid #e5e7eb; padding: 20px; margin-bottom: 20px; }
    .sync-metric { background: #f8fafc; border-radius: 6px; padding: 14px; text-align: center; }
    .sync-metric .val { font-size: 26px; font-weight: 600; color: #1e293b; }
    .sync-metric .lbl { font-size: 12px; color: #64748b; margin-top: 2px; }
    .log-box { background: #0f172a; color: #94a3b8; font-family: monospace; font-size: 12px;
               border-radius: 6px; padding: 14px; height: 280px; overflow-y: auto; }
    .log-ok   { color: #4ade80; }
    .log-warn { color: #facc15; }
    .log-err  { color: #f87171; }
    .progress-wrap { background: #e2e8f0; border-radius: 99px; height: 8px; overflow: hidden; }
    .progress-fill { height: 100%; background: #16a34a; border-radius: 99px; transition: width .4s; }
    .status-pill { display: inline-flex; align-items: center; gap: 6px;
                   font-size: 13px; font-weight: 500; padding: 4px 12px; border-radius: 99px; }
    .pill-idle    { background: #f1f5f9; color: #64748b; }
    .pill-running { background: #dcfce7; color: #15803d; }
    .pill-paused  { background: #fef9c3; color: #854d0e; }
    .dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; }
    .dot-idle    { background: #94a3b8; }
    .dot-running { background: #16a34a; animation: blink 1.2s infinite; }
    .dot-paused  { background: #ca8a04; }
    @keyframes blink { 0%,100%{opacity:1} 50%{opacity:.3} }
    .section-title { font-weight: 600; margin: 20px 0 10px; font-size: 14px; color: #666; text-transform: uppercase; }
</style>

<div class="container-fluid">

    <div class="section-title">DigiKey Specs Sync</div>

    {{-- Status bar --}}
    <div class="sync-card" style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
        <div style="display:flex; align-items:center; gap:12px;">
            <span class="status-pill pill-idle" id="status-pill">
                <span class="dot dot-idle" id="status-dot"></span>
                <span id="status-text">Idle</span>
            </span>
            <span style="font-size:13px; color:#94a3b8;" id="last-run-label">Last run: —</span>
        </div>
        <span style="font-size:13px; color:#64748b;">Rate: 1 req / 2s &nbsp;·&nbsp; ~27 min per 800</span>
    </div>

    {{-- Metrics --}}
    <div class="row mb-3">
        <div class="col-sm-3"><div class="sync-metric"><div class="val" id="m-checked">0</div><div class="lbl">Checked</div></div></div>
        <div class="col-sm-3"><div class="sync-metric"><div class="val" id="m-updated">0</div><div class="lbl">Updated</div></div></div>
        <div class="col-sm-3"><div class="sync-metric"><div class="val" id="m-failed">0</div><div class="lbl">Failed</div></div></div>
        <div class="col-sm-3"><div class="sync-metric"><div class="val" id="m-skipped">0</div><div class="lbl">Skipped</div></div></div>
    </div>

    {{-- Synced total + Progress --}}
    <div class="row mb-3">
        <div class="col-sm-4">
            <div class="sync-card" style="font-size:13px; color:#64748b; margin-bottom:0;">
                Total products with specs synced:
                <strong id="synced-total" style="color:#16a34a; font-size:16px;">—</strong>
            </div>
        </div>
        <div class="col-sm-8">
            <div class="sync-card" style="margin-bottom:0;">
                <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                    <span style="font-size:13px; font-weight:600;">Progress</span>
                    <span style="font-size:13px; color:#64748b;" id="progress-label">0 / 800</span>
                </div>
                <div class="progress-wrap"><div class="progress-fill" id="progress-bar" style="width:0%"></div></div>
                <div style="font-size:12px; color:#94a3b8; margin-top:6px;" id="last-id-label">Last saved ID: —</div>
            </div>
        </div>
    </div>

    {{-- Row 1: Controls + Log --}}
    <div class="row">

        {{-- Controls --}}
        <div class="col-sm-4">
            <div class="sync-card">
                <div style="font-size:14px; font-weight:600; margin-bottom:14px;">Run settings</div>

                <div style="margin-bottom:12px;">
                    <label style="font-size:13px; color:#64748b;">Daily limit</label>
                    <input type="number" id="limit-input" value="800" min="1" max="5000"
                           class="form-control form-control-sm mt-1" style="width:120px;">
                </div>

                <div style="display:flex; flex-direction:column; gap:8px; margin-top:16px;">
                    <button onclick="doRun()" id="btn-run"
                            style="padding:8px 16px; background:#16a34a; color:#fff; border:none; border-radius:6px; font-size:14px; cursor:pointer;">
                        ▶ Run sync now
                    </button>
                    <button onclick="doPause()" id="btn-pause"
                            style="padding:8px 16px; background:#ca8a04; color:#fff; border:none; border-radius:6px; font-size:14px; cursor:pointer;">
                        ⏸ Pause sync
                    </button>
                    <button onclick="doStop()"
                            style="padding:8px 16px; background:#dc2626; color:#fff; border:none; border-radius:6px; font-size:14px; cursor:pointer;">
                        ✕ Stop sync
                    </button>
                </div>
            </div>
        </div>

        {{-- Log --}}
        <div class="col-sm-8">
            <div class="sync-card">
                <div style="font-size:14px; font-weight:600; margin-bottom:10px;">Live log</div>
                <div class="log-box" id="log-box">
                    <div style="color:#475569;">No activity yet. Run the sync to see output here.</div>
                </div>
            </div>
        </div>

    </div>

    {{-- Row 2: Schedule + Last Run Summary --}}
    <div class="row">

        {{-- Schedule --}}
        <div class="col-sm-6">
            <div class="sync-card">
                <div style="font-size:14px; font-weight:600; margin-bottom:14px;">Auto-run schedule</div>

                <div style="margin-bottom:10px;">
                    <span id="sched-pill"
                          style="display:inline-block; font-size:12px; padding:3px 10px; border-radius:99px; background:#f1f5f9; color:#64748b;">
                        Not scheduled
                    </span>
                </div>

                <div style="margin-bottom:10px;">
                    <label style="font-size:13px; color:#64748b;">Run daily at (IST)</label>
                    <input type="time" id="sched-time" value="02:00"
                           style="display:block; margin-top:4px; padding:6px 10px; border:1px solid #e2e8f0; border-radius:6px; font-size:13px;">
                </div>

                <div style="margin-bottom:14px;">
                    <label style="font-size:13px; color:#64748b;">Limit</label>
                    <input type="number" id="sched-limit" value="800" min="1" max="5000"
                           style="display:block; margin-top:4px; width:100px; padding:6px 10px; border:1px solid #e2e8f0; border-radius:6px; font-size:13px;">
                </div>

                <div style="display:flex; gap:8px;">
                    <button onclick="setSchedule()"
                            style="padding:7px 14px; background:#16a34a; color:#fff; border:none; border-radius:6px; font-size:13px; cursor:pointer;">
                        Save schedule
                    </button>
                    <button onclick="clearSchedule()"
                            style="padding:7px 14px; background:#dc2626; color:#fff; border:none; border-radius:6px; font-size:13px; cursor:pointer;">
                        Clear
                    </button>
                </div>

                <div id="sched-next"
                     style="display:none; font-size:12px; color:#64748b; margin-top:10px; padding:8px 10px; background:#f8fafc; border-radius:6px;">
                </div>

                <p style="font-size:12px; color:#94a3b8; margin-top:10px; margin-bottom:0;">
                    Requires <code>* * * * * php artisan schedule:run</code> cron on the server.
                </p>
            </div>
        </div>

        {{-- Last Run Summary --}}
        <div class="col-sm-6">
            <div class="sync-card">
                <div style="font-size:14px; font-weight:600; margin-bottom:12px;">Last run summary</div>
                <table style="font-size:13px; width:100%;">
                    <tr><td style="color:#64748b; padding:6px 0; width:130px;">Started</td><td id="s-started">—</td></tr>
                    <tr><td style="color:#64748b; padding:6px 0;">Finished</td><td id="s-finished">—</td></tr>
                    <tr><td style="color:#64748b; padding:6px 0;">Termination</td><td id="s-term">—</td></tr>
                    <tr><td style="color:#64748b; padding:6px 0;">Last error</td><td id="s-err">None</td></tr>
                </table>
            </div>
        </div>

    </div>

</div>

@endsection

@section('footer')
<script>
const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
let prevRunning = false;
let prevPaused  = false;
let logOffset   = null;
let polling     = false;

function post(url, body = {}) {
    return fetch(url, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json' },
        body: JSON.stringify(body)
    }).then(r => r.json());
}

function doRun() {
    const limit = document.getElementById('limit-input').value || 800;
    post(`{{ route('admin.sync.run') }}?limit=${limit}`)
        .then(d => addLog(d.message || 'Run request sent.', d.success ? 'ok' : 'err'));
}

function doPause() {
    post('{{ route('admin.sync.pause') }}')
        .then(d => addLog(d.paused ? 'Sync paused.' : 'Sync resumed.', 'warn'));
}

function doStop() {
    if (!confirm('Send stop signal to the running sync?')) return;
    post('{{ route('admin.sync.stop') }}')
        .then(d => addLog('Stop signal sent.', 'err'));
}

function setSchedule() {
    const time  = document.getElementById('sched-time').value;
    const limit = document.getElementById('sched-limit').value;
    if (!time) return;
    post('{{ route('admin.sync.schedule') }}', { action: 'set', time, limit })
        .then(d => {
            addLog(d.message, d.success ? 'ok' : 'err');
            if (d.success) updateSchedPill(true, time, limit);
        });
}

function clearSchedule() {
    post('{{ route('admin.sync.schedule') }}', { action: 'clear' })
        .then(d => {
            addLog(d.message, 'warn');
            updateSchedPill(false);
        });
}

function updateSchedPill(active, time, limit) {
    const pill = document.getElementById('sched-pill');
    const next = document.getElementById('sched-next');
    if (active) {
        pill.style.background = '#dcfce7';
        pill.style.color = '#15803d';
        pill.textContent = 'Daily at ' + time + ' IST · ' + limit + ' products';
        const [h, m] = time.split(':').map(Number);
        const n = new Date();
        n.setHours(h, m, 0, 0);
        if (n <= new Date()) n.setDate(n.getDate() + 1);
        const diff = n - new Date();
        const hrs  = Math.floor(diff / 3600000);
        const mins = Math.floor((diff % 3600000) / 60000);
        next.style.display = 'block';
        next.textContent = 'Next run in ' + hrs + 'h ' + mins + 'm (' + n.toLocaleString() + ')';
    } else {
        pill.style.background = '#f1f5f9';
        pill.style.color = '#64748b';
        pill.textContent = 'Not scheduled';
        next.style.display = 'none';
    }
}

function addLog(msg, type = '') {
    const box = document.getElementById('log-box');
    if (box.children.length === 1 && box.children[0].style.color === '') box.innerHTML = '';
    const line = document.createElement('div');
    line.className = type ? 'log-' + type : '';
    line.textContent = '[' + new Date().toLocaleTimeString() + '] ' + msg;
    box.appendChild(line);
    while (box.children.length > 200) box.removeChild(box.firstChild);
    box.scrollTop = box.scrollHeight;
}

function setStatus(running, paused) {
    const pill = document.getElementById('status-pill');
    const dot  = document.getElementById('status-dot');
    const text = document.getElementById('status-text');

    pill.className = 'status-pill';
    dot.className  = 'dot';

    if (running && paused) {
        pill.classList.add('pill-paused');
        dot.classList.add('dot-paused');
        text.textContent = 'Paused';
        document.getElementById('btn-pause').textContent = '▶ Resume sync';
    } else if (running) {
        pill.classList.add('pill-running');
        dot.classList.add('dot-running');
        text.textContent = 'Running';
        document.getElementById('btn-pause').textContent = '⏸ Pause sync';
    } else {
        pill.classList.add('pill-idle');
        dot.classList.add('dot-idle');
        text.textContent = 'Idle';
        document.getElementById('btn-pause').textContent = '⏸ Pause sync';
    }
}

async function poll() {
    if (polling) return;
    polling = true;

    try {
        const statusRes = await fetch('{{ route('admin.sync.status') }}');
        const d = await statusRes.json();

        setStatus(d.running, d.paused);

        const stats   = d.stats || {};
        const checked = stats.checked || 0;
        const limit   = stats.limit   || 800;

        document.getElementById('synced-total').textContent = d.synced_total ?? '—';
        document.getElementById('m-checked').textContent = checked;
        document.getElementById('m-updated').textContent = stats.updated || 0;
        document.getElementById('m-failed').textContent  = stats.failed  || 0;
        document.getElementById('m-skipped').textContent = stats.skipped || 0;
        document.getElementById('progress-label').textContent = checked + ' / ' + limit;
        document.getElementById('progress-bar').style.width = Math.min(100, Math.round(checked / limit * 100)) + '%';
        document.getElementById('last-id-label').textContent = 'Last saved ID: ' + (d.last_id || '—');

        if (d.running && !prevRunning) addLog('Sync is running…', 'ok');
        if (!d.running && prevRunning) {
            addLog('Sync stopped.', 'warn');
            if (d.summary) {
                document.getElementById('last-run-label').textContent =
                    'Last run: ' + (d.summary.finished_at || '—');
            }
        }
        if (d.paused && !prevPaused) addLog('Sync paused.', 'warn');
        if (!d.paused && prevPaused && d.running) addLog('Sync resumed.', 'ok');

        prevRunning = d.running;
        prevPaused  = d.paused;

        if (d.summary) {
            document.getElementById('s-started').textContent  = d.summary.started_at  || '—';
            document.getElementById('s-finished').textContent = d.summary.finished_at || '—';
            document.getElementById('s-term').textContent     = d.summary.termination_type || '—';
            document.getElementById('s-err').textContent      = d.summary.last_error_code  || 'None';
        }

        if (d.schedule && d.schedule.time) {
            updateSchedPill(true, d.schedule.time, d.schedule.limit || 800);
            document.getElementById('sched-time').value  = d.schedule.time;
            document.getElementById('sched-limit').value = d.schedule.limit || 800;
        }

        if (d.running || prevRunning) {
            const logRes = await fetch('{{ route('admin.sync.log-tail') }}?offset=' + (logOffset ?? 'end'));
            const ld = await logRes.json();
            if (ld.lines && ld.lines.length) {
                ld.lines.forEach(line => {
                    if (!line.trim()) return;
                    const type = line.includes('[ERROR]') ? 'err'
                               : line.includes('[WARN]')  ? 'warn' : 'ok';
                    addLog(line, type);
                });
            }
            logOffset = ld.offset ?? logOffset;
        }

    } catch(e) {
        // silent fail
    } finally {
        polling = false;
    }
}

let pollInterval = setInterval(poll, 5000);

document.addEventListener('visibilitychange', function() {
    clearInterval(pollInterval);
    if (!document.hidden) {
        poll();
        pollInterval = setInterval(poll, 5000);
    }
});

poll();
</script>
@endsection