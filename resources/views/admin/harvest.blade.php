@extends('admin.includes.masterpage-admin')

@section('content')
<div class="container-fluid">
    <div class="section-title" style="font-weight:600;margin:20px 0 10px;font-size:14px;color:#666;text-transform:uppercase;">
        Mouser Harvest Control
    </div>

    <div class="card" style="max-width:420px;">
        <div class="card-body">
            <button id="btn-start" style="padding:6px 14px;border:none;background:#16a34a;color:#fff;border-radius:4px;">
                Start
            </button>
            <button id="btn-stop" style="padding:6px 14px;border:none;background:#dc2626;color:#fff;border-radius:4px;">
                Stop
            </button>

            <div style="margin-top:14px;font-size:13px;line-height:1.8;">
                <div>Status: <b id="h-status">Idle</b></div>
                <div>Keyword: <b id="h-key">-</b></div>
                <div>Offset: <b id="h-offset">0</b></div>
                <div>API Used: <b id="h-api">0</b></div>
                <div>Fetched: <b id="h-fetched">0</b></div>
                <div>Inserted: <b id="h-inserted">0</b></div>
                <div>Stop Reason: <b id="h-stopreason">-</b></div>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const baseUrl = "{{ url('admin/harvest') }}";
    let pollTimer = null;

    function post(path) {
        return fetch(baseUrl + path, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        }).then(r => r.json());
    }

    function getStatus() {
        return fetch(baseUrl + '/status', {
            headers: { 'Accept': 'application/json' }
        }).then(r => r.json());
    }

    function renderStatus(data) {
        if (!data) return;
        document.getElementById('h-status').innerText = data.is_running ? 'Running' : 'Idle';
        document.getElementById('h-key').innerText = data.current_keyword ?? '-';
        document.getElementById('h-offset').innerText = data.current_offset ?? 0;
        document.getElementById('h-api').innerText = data.api_used ?? 0;
        document.getElementById('h-fetched').innerText = data.total_fetched ?? 0;
        document.getElementById('h-inserted').innerText = data.total_inserted ?? 0;
        document.getElementById('h-stopreason').innerText = data.stop_reason ?? '-';
    }

    function startPolling() {
        if (pollTimer) clearInterval(pollTimer);
        pollTimer = setInterval(() => {
            getStatus().then(data => {
                renderStatus(data);
                if (!data || !data.is_running) {
                    clearInterval(pollTimer);
                    pollTimer = null;
                }
            }).catch(err => console.error('status poll failed', err));
        }, 3000);
    }

    document.getElementById('btn-start').addEventListener('click', function() {
        document.getElementById('h-status').innerText = 'Starting...';
        post('/start').then(() => startPolling()).catch(err => console.error('start failed', err));
    });

    document.getElementById('btn-stop').addEventListener('click', function() {
        document.getElementById('h-status').innerText = 'Stopping...';
        post('/stop').catch(err => console.error('stop failed', err));
    });

    // On page load, check if it's already running and resume polling
    getStatus().then(data => {
        renderStatus(data);
        if (data && data.is_running) startPolling();
    }).catch(() => {});
})();
</script>
@endsection
