@extends('admin.includes.masterpage-admin')

@section('content')

@php
    $stageColors = [
        'new'       => '#2F6FED',
        'contacted' => '#00B884',
        'quoted'    => '#7C5CFC',
        'won'       => '#16A34A',
        'lost'      => '#DC2626',
    ];
    $stageColorsSoft = [
        'new'       => '#EAF0FE',
        'contacted' => '#E5FBF4',
        'quoted'    => '#F1EDFE',
        'won'       => '#EAFBF1',
        'lost'      => '#FDEDED',
    ];
    $avatarPalette = ['#2F6FED', '#00B884', '#7C5CFC', '#D97706', '#DC2626', '#0891B2', '#DB2777'];

    $counts = collect($stages)->mapWithKeys(function ($label, $key) use ($contactsByStage) {
        return [$key => isset($contactsByStage[$key]) ? $contactsByStage[$key]->count() : 0];
    });
    $total = $counts->sum();
    $wonCount = $counts['won'] ?? 0;
    $lostCount = $counts['lost'] ?? 0;
    $closedCount = $wonCount + $lostCount;
    $winRate = $closedCount > 0 ? round($wonCount / $closedCount * 100) : 0;
    $wonThisMonth = isset($contactsByStage['won'])
        ? $contactsByStage['won']->filter(fn($c) => $c->updated_at && $c->updated_at->isCurrentMonth())->count()
        : 0;
    $manualActionLabels = [
        'none' => 'No action yet',
        'rfq_received' => 'RFQ Received',
        'quote_sent' => 'Quote Sent',
        'quote_signed' => 'Quote Signed',
        'quote_unsigned' => 'Quote Unsigned',
        'no_quote' => 'No Quote',
        'invalid_rfq' => 'Invalid RFQ',
    ];

    // Initials + a stable color for the avatar circle on each card.
    if (!function_exists('sx_initials')) {
        function sx_initials($name) {
            $name = trim((string) $name);
            if ($name === '') return '?';
            $parts = preg_split('/\s+/', $name);
            $initials = mb_substr($parts[0], 0, 1);
            if (count($parts) > 1) $initials .= mb_substr($parts[count($parts) - 1], 0, 1);
            return mb_strtoupper($initials);
        }
    }
@endphp

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@500;600&display=swap" rel="stylesheet">

<style>
.sxp {
    --ink: #0B1220;
    --paper: #F5F7FA;
    --surface: #FFFFFF;
    --accent: #2F6FED;
    --accent-dark: #1E4FC4;
    --signal: #00B884;
    --muted: #64748B;
    --line: #E3E7EE;
    --danger: #DC2626;
    font-family: 'Inter', system-ui, sans-serif;
    color: var(--ink);
}
.sxp *{ box-sizing: border-box; }

.sxp-eyebrow{
    display:inline-flex; align-items:center; gap:7px;
    font-family:'IBM Plex Mono', monospace;
    font-size:11px; font-weight:600; letter-spacing:.1em;
    color:var(--accent-dark);
    background:#EAF0FE;
    border:1px solid #D6E2FC;
    padding:4px 10px; border-radius:4px;
    margin-bottom:10px;
}
.sxp-eyebrow::before{
    content:""; width:6px; height:6px; background:var(--accent); border-radius:50%;
    display:inline-block;
}
.sxp-header{ display:flex; align-items:flex-end; justify-content:space-between; flex-wrap:wrap; gap:16px; margin-bottom:20px; }
.sxp-header h1{
    font-family:'Space Grotesk', sans-serif;
    font-size:clamp(22px, 2.6vw, 28px);
    font-weight:700; line-height:1.15; margin:0 0 4px;
}
.sxp-header p{ font-size:13.5px; color:var(--muted); margin:0; max-width:520px; }
.sxp-pins{ display:flex; gap:5px; }
.sxp-pins span{ width:7px; height:9px; background:var(--ink); opacity:.12; border-radius:1px 1px 0 0; }

/* Metrics strip */
.sxp-metrics{ display:flex; gap:12px; flex-wrap:wrap; margin-bottom:18px; }
.sxp-metric{
    background:var(--surface); border:1px solid var(--line); border-radius:10px;
    padding:14px 18px; flex:1 1 130px; min-width:120px;
    display:flex; flex-direction:column; gap:2px;
}
.sxp-metric-total{ background:var(--ink); border-color:var(--ink); }
.sxp-metric-total .sxp-metric-num{ color:#fff; }
.sxp-metric-total .sxp-metric-label{ color:rgba(255,255,255,.65); }
.sxp-metric-num{ font-family:'Space Grotesk', sans-serif; font-size:24px; font-weight:700; line-height:1; }
.sxp-metric-label{
    font-family:'IBM Plex Mono', monospace; font-size:10px; font-weight:600;
    letter-spacing:.06em; text-transform:uppercase; color:var(--muted); margin-top:4px;
}

.sxp-insights{ display:flex; gap:12px; margin-bottom:22px; flex-wrap:wrap; }
.sxp-insight{
    flex:1 1 220px; border-radius:10px; padding:16px 18px;
    display:flex; align-items:center; justify-content:space-between; gap:10px;
}
.sxp-insight-rate{ background:linear-gradient(135deg,#FFF7E6,#FFECC7); }
.sxp-insight-won{ background:linear-gradient(135deg,#E9FBF1,#D3F7E1); }
.sxp-insight-num{ font-family:'Space Grotesk', sans-serif; font-size:22px; font-weight:700; }
.sxp-insight-label{ font-family:'IBM Plex Mono', monospace; font-size:10.5px; font-weight:600; letter-spacing:.05em; text-transform:uppercase; }
.sxp-insight-rate .sxp-insight-label{ color:#92400E; }
.sxp-insight-rate .sxp-insight-num{ color:#78350F; }
.sxp-insight-won .sxp-insight-label{ color:#166534; }
.sxp-insight-won .sxp-insight-num{ color:#14532D; }
.sxp-insight-icon{ font-size:26px; opacity:.9; }

/* Board card wrapper */
.sxp-board-card{ background:var(--surface); border:1px solid var(--line); border-radius:12px; overflow:hidden; }
.sxp-board-head{
    padding:16px 20px; border-bottom:1px solid var(--line);
    display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;
}
.sxp-board-title{ font-family:'Space Grotesk', sans-serif; font-size:16px; font-weight:600; margin:0; }
.sxp-board-sub{ font-size:12px; color:var(--muted); margin:2px 0 0; }
.sxp-toolbar{ display:flex; gap:8px; }
.sxp-input{
    height:34px; padding:0 12px; border:1px solid var(--line); border-radius:6px;
    font-size:13px; font-family:'Inter', sans-serif; color:var(--ink); background:var(--paper);
}
.sxp-input:focus{ outline:none; border-color:var(--accent); background:var(--surface); }

/* Columns */
.pipeline-board{ display:flex; gap:14px; overflow-x:auto; padding:18px 20px 20px; background:var(--paper); }
.pipeline-column{
    flex:0 0 270px; background:var(--surface); border-radius:10px; padding:0;
    height:74vh; display:flex; flex-direction:column;
    border:1px solid var(--line); border-top:3px solid var(--stage-color, var(--accent));
    overflow:hidden;
}
.sxp-col-head{
    padding:12px 14px; display:flex; align-items:center; justify-content:space-between;
    background:var(--stage-soft, var(--paper));
}
.sxp-col-label{
    font-family:'IBM Plex Mono', monospace; font-size:11px; font-weight:600;
    letter-spacing:.06em; text-transform:uppercase; color:var(--ink);
}
.sxp-col-count{
    font-family:'IBM Plex Mono', monospace; font-size:11px; font-weight:700;
    background:var(--stage-color, var(--accent)); color:#fff;
    border-radius:20px; padding:2px 8px; min-width:22px; text-align:center;
}
.pipeline-cards{ flex:1; overflow-y:auto; min-height:0; padding:10px; }
.pipeline-cards::-webkit-scrollbar{ width:6px; }
.pipeline-cards::-webkit-scrollbar-thumb{ background:#D8DEE8; border-radius:6px; }

.pipeline-card{
    background:var(--surface); border:1px solid var(--line); border-radius:8px;
    padding:11px 12px; margin-bottom:8px; cursor:grab;
    box-shadow:0 1px 2px rgba(15,23,42,0.04);
    transition:transform .12s ease, box-shadow .12s ease, border-color .12s ease;
    display:flex; gap:10px;
}
.pipeline-column.drag-over{ background:#EEF3FC !important; }
.pipeline-card:active{ cursor:grabbing; }
.pipeline-card:hover{ transform:translateY(-2px); box-shadow:0 4px 12px rgba(15,23,42,0.09); border-color:#CBD5E1; }

.sxp-avatar{
    flex:0 0 32px; width:32px; height:32px; border-radius:8px;
    display:flex; align-items:center; justify-content:center;
    color:#fff; font-family:'Space Grotesk', sans-serif; font-weight:700; font-size:12px;
}
.sxp-card-body{ flex:1; min-width:0; }
.sxp-card-name{ font-weight:600; font-size:13.5px; color:var(--ink); line-height:1.3; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.sxp-card-company{ font-size:11.5px; color:var(--muted); margin-top:1px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.sxp-card-badges{ display:flex; flex-wrap:wrap; gap:4px; margin-top:7px; }
.sxp-badge{
    font-family:'IBM Plex Mono', monospace; font-size:9px; font-weight:600; letter-spacing:.02em;
    padding:2px 6px; border-radius:4px; color:#fff; display:inline-block;
}
.sxp-card-updated{
    font-family:'IBM Plex Mono', monospace; font-size:9.5px; color:#A0A4AB; margin-top:7px;
    display:flex; align-items:center; gap:4px;
}

.sxp-empty-col{ font-size:12px; color:#A0A4AB; text-align:center; padding:40px 8px; }
.sxp-empty-col .icon{ font-size:22px; margin-bottom:6px; opacity:.6; }

/* Modal refinements */
#contactModal .modal-content{ border-radius:12px; border:none; overflow:hidden; }
#contactModal .modal-header{ background:var(--paper); border-bottom:1px solid var(--line); padding:16px 20px; }
#contactModal .modal-title{ font-family:'Space Grotesk', sans-serif; font-weight:600; }
#contactModal .modal-body{ padding:20px; }
.sxp-section-label{
    font-family:'IBM Plex Mono', monospace; font-size:10.5px; font-weight:600;
    letter-spacing:.06em; text-transform:uppercase; color:var(--muted); margin:16px 0 8px;
}
.sxp-section-label:first-child{ margin-top:0; }

.activity-row{ display:flex; gap:8px; border-bottom:1px solid #eee; padding:8px 0; }
.activity-icon{ font-size:15px; flex:0 0 20px; }
.celebrate-emoji{ position:absolute; top:-40px; font-size:22px; animation:celebrateFall 1.6s ease-in forwards; }
@keyframes celebrateFall{
    0%{ transform:translateY(0) rotate(0deg); opacity:1; }
    100%{ transform:translateY(100vh) rotate(360deg); opacity:0; }
}

@media (max-width:640px){
    .sxp-header{ flex-direction:column; align-items:flex-start; }
    .sxp-toolbar{ width:100%; }
    .sxp-toolbar .sxp-input{ flex:1; }
}
</style>

<div class="sxp">

    <div class="sxp-header">
        <div>
            <div class="sxp-eyebrow">CRM &middot; PIPELINE</div>
            <h1>Deal pipeline</h1>
            <p>Drag a card to a new stage to move it. Moving a card manually turns off automated follow-ups for that contact.</p>
        </div>
        <div class="sxp-pins">
            <span></span><span></span><span></span><span></span><span></span>
        </div>
    </div>

    <div class="sxp-metrics">
        <div class="sxp-metric sxp-metric-total">
            <div class="sxp-metric-num">{{ $total }}</div>
            <div class="sxp-metric-label">Total contacts</div>
        </div>
        @foreach($stages as $stageKey => $stageLabel)
        <div class="sxp-metric" style="border-left:3px solid {{ $stageColors[$stageKey] }};">
            <div class="sxp-metric-num" style="color:{{ $stageColors[$stageKey] }};">{{ $counts[$stageKey] ?? 0 }}</div>
            <div class="sxp-metric-label">{{ $stageLabel }}</div>
        </div>
        @endforeach
    </div>

    <div class="sxp-insights">
        <div class="sxp-insight sxp-insight-rate">
            <div>
                <div class="sxp-insight-label">Win rate (closed deals)</div>
                <div class="sxp-insight-num">{{ $winRate }}%</div>
            </div>
            <div class="sxp-insight-icon">📈</div>
        </div>
        <div class="sxp-insight sxp-insight-won">
            <div>
                <div class="sxp-insight-label">Won this month</div>
                <div class="sxp-insight-num">{{ $wonThisMonth }}</div>
            </div>
            <div class="sxp-insight-icon">🏆</div>
        </div>
    </div>

    <div class="sxp-board-card">
        <div class="sxp-board-head">
            <div>
                <h4 class="sxp-board-title">Board</h4>
                <p class="sxp-board-sub">{{ $total }} contacts across {{ count($stages) }} stages</p>
            </div>
            <div class="sxp-toolbar">
                <input type="text" id="cardSearch" class="sxp-input" placeholder="Search name or company..." style="width:220px;">
                <select id="sourceFilter" class="sxp-input" style="width:150px;">
                    <option value="all">All sources</option>
                    <option value="quote">Quote</option>
                    <option value="visitor">Visitor</option>
                    <option value="winback">Winback</option>
                </select>
            </div>
        </div>

        <div class="pipeline-board">
            @foreach($stages as $stageKey => $stageLabel)
            <div class="pipeline-column" data-stage="{{ $stageKey }}"
                 style="--stage-color:{{ $stageColors[$stageKey] }}; --stage-soft:{{ $stageColorsSoft[$stageKey] }};">

                <div class="sxp-col-head">
                    <span class="sxp-col-label">{{ $stageLabel }}</span>
                    <span class="sxp-col-count">{{ isset($contactsByStage[$stageKey]) ? $contactsByStage[$stageKey]->count() : 0 }}</span>
                </div>

                <div class="pipeline-cards" data-stage="{{ $stageKey }}">
                    @forelse($contactsByStage[$stageKey] ?? [] as $contact)
                    @php
                        $avatarColor = $avatarPalette[$contact->id % count($avatarPalette)];
                    @endphp
                    <div class="pipeline-card" draggable="true" data-id="{{ $contact->id }}"
                         data-name="{{ strtolower($contact->name ?? '') }}"
                         data-company="{{ strtolower($contact->company ?? '') }}"
                         data-source="{{ $contact->source }}" data-email="{{ strtolower($contact->email ?? '') }}">

                        <div class="sxp-avatar" style="background:{{ $avatarColor }};">{{ sx_initials($contact->name) }}</div>

                        <div class="sxp-card-body">
                            <div class="sxp-card-name">{{ $contact->name ?: 'No name' }}</div>
                            @if($contact->company)
                            <div class="sxp-card-company">{{ $contact->company }}</div>
                            @endif

                            <div class="sxp-card-badges">
                                @if($contact->source === 'quote')
                                    <span class="sxp-badge" style="background:#2563eb;">Quote</span>
                                @elseif($contact->source === 'visitor')
                                    <span class="sxp-badge" style="background:#64748B;">Visitor</span>
                                @elseif($contact->source === 'winback')
                                    <span class="sxp-badge" style="background:#D97706;">Winback</span>
                                @endif
                                @if(!$contact->automation_enabled)
                                    <span class="sxp-badge" style="background:#334155;" title="Automation off">Manual</span>
                                @endif
                                @if($contact->manual_action !== 'none')
                                    <span class="sxp-badge" style="background:#16A34A;">{{ $manualActionLabels[$contact->manual_action] ?? $contact->manual_action }}</span>
                                @endif
                                @if($contact->email_status === 'bounced')
                                    <span class="sxp-badge" style="background:#DC2626;" title="Email bounced — automation halted">Bounced</span>
                                @endif
                            </div>

                            <div class="sxp-card-updated card-updated" data-timestamp="{{ $contact->updated_at?->toIso8601String() }}">
                                Updated recently
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="sxp-empty-col">
                        <div class="icon">🗂️</div>
                        Nothing here yet
                    </div>
                    @endforelse
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Contact Detail Modal -->
<div class="modal fade" id="contactModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="contactModalName">Contact</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p style="font-size:13px; color:#6b7280; margin-bottom:4px;" id="contactModalCompany"></p>
                <p style="font-size:13px; margin-bottom:2px;"><strong>Email:</strong> <span id="contactModalEmail"></span></p>
                <p style="font-size:13px; margin-bottom:16px;"><strong>Phone:</strong> <span id="contactModalPhone"></span></p>

                <div id="initialFollowupWrap" style="display:none; margin-bottom:16px;">
                    <button class="btn btn-sm btn-outline-primary" id="sendInitialFollowupBtn">
                        Send Day 0 Follow-up
                    </button>
                    <p style="font-size:11px; color:#6b7280; margin:4px 0 0;">
                        Sends the first follow-up email now and starts the automated day 3/7/14 cadence.
                    </p>
                </div>
                <div id="cadenceRunningNote" style="display:none; font-size:12px; color:#3b82f6; margin-bottom:16px;">
                    ⏱ Automated follow-up cadence is running for this contact.
                </div>
                <div id="bouncedNote" style="display:none; font-size:12px; color:#ef4444; margin-bottom:16px;">
                    ✉️ This contact's email has bounced — automation is halted and bulk/cadence sends will skip them.
                </div>

                <h6 class="sxp-section-label">Lead action</h6>
                <div class="form-group">
                    <select class="form-control form-control-sm" id="manualActionSelect">
                        <option value="none">No action yet</option>
                        <option value="rfq_received">RFQ Received</option>
                        <option value="quote_sent">Quote Sent</option>
                        <option value="quote_signed">Quote Signed</option>
                        <option value="quote_unsigned">Quote Unsigned</option>
                        <option value="no_quote">No Quote</option>
                        <option value="invalid_rfq">Invalid RFQ</option>
                    </select>
                </div>
                <textarea class="form-control mb-2" id="manualActionNote" rows="2" placeholder="Optional note about this action..."></textarea>
                <button class="btn btn-sm btn-outline-success mb-3" id="saveManualActionBtn">Save action</button>
                <p style="font-size:11px; color:#6b7280; margin-top:-8px;" class="mb-3">
                    Setting an action (other than "No action yet") stops automated follow-ups for this contact, same as moving its card.
                </p>

                <h6 class="sxp-section-label">Add a note</h6>
                <div class="input-group mb-3">
                    <textarea class="form-control" id="noteInput" rows="2" placeholder="Call outcome, context, anything worth remembering..."></textarea>
                </div>
                <button class="btn btn-sm btn-primary mb-3" id="saveNoteBtn">Save note</button>

                <h6 class="sxp-section-label">Activity history</h6>
                <div id="activityList" style="font-size:13px;"></div>
            </div>
        </div>
    </div>
</div>

<div id="celebrationLayer" style="position:fixed; top:0; left:0; width:100%; height:100%; pointer-events:none; z-index:9999; overflow:hidden;"></div>

@endsection

@section('footer')
<script>
$(document).ready(function () {

    var draggedId = null;
    var draggedFromStage = null;
    var currentContactId = null;

    /* Relative time for "Updated Xd ago" on cards */
    function timeAgo(iso) {
        if (!iso) return 'Updated recently';
        var seconds = Math.floor((new Date() - new Date(iso)) / 1000);
        if (seconds < 60) return 'Updated just now';
        var minutes = Math.floor(seconds / 60);
        if (minutes < 60) return 'Updated ' + minutes + 'm ago';
        var hours = Math.floor(minutes / 60);
        if (hours < 24) return 'Updated ' + hours + 'h ago';
        var days = Math.floor(hours / 24);
        if (days < 30) return 'Updated ' + days + 'd ago';
        var months = Math.floor(days / 30);
        return 'Updated ' + months + 'mo ago';
    }

    $('.card-updated').each(function () {
        $(this).text(timeAgo($(this).data('timestamp')));
    });

    /* Activity type -> icon */
    function activityIcon(type) {
        var map = {
            'winback_email': '✉️',
            'winback_followup': '✉️',
            'auto_email': '✉️',
            'bulk_email': '📣',
            'manual_note': '📝',
            'manual_action': '✅',
            'stage_change': '🔀',
            'hard_bounce': '⚠️',
            'soft_bounce': '⚠️',
            'call': '📞'
        };
        return map[type] || '•';
    }

    /* Drag events */
    $(document).on('dragstart', '.pipeline-card', function (e) {
        draggedId = $(this).data('id');
        draggedFromStage = $(this).closest('.pipeline-column').data('stage');
        e.originalEvent.dataTransfer.setData('text/plain', draggedId);
    });

    $(document).on('dragover', '.pipeline-column', function (e) {
        e.preventDefault();
        $(this).addClass('drag-over');
    });

    $(document).on('dragleave', '.pipeline-column', function () {
        $(this).removeClass('drag-over');
    });

    function celebrate() {
        var emojis = ['🎉', '🎊', '⭐', '✨'];
        for (var i = 0; i < 16; i++) {
            (function () {
                var el = $('<div class="celebrate-emoji"></div>')
                    .text(emojis[Math.floor(Math.random() * emojis.length)])
                    .css({ left: Math.random() * 100 + '%', animationDelay: (Math.random() * 0.4) + 's' });
                $('#celebrationLayer').append(el);
                setTimeout(function () { el.remove(); }, 2200);
            })();
        }
    }

    $(document).on('drop', '.pipeline-column', function (e) {
        e.preventDefault();
        $(this).removeClass('drag-over');

        var newStage = $(this).data('stage');
        var id = draggedId;
        if (!id) return;

        if (newStage === 'won' && draggedFromStage !== 'won') {
            celebrate();
        }

        $.ajax({
            url: "{{ route('admin.crm.move_stage') }}",
            type: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                id: id,
                stage: newStage
            },
            success: function () {
                location.reload();
            },
            error: function (xhr) {
                console.log(xhr.responseText);
                alert('Failed to move contact — check console.');
            }
        });
    });

    /* Search + source filter */
    function applyFilters() {
        var q = $('#cardSearch').val().trim().toLowerCase();
        var source = $('#sourceFilter').val();

        $('.pipeline-card').each(function () {
            var name = String($(this).data('name') || '').toLowerCase();
            var company = String($(this).data('company') || '').toLowerCase();
            var cardSource = $(this).data('source') || '';

            var email = String($(this).data('email') || '').toLowerCase();
            var matchesText = !q || name.indexOf(q) !== -1 || company.indexOf(q) !== -1 || email.indexOf(q) !== -1;
            var matchesSource = source === 'all' || cardSource === source;

            $(this).toggle(matchesText && matchesSource);
        });
    }

    $('#cardSearch').on('input', applyFilters);
    $('#sourceFilter').on('change', applyFilters);

    /* Click card -> open detail modal */
    $(document).on('click', '.pipeline-card', function () {
        var id = $(this).data('id');
        currentContactId = id;

        $.get("{{ url('admin/crm/contact') }}/" + id, function (res) {
            var c = res.contact;
            $('#contactModalName').text(c.name || 'No name');
            $('#contactModalCompany').text(c.company || '');
            $('#contactModalEmail').text(c.email || '—');
            $('#contactModalPhone').text(c.phone || '—');
            $('#noteInput').val('');
            $('#manualActionSelect').val(c.manual_action || 'none');
            $('#manualActionNote').val('');

            $('#initialFollowupWrap').toggle(!c.automation_enabled && c.followup_step === 0 && !!c.email && c.manual_action === 'none' && c.email_status !== 'bounced');
            $('#cadenceRunningNote').toggle(!!c.automation_enabled);
            $('#bouncedNote').toggle(c.email_status === 'bounced');

            var activityHtml = '';
            if (res.activities.length === 0) {
                activityHtml = '<p style="color:#a0a4ab;">No activity yet.</p>';
            } else {
                res.activities.forEach(function (a) {
                    activityHtml += '<div class="activity-row">';
                    activityHtml += '<div class="activity-icon">' + activityIcon(a.type) + '</div>';
                    activityHtml += '<div style="flex:1;">';
                    activityHtml += '<div style="font-size:11px; color:#a0a4ab;">' + timeAgo(a.created_at) + '</div>';
                    if (a.subject) activityHtml += '<div><strong>' + a.subject + '</strong></div>';
                    if (a.body) activityHtml += '<div>' + a.body + '</div>';
                    activityHtml += '</div></div>';
                });
            }
            $('#activityList').html(activityHtml);

            $('#contactModal').modal('show');
        }).fail(function (xhr) {
            console.log(xhr.responseText);
            alert('Failed to load contact details.');
        });
    });

    /* Save note */
    $('#saveNoteBtn').on('click', function () {
        var note = $('#noteInput').val().trim();
        if (!note || !currentContactId) return;

        $.ajax({
            url: "{{ url('admin/crm/contact') }}/" + currentContactId + "/note",
            type: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                note: note
            },
            success: function () {
                $('#contactModal').modal('hide');
                location.reload();
            },
            error: function (xhr) {
                console.log(xhr.responseText);
                alert('Failed to save note.');
            }
        });
    });

    /* Save manual action */
    $('#saveManualActionBtn').on('click', function () {
        if (!currentContactId) return;
        var action = $('#manualActionSelect').val();
        var note = $('#manualActionNote').val().trim();

        if (action !== 'none' && !confirm('Set this lead action to "' + $('#manualActionSelect option:selected').text() + '"? This will stop automated follow-ups for this contact.')) {
            return;
        }

        var btn = $(this);
        btn.prop('disabled', true).text('Saving...');

        $.ajax({
            url: "{{ url('admin/crm/contact') }}/" + currentContactId + "/manual-action",
            type: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                action: action,
                note: note
            },
            success: function () {
                $('#contactModal').modal('hide');
                location.reload();
            },
            error: function (xhr) {
                console.log(xhr.responseText);
                var msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Failed to save action — check console.';
                alert(msg);
                btn.prop('disabled', false).text('Save action');
            }
        });
    });

    /* Manual day 0 follow-up trigger */
    $(document).on("click", "#sendInitialFollowupBtn", function () {
        if (!currentContactId) return;
        if (!confirm("Send the day 0 follow-up email now and start the automated cadence?")) return;

        var btn = $(this);
        btn.prop("disabled", true).text("Sending...");

        $.ajax({
            url: "{{ url('admin/crm') }}/" + currentContactId + "/send-initial-followup",
            type: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (res) {
                alert(res.message);
                if (res.success) location.reload();
            },
            error: function (xhr) {
                console.log(xhr.responseText);
                var msg = (xhr.responseJSON && xhr.responseJSON.message) || "Send failed — check console.";
                alert(msg);
                btn.prop("disabled", false).text("Send Day 0 Follow-up");
            }
        });
    });

});
</script>
@endsection