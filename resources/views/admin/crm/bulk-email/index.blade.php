@extends('admin.includes.masterpage-admin')

@section('content')

<div style="padding: 20px;">
    <h2>Bulk Email</h2>

    @if(Session::has('status'))
    <div class="alert alert-success alert-dismissable">
        <button class="close" data-dismiss="alert">&times;</button>
        {{ Session::get('status') }}
    </div>
    @endif

    <div class="row" style="margin-bottom: 20px;">
        <div class="col-md-4">
            <div class="card" style="padding: 15px; text-align: center;">
                <div style="font-size: 32px; font-weight: bold;">{{ $totalContacts }}</div>
                <div style="font-size: 12px; color: #666;">Total Contacts</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card" style="padding: 15px; text-align: center;">
                <div style="font-size: 32px; font-weight: bold; color: #2563eb;" id="selectedCountStat">0</div>
                <div style="font-size: 12px; color: #666;">Selected</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card" style="padding: 15px; text-align: center;">
                <div style="font-size: 32px; font-weight: bold; color: #16a34a;">{{ $masterListCount }}</div>
                <div style="font-size: 12px; color: #666;">Master List</div>
            </div>
        </div>
    </div>

    <form id="bulkEmailForm" method="POST" action="{{ route('admin.crm.bulk_email.send') }}">
    @csrf

    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Recipients</h4>
                </div>
                <div class="card-body">
                    <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                        <input type="text" id="search" class="form-control" placeholder="Email, name..." style="flex: 1;">
                        <select id="source" class="form-control" style="width: 140px;">
                            <option value="">All Sources</option>
                            <option value="master_list">Master List</option>
                            <option value="quote">Quote</option>
                            <option value="visitor">Visitor</option>
                        </select>
                    </div>

                    <div style="max-height: 550px; overflow-y: auto; border: 1px solid #ddd; border-radius: 4px;">
                        <table class="table table-sm mb-0" style="font-size: 13px;">
                            <thead style="position: sticky; top: 0; background: #f8f9fa;">
                                <tr>
                                    <th style="width: 30px;"></th>
                                    <th>Email</th>
                                    <th>Source</th>
                                </tr>
                            </thead>
                            <tbody id="list">
                                <tr><td colspan="3" style="text-align: center; padding: 20px; color: #999;">Loading...</td></tr>
                            </tbody>
                        </table>
                    </div>

                    <p id="info" style="font-size: 12px; color: #999; margin-top: 10px;">Loading...</p>
                    <label style="margin-top: 10px;"><input type="checkbox" id="selectAll"> Select all visible</label>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Compose Email</h4>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label style="font-weight: 600; font-size: 12px;">SUBJECT</label>
                        <input type="text" name="subject" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label style="font-weight: 600; font-size: 12px;">MESSAGE</label>
                        <div id="emailEditor" style="background: white; border: 1px solid #ddd; border-radius: 4px; min-height: 250px;"></div>
                        <textarea id="emailBody" name="body" style="display: none;"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block" style="margin-top: 15px;">
                        Send to <b id="selectedCountBtn">0</b> contact(s)
                    </button>
                </div>
            </div>
        </div>
    </div>

    </form>
</div>

<!-- Quill Rich Text Editor (Free, No Registration) -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

<script>
// Initialize Quill editor
const quill = new Quill('#emailEditor', {
    theme: 'snow',
    placeholder: 'Write your message...',
    modules: {
        toolbar: [
            ['bold', 'italic', 'underline'],
            ['list', 'ordered'],
            ['link'],
            ['clean']
        ]
    }
});

// Sync Quill content to hidden textarea on form submit
document.getElementById('bulkEmailForm').addEventListener('submit', function(e) {
    const content = quill.root.innerHTML;
    document.getElementById('emailBody').value = content;
});
</script>

<script>
$(function() {
    let data = [];
    const search = $('#search');
    const source = $('#source');
    const list = $('#list');
    const info = $('#info');
    const selectAll = $('#selectAll');

    $.get('/admin/crm/get-contacts?limit=500', (res) => {
        data = res.contacts;
        render();
    });

    function render() {
        let filtered = data;
        const q = search.val().toLowerCase();
        const s = source.val();

        if (q) filtered = filtered.filter(c => c.email.includes(q) || (c.name && c.name.toLowerCase().includes(q)));
        if (s) filtered = filtered.filter(c => c.source === s);

        let html = filtered.map(c => `
            <tr>
                <td><input type="checkbox" name="contact_ids[]" class="cb" value="${c.id}"></td>
                <td style="color: #0066cc; font-size: 12px;">${c.email}</td>
                <td><span style="font-size: 10px; background: ${c.source === 'master_list' ? '#a855f7' : c.source === 'quote' ? '#2563eb' : '#d97706'}; color: white; padding: 2px 6px; border-radius: 2px;">${c.source === 'master_list' ? 'Master List' : c.source}</span></td>
            </tr>
        `).join('');

        list.html(html || '<tr><td colspan="3" style="text-align: center; padding: 20px; color: #999;">No contacts</td></tr>');
        info.text('Showing ' + filtered.length + ' of {{ $totalContacts }}');
    }

    function count() {
        const n = $('.cb:checked').length;
        $('#selectedCountStat').text(n);
        $('#selectedCountBtn').text(n);
    }

    search.on('input', render);
    source.on('change', render);
    selectAll.on('change', () => { $('.cb').prop('checked', selectAll.is(':checked')); count(); });
    $(document).on('change', '.cb', count);

    $('#bulkEmailForm').on('submit', function(e) {
        if ($('.cb:checked').length === 0) { 
            e.preventDefault(); 
            alert('Select at least one contact'); 
            return;
        }
        
        const content = quill.getText().trim();
        if (!content || content === '') { 
            e.preventDefault(); 
            alert('Write a message'); 
            return;
        }
        
        if (!confirm('Send to ' + $('.cb:checked').length + ' contact(s)?')) {
            e.preventDefault();
        }
    });
});
</script>

@endsection
