@extends('admin.includes.masterpage-admin')

@section('content')

@php
    $customerApproved = $contacts->where('winback_track', 'customer')->where('winback_approved', true)->count();
    $prospectApproved = $contacts->where('winback_track', 'prospect')->where('winback_approved', true)->count();
    $inCadence = $contacts->filter(fn($c) => $c->automation_enabled && !is_null($c->followup_step))->count();
    $completed = $contacts->filter(fn($c) => $c->stage === 'contacted' && !$c->winback_track)->count();
    $notSent = $contacts->count() - $customerApproved - $prospectApproved - $inCadence - $completed;
@endphp

@if(Session::has('status'))
<div class="alert alert-success alert-dismissable">
    <a href="#" class="close" data-dismiss="alert">&times;</a>
    {{ Session::get('status') }}
</div>
@endif

<div class="row mb-3">
    <div class="col-sm-2 mb-2">
        <div class="card" style="border-left:4px solid #6b7280;">
            <div class="card-body p-3 text-center">
                <div style="font-size:20px; font-weight:700;">{{ $contacts->count() }}</div>
                <div style="font-size:11px; text-transform:uppercase; color:#6b7280; letter-spacing:0.4px;">Total</div>
            </div>
        </div>
    </div>
    <div class="col-sm-2 mb-2">
        <div class="card" style="border-left:4px solid #22c55e;">
            <div class="card-body p-3 text-center">
                <div style="font-size:20px; font-weight:700; color:#22c55e;">{{ $customerCount }}</div>
                <div style="font-size:11px; text-transform:uppercase; color:#6b7280; letter-spacing:0.4px;">Customers</div>
            </div>
        </div>
    </div>
    <div class="col-sm-2 mb-2">
        <div class="card" style="border-left:4px solid #f59e0b;">
            <div class="card-body p-3 text-center">
                <div style="font-size:20px; font-weight:700; color:#f59e0b;">{{ $prospectCount }}</div>
                <div style="font-size:11px; text-transform:uppercase; color:#6b7280; letter-spacing:0.4px;">Prospects</div>
            </div>
        </div>
    </div>
    <div class="col-sm-2 mb-2">
        <div class="card" style="border-left:4px solid #3b82f6;">
            <div class="card-body p-3 text-center">
                <div style="font-size:20px; font-weight:700; color:#3b82f6;">{{ $inCadence }}</div>
                <div style="font-size:11px; text-transform:uppercase; color:#6b7280; letter-spacing:0.4px;">In Cadence</div>
            </div>
        </div>
    </div>
    <div class="col-sm-2 mb-2">
        <div class="card" style="border-left:4px solid #14b8a6;">
            <div class="card-body p-3 text-center">
                <div style="font-size:20px; font-weight:700; color:#14b8a6;">{{ $customerApproved + $prospectApproved }}</div>
                <div style="font-size:11px; text-transform:uppercase; color:#6b7280; letter-spacing:0.4px;">Pending Send</div>
            </div>
        </div>
    </div>
    <div class="col-sm-2 mb-2">
        <div class="card" style="border-left:4px solid #1f2937;">
            <div class="card-body p-3 text-center">
                <div style="font-size:20px; font-weight:700; color:#1f2937;">{{ $completed }}</div>
                <div style="font-size:11px; text-transform:uppercase; color:#6b7280; letter-spacing:0.4px;">Completed</div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="d-flex align-items-center justify-content-between flex-wrap" style="gap:10px;">
            <ul class="nav" id="winbacktab" role="tablist" style="margin:0;">
                <li class="nav-item">
                    <a class="nav-link track active" data-track="all" href="#">
                        <h4 class="card-title mb-0">All ({{ $contacts->count() }})</h4>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link track" data-track="customer" href="#">
                        <h4 class="card-title mb-0">Customers ({{ $customerCount }})</h4>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link track" data-track="prospect" href="#">
                        <h4 class="card-title mb-0">Prospects ({{ $prospectCount }})</h4>
                    </a>
                </li>
            </ul>
            <input type="text" id="winbackSearch" class="form-control form-control-sm" placeholder="Search name, company, or email..." style="width:260px;">
        </div>
    </div>

    <div class="card-body">
        @if($contacts->isEmpty())
        <div style="text-align:center; padding:48px 0; color:#a0a4ab;">
            <div style="font-size:32px; margin-bottom:8px;">📭</div>
            No win-back contacts to review yet. Run <code>crm:import-winback</code> to pull in Dolibarr order/quote history.
        </div>
        @else
        <p class="text-muted" style="font-size:13px;">
            These are one-time winback contacts pulled from your full Dolibarr order/quote history.
            Uncheck anyone you don't want contacted, then approve to queue the rest for sending.
        </p>

        <form id="winbackForm" method="POST" action="{{ route('admin.crm.winback.approve') }}">
            @csrf

            <div class="mb-2">
                <label>
                    <input type="checkbox" id="selectAll" checked> Select / deselect all
                </label>
            </div>

            <div class="table-responsive">
                <table id="winbacklist" class="table table-striped dt-responsive" width="100%">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Name</th>
                            <th>Company</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Track</th>
                            <th>Cadence</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($contacts as $contact)
                        <tr data-track="{{ $contact->winback_track }}">
                            <td>
                                <input type="checkbox" name="approved_ids[]"
                                       class="contact-checkbox"
                                       value="{{ $contact->id }}" checked>
                            </td>
                            <td>
                                <input type="text" class="form-control form-control-sm name-input"
                                       data-id="{{ $contact->id }}"
                                       value="{{ $contact->name }}"
                                       placeholder="No name on file">
                            </td>
                            <td>{{ $contact->company ?: '—' }}</td>
                            <td>{{ $contact->email ?: '—' }}</td>
                            <td>{{ $contact->phone ?: '—' }}</td>
                            <td>
                                @if($contact->winback_track === 'customer')
                                    <span class="badge badge-success">Customer</span>
                                @else
                                    <span class="badge badge-warning">Prospect</span>
                                @endif
                            </td>
                            @php
                                if ($contact->stage === 'contacted' && !$contact->winback_track) {
                                    $cadenceSort = 5;
                                } elseif ($contact->automation_enabled && !is_null($contact->followup_step)) {
                                    $cadenceSort = 2 + $contact->followup_step;
                                } elseif ($contact->winback_approved) {
                                    $cadenceSort = 1;
                                } else {
                                    $cadenceSort = 0;
                                }
                            @endphp
                            <td data-order="{{ $cadenceSort }}">
                                @if($contact->stage === 'contacted' && !$contact->winback_track)
                                    <span class="badge badge-dark">Completed</span>
                                @elseif($contact->automation_enabled && !is_null($contact->followup_step))
                                    @php
                                        $stepLabels = ['Day 3', 'Day 7', 'Day 14'];
                                        $label = $stepLabels[$contact->followup_step] ?? 'Follow-up';
                                    @endphp
                                    <span class="badge badge-primary">{{ $label }} &mdash; next {{ optional($contact->next_followup_at)->format('M j') }}</span>
                                @elseif($contact->winback_approved)
                                    <span class="badge badge-info">Approved &mdash; pending send</span>
                                @else
                                    <span class="badge badge-secondary">Not sent</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <button type="submit" class="btn btn-primary" id="approveBtn">
                Approve checked contacts for sending
            </button>
        </form>
        @endif
    </div>
</div>

@endsection

@section('footer')
<script>
$.fn.dataTable.ext.order['dom-data-order'] = function (settings, col) {
    return this.api().column(col, { order: 'index' }).nodes().map(function (td) {
        return $(td).data('order') || 0;
    });
};

$(document).ready(function () {

    var table = $('#winbacklist').DataTable({
        pageLength: 25,
        lengthMenu: [[25,50,100,5000],[25,50,100,"All"]],
        columnDefs: [
            { orderable: false, targets: 0 },
            { orderDataType: "dom-data-order", targets: 6 }
        ]
    });

    /* Tab filter by track — filters via DataTables search API so
       pagination/row-count stays correct (fixes the old show/hide bug) */
    $('.track').on('click', function (e) {
        e.preventDefault();
        $('.track').removeClass('active');
        $(this).addClass('active');

        var track = $(this).data('track');
        if (track === 'all') {
            table.column(5).search('').draw();
        } else {
            var label = track === 'customer' ? 'Customer' : 'Prospect';
            table.column(5).search(label).draw();
        }
    });

    /* Free-text search across name, company, email */
    $('#winbackSearch').on('input', function () {
        table.search($(this).val()).draw();
    });

    /* Inline name editing — autosave on blur */
    $(document).on('blur', '.name-input', function () {
        var input = $(this);
        var newName = input.val().trim();
        var id = input.data('id');

        if (newName === input.data('original')) return;

        $.ajax({
            url: "{{ route('admin.crm.winback.update_name') }}",
            type: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                id: id,
                name: newName
            },
            success: function () {
                input.data('original', newName);
                input.css('background-color', '#d4edda');
                setTimeout(function () { input.css('background-color', ''); }, 800);
            },
            error: function (xhr) {
                console.log(xhr.responseText);
                alert('Failed to save name — check console.');
            }
        });
    });

    /* Select all toggle */
    $('#selectAll').on('change', function () {
        $('.contact-checkbox').prop('checked', $(this).is(':checked'));
    });

    /* Shift-click range select — click a row, shift-click another row,
       everything between them gets set to match the row you just clicked. */
    var lastChecked = null;

    $(document).on('click', '.contact-checkbox', function (e) {
        var checkboxes = $('.contact-checkbox:visible');
        var checked = $(this).is(':checked');

        if (e.shiftKey && lastChecked) {
            var start = checkboxes.index(lastChecked);
            var end = checkboxes.index(this);

            if (start > -1 && end > -1) {
                var lo = Math.min(start, end);
                var hi = Math.max(start, end);
                checkboxes.slice(lo, hi + 1).prop('checked', checked);
            }
        }

        lastChecked = this;
    });

    /* Confirm before sending */
    $('#winbackForm').on('submit', function (e) {
        var checkedCount = $('.contact-checkbox:checked').length;
        if (checkedCount === 0) {
            if (!confirm('No contacts are checked — this will approve zero contacts for sending. Continue?')) {
                e.preventDefault();
            }
            return;
        }
        if (!confirm('Approve ' + checkedCount + ' contact(s) for the winback email send?')) {
            e.preventDefault();
        }
    });

});
</script>
@endsection
