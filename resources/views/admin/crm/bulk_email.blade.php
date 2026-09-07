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
                <div style="font-size: 32px; font-weight: bold; color: #16a34a;">{{ $masterListCount ?? 0 }}</div>
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
                    <style>
                        .sxf-toolbar select, .sxf-toolbar input { font-size: 12px !important; padding: 4px 8px !important; height: 30px !important; }
                        .sxf-toolbar { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 15px; }
                        .sxf-toolbar input#search { flex: 1 1 100%; margin-bottom: 4px; }
                        .sxf-toolbar select { flex: 1 1 auto; min-width: 0; }
                    </style>
                    <div class="sxf-toolbar">
                        <input type="text" id="search" class="form-control" placeholder="Search email, name...">
                        <select id="source" class="form-control">
                            <option value="">All Sources</option>
                            <option value="master_list">Master List</option>
                            <option value="quote">Quote</option>
                            <option value="visitor">Visitor</option>
                            <option value="winback">Winback</option>
                        </select>
                        <select id="status" class="form-control">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="all">All</option>
                        </select>
                        <select id="recency" class="form-control">
                            <option value="">Any time</option>
                            <option value="3">3+ days</option>
                            <option value="7" selected>7+ days</option>
                            <option value="14">14+ days</option>
                            <option value="30">30+ days</option>
                            <option value="never">Never contacted</option>
                        </select>
                        <select id="perPage" class="form-control">
                            <option value="25">25</option>
                            <option value="50" selected>50</option>
                            <option value="100">100</option>
                            <option value="500">500</option>
                            <option value="1000">1000</option>
                        </select>
                    </div>

                    <div style="max-height: 500px; overflow-y: auto; border: 1px solid #e0e0e0; border-radius: 6px;">
                        <table class="table table-sm mb-0" style="font-size: 12px;">
                            <thead style="position: sticky; top: 0; background: #f8f9fa; border-bottom: 2px solid #e0e0e0;">
                                <tr>
                                    <th style="width: 35px; padding: 10px;"><input type="checkbox" id="selectAll" style="cursor: pointer;"></th>
                                    <th style="padding: 10px;">Email</th>
                                    <th style="padding: 10px;">Name</th>
                                    <th style="padding: 10px;">Source</th>
                                    <th style="padding: 10px;">Status</th>
                                </tr>
                            </thead>
                            <tbody id="list">
                                <tr><td colspan="5" style="text-align: center; padding: 30px; color: #999;">Loading contacts...</td></tr>
                            </tbody>
                        </table>
                    </div>

                    <p id="info" style="font-size: 11px; color: #999; margin-top: 12px;"></p>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 8px;">
                        <label style="cursor: pointer; font-size: 13px;"><input type="checkbox" id="selectAll2" style="margin-right: 6px;"> Select all visible</label>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <button type="button" id="prevPageBtn" class="btn btn-outline-secondary btn-sm">&laquo; Prev</button>
                            <span id="pageInfo" style="font-size: 12px; color: #666; min-width: 90px; text-align: center;">Page 1 of 1</span>
                            <button type="button" id="nextPageBtn" class="btn btn-outline-secondary btn-sm">Next &raquo;</button>
                        </div>
                    </div>
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
                        <input type="text" id="subjectInput" name="subject" class="form-control" required placeholder="Email subject...">
                    </div>

                    <div class="form-group">
                        <label style="font-weight: 600; font-size: 12px;">MESSAGE</label>
                        <div id="emailEditor" style="background: white; border: 1px solid #ddd; border-radius: 4px; min-height: 250px;"></div>
                        <textarea id="bodyEditor" name="body" style="display: none;"></textarea>
                        <div style="margin-top: 8px;">
                            <button type="button" id="pasteHtmlBtn" class="btn btn-outline-secondary btn-sm">Paste HTML Source</button>
                        </div>
                        <div id="rawHtmlWrapper" style="display: none; margin-top: 8px;">
                            <textarea id="rawHtmlInput" class="form-control" style="min-height: 180px; font-family: monospace; font-size: 12px;" placeholder="Paste your raw HTML template here..."></textarea>
                            <div style="margin-top: 6px;">
                                <button type="button" id="applyHtmlBtn" class="btn btn-primary btn-sm">Insert into Editor</button>
                                <button type="button" id="cancelHtmlBtn" class="btn btn-link btn-sm">Cancel</button>
                            </div>
                        </div>
                    </div>

                    <button type="button" id="previewBtn" class="btn btn-outline-secondary" style="margin-top: 15px;">
                        📧 Preview
                    </button>
                    <button type="submit" class="btn btn-primary" style="margin-top: 15px; float: right;">
                        Send to <b id="selectedCountBtn">0</b> contact(s)
                    </button>
                </div>
            </div>
        </div>
    </div>

    </form>
</div>

<!-- HTML Mailbox Preview Modal -->
<div id="mailboxModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; overflow-y: auto;">
    <div style="max-width: 900px; margin: 20px auto; background: white; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.2);">
        <!-- Mailbox Header -->
        <div style="background: #f8f9fa; border-bottom: 1px solid #e0e0e0; padding: 20px; display: flex; justify-content: space-between; align-items: center; border-radius: 8px 8px 0 0;">
            <div>
                <h3 style="margin: 0; font-size: 18px;">📧 Email Preview</h3>
                <p style="margin: 5px 0 0 0; font-size: 12px; color: #666;">HTML Mailbox Render</p>
            </div>
            <button type="button" id="closeMailbox" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #999;">&times;</button>
        </div>

        <!-- Mailbox Tabs -->
        <div style="border-bottom: 1px solid #e0e0e0; padding: 0 20px; display: flex; gap: 15px;">
            <button type="button" class="mailbox-tab" data-tab="desktop" style="padding: 15px 0; border: none; background: none; cursor: pointer; border-bottom: 3px solid #2563eb; color: #2563eb; font-weight: 600; font-size: 13px;">
                💻 Desktop (600px)
            </button>
            <button type="button" class="mailbox-tab" data-tab="mobile" style="padding: 15px 0; border: none; background: none; cursor: pointer; border-bottom: 3px solid transparent; color: #999; font-weight: 600; font-size: 13px;">
                📱 Mobile (320px)
            </button>
            <button type="button" class="mailbox-tab" data-tab="source" style="padding: 15px 0; border: none; background: none; cursor: pointer; border-bottom: 3px solid transparent; color: #999; font-weight: 600; font-size: 13px;">
                &lt;/&gt; HTML Source
            </button>
        </div>

        <!-- Mailbox Content -->
        <div style="padding: 20px; min-height: 400px; max-height: 70vh; overflow-y: auto; background: #fafafa;">
            <!-- Desktop View -->
            <div id="tabDesktop" class="mailbox-content" style="display: block;">
                <div style="max-width: 600px; margin: 0 auto; background: white; border-radius: 6px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <!-- Email Header -->
                    <div style="background: #f0f0f0; padding: 15px 20px; border-bottom: 1px solid #ddd;">
                        <div style="font-size: 12px; color: #666; margin-bottom: 8px;"><strong>To:</strong> recipient@example.com</div>
                        <div style="font-size: 12px; color: #666; margin-bottom: 8px;"><strong>Subject:</strong> <span id="mailboxSubject">—</span></div>
                        <div style="font-size: 12px; color: #999;">Sent: <span id="mailboxDate">—</span></div>
                    </div>
                    <!-- Email Body -->
                    <div id="mailboxBody" style="padding: 30px 20px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 14px; line-height: 1.6; color: #333;">
                        <p style="text-align: center; color: #999;">Preview will render here...</p>
                    </div>
                </div>
            </div>

            <!-- Mobile View -->
            <div id="tabMobile" class="mailbox-content" style="display: none;">
                <div style="max-width: 320px; margin: 0 auto; background: white; border-radius: 6px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <!-- Email Header -->
                    <div style="background: #f0f0f0; padding: 12px 15px; border-bottom: 1px solid #ddd;">
                        <div style="font-size: 11px; color: #666; margin-bottom: 6px; word-break: break-all;"><strong>To:</strong> recipient@example.com</div>
                        <div style="font-size: 11px; color: #666; margin-bottom: 6px;"><strong>Subject:</strong> <span id="mailboxSubjectMobile">—</span></div>
                        <div style="font-size: 10px; color: #999;">Sent: <span id="mailboxDateMobile">—</span></div>
                    </div>
                    <!-- Email Body -->
                    <div id="mailboxBodyMobile" style="padding: 20px 15px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 13px; line-height: 1.6; color: #333;">
                        <p style="text-align: center; color: #999;">Preview will render here...</p>
                    </div>
                </div>
            </div>

            <!-- HTML Source View -->
            <div id="tabSource" class="mailbox-content" style="display: none;">
                <div style="background: #1e1e1e; padding: 15px; border-radius: 4px; overflow-x: auto;">
                    <pre id="sourceCode" style="margin: 0; color: #d4d4d4; font-family: 'Courier New', monospace; font-size: 12px; line-height: 1.5;"></pre>
                </div>
            </div>
        </div>

        <!-- Mailbox Footer -->
        <div style="background: #f8f9fa; border-top: 1px solid #e0e0e0; padding: 15px 20px; text-align: right; border-radius: 0 0 8px 8px;">
            <button type="button" id="closeMailboxBtn" class="btn btn-secondary" style="margin-right: 10px;">Close</button>
            <button type="button" id="copyHtmlBtn" class="btn btn-outline-primary">Copy HTML</button>
        </div>
    </div>
</div>

<!-- Quill Rich Text Editor -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const quill = new Quill('#emailEditor', {
        theme: 'snow',
        placeholder: 'Write your message here...',
        modules: {
            toolbar: [
                ['bold', 'italic', 'underline'],
                ['list', 'ordered'],
                ['link'],
                ['clean']
            ]
        }
    });

    // Paste HTML source directly, bypassing Quill's normal paste handling
    const pasteHtmlBtn = document.getElementById('pasteHtmlBtn');
    const rawHtmlWrapper = document.getElementById('rawHtmlWrapper');
    const rawHtmlInput = document.getElementById('rawHtmlInput');
    const applyHtmlBtn = document.getElementById('applyHtmlBtn');
    const cancelHtmlBtn = document.getElementById('cancelHtmlBtn');

    pasteHtmlBtn.addEventListener('click', function() {
        rawHtmlWrapper.style.display = rawHtmlWrapper.style.display === 'none' ? 'block' : 'none';
        if (rawHtmlWrapper.style.display === 'block') rawHtmlInput.focus();
    });

    cancelHtmlBtn.addEventListener('click', function() {
        rawHtmlWrapper.style.display = 'none';
        rawHtmlInput.value = '';
    });

    applyHtmlBtn.addEventListener('click', function() {
    const html = rawHtmlInput.value.trim();
    if (!html) {
        alert('Paste HTML first');
        return;
    }
    
    // Bypass Quill entirely - put raw HTML directly into the hidden textarea
    document.getElementById('bodyEditor').value = html;
    
    // Also show preview immediately
    const subject = document.getElementById('subjectInput').value || '(No Subject)';
    const now = new Date().toLocaleString();
    document.getElementById('mailboxSubject').textContent = subject;
    document.getElementById('mailboxSubjectMobile').textContent = subject;
    document.getElementById('mailboxDate').textContent = now;
    document.getElementById('mailboxDateMobile').textContent = now;
    document.getElementById('mailboxBody').innerHTML = html;
    document.getElementById('mailboxBodyMobile').innerHTML = html;
    
    // Show the modal
    mailboxModal.style.display = 'block';
    document.body.style.overflow = 'hidden';
    
    // Clear and hide the raw HTML textarea
    rawHtmlWrapper.style.display = 'none';
    rawHtmlInput.value = '';
    });

    document.getElementById('bulkEmailForm').addEventListener('submit', function(e) {
    // Only use Quill content if user was using the WYSIWYG editor
    // If they pasted raw HTML, bodyEditor is already populated - don't overwrite
    if (document.getElementById('bodyEditor').value === '') {
        const content = quill.root.innerHTML;
        document.getElementById('bodyEditor').value = content;
    }
    });

    // Mailbox Preview Logic
    const mailboxModal = document.getElementById('mailboxModal');
    const previewBtn = document.getElementById('previewBtn');
    const closeMailbox = document.getElementById('closeMailbox');
    const closeMailboxBtn = document.getElementById('closeMailboxBtn');
    const mailboxTabs = document.querySelectorAll('.mailbox-tab');
    const mailboxContents = document.querySelectorAll('.mailbox-content');
    const copyHtmlBtn = document.getElementById('copyHtmlBtn');

    previewBtn.addEventListener('click', function() {
        const subject = document.getElementById('subjectInput').value || '(No Subject)';
        const body = quill.root.innerHTML;
        const now = new Date().toLocaleString();

        // Update all views
        document.getElementById('mailboxSubject').textContent = subject;
        document.getElementById('mailboxSubjectMobile').textContent = subject;
        document.getElementById('mailboxDate').textContent = now;
        document.getElementById('mailboxDateMobile').textContent = now;
        document.getElementById('mailboxBody').innerHTML = body;
        document.getElementById('mailboxBodyMobile').innerHTML = body;

        // HTML Source
        const htmlSource = `<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>${escapeHtml(subject)}</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        img { max-width: 100%; height: auto; }
    </style>
</head>
<body>
    <div class="container">
        ${body}
    </div>
</body>
</html>`;

        document.getElementById('sourceCode').textContent = htmlSource;
        copyHtmlBtn.dataset.html = htmlSource;

        mailboxModal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    });

    // Close Modal
    function closeModal() {
        mailboxModal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    closeMailbox.addEventListener('click', closeModal);
    closeMailboxBtn.addEventListener('click', closeModal);
    mailboxModal.addEventListener('click', function(e) {
        if (e.target === mailboxModal) closeModal();
    });

    // Tab Switching
    mailboxTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const tabName = this.dataset.tab;
            mailboxTabs.forEach(t => {
                t.style.borderBottomColor = t.dataset.tab === tabName ? '#2563eb' : 'transparent';
                t.style.color = t.dataset.tab === tabName ? '#2563eb' : '#999';
            });
            mailboxContents.forEach(c => {
                c.style.display = c.id === 'tab' + tabName.charAt(0).toUpperCase() + tabName.slice(1) ? 'block' : 'none';
            });
        });
    });

    // Copy HTML to Clipboard
    copyHtmlBtn.addEventListener('click', function() {
        const html = copyHtmlBtn.dataset.html;
        navigator.clipboard.writeText(html).then(() => {
            const oldText = copyHtmlBtn.textContent;
            copyHtmlBtn.textContent = '✓ Copied!';
            setTimeout(() => {
                copyHtmlBtn.textContent = oldText;
            }, 2000);
        }).catch(() => {
            alert('Failed to copy. Please try again.');
        });
    });

    // Escape HTML
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    }
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentPage = 1;
    let totalContacts = 0;
    let searchDebounce = null;
    const checkedIds = new Set();
    const search = document.getElementById('search');
    const source = document.getElementById('source');
    const status = document.getElementById('status');
    const recency = document.getElementById('recency');
    const perPageSelect = document.getElementById('perPage');
    const list = document.getElementById('list');
    const info = document.getElementById('info');
    const pageInfo = document.getElementById('pageInfo');
    const prevPageBtn = document.getElementById('prevPageBtn');
    const nextPageBtn = document.getElementById('nextPageBtn');
    const selectAll = document.getElementById('selectAll');
    const selectAll2 = document.getElementById('selectAll2');
    const form = document.getElementById('bulkEmailForm');

    function buildUrl() {
        const params = new URLSearchParams({
            page: currentPage,
            per_page: perPageSelect.value,
            search: search.value.trim(),
            source: source.value,
            status: status.value,
            recency: recency.value,
        });
        return '/admin/crm/get-contacts?' + params.toString();
    }

    function fetchAndRender() {
        list.innerHTML = '<tr><td colspan="5" style="text-align: center; padding: 30px; color: #999;">Loading...</td></tr>';
        fetch(buildUrl())
            .then(res => res.json())
            .then(result => {
                totalContacts = result.total || 0;
                render(result.contacts || [], result.per_page || 50);
            })
            .catch(err => {
                console.error('Error loading contacts:', err);
                list.innerHTML = '<tr><td colspan="5" style="color: red; padding: 20px;">Error loading contacts</td></tr>';
            });
    }

    function render(pageItems, perPage) {
        const totalPages = Math.max(1, Math.ceil(totalContacts / perPage));
        if (currentPage > totalPages) currentPage = totalPages;

        let html = pageItems.map(c => {
            const inactive = c.email_status === 'bounced';
            const rowStyle = inactive ? 'opacity: 0.5;' : '';
            const sourceLabel = c.source === 'master_list' ? 'Master List' : (c.source || '—');
            const sourceColor = c.source === 'master_list' ? '#a855f7' : c.source === 'quote' ? '#2563eb' : c.source === 'winback' ? '#dc2626' : '#d97706';
            return `
            <tr style="border-bottom: 1px solid #f5f5f5; ${rowStyle}">
                <td style="padding: 10px;"><input type="checkbox" name="contact_ids[]" class="cb" value="${c.id}" style="cursor: pointer;" ${inactive ? 'disabled title="Inactive: this address has bounced"' : ''} ${checkedIds.has(String(c.id)) ? 'checked' : ''}></td>
                <td style="padding: 10px; color: #0066cc; font-weight: 500;">${c.email}</td>
                <td style="padding: 10px; color: #555;">${c.name || '—'}</td>
                <td style="padding: 10px;"><span style="font-size: 9px; background: ${sourceColor}; color: white; padding: 2px 6px; border-radius: 2px; display: inline-block;">${sourceLabel}</span></td>
                <td style="padding: 10px;">${inactive ? '<span style="font-size: 9px; background: #6b7280; color: white; padding: 2px 6px; border-radius: 2px;">Inactive</span>' : '<span style="font-size: 9px; background: #16a34a; color: white; padding: 2px 6px; border-radius: 2px;">Active</span>'}</td>
            </tr>
        `;
        }).join('');

        list.innerHTML = html || '<tr><td colspan="5" style="text-align: center; padding: 30px; color: #999;">No contacts found</td></tr>';

        const startIdx = (currentPage - 1) * perPage;
        const rangeStart = totalContacts === 0 ? 0 : startIdx + 1;
        const rangeEnd = Math.min(startIdx + perPage, totalContacts);
        info.textContent = `Showing ${rangeStart}-${rangeEnd} of ${totalContacts} contacts`;
        pageInfo.textContent = `Page ${currentPage} of ${totalPages}`;
        prevPageBtn.disabled = currentPage <= 1;
        nextPageBtn.disabled = currentPage >= totalPages;
        count();
    }

    function count() {
        const n = checkedIds.size;
        document.getElementById('selectedCountStat').textContent = n;
        document.getElementById('selectedCountBtn').textContent = n;
    }

    function goToPage(delta) {
        currentPage += delta;
        fetchAndRender();
    }

    search.addEventListener('input', () => {
        clearTimeout(searchDebounce);
        searchDebounce = setTimeout(() => { currentPage = 1; fetchAndRender(); }, 350);
    });
    source.addEventListener('change', () => { currentPage = 1; fetchAndRender(); });
    status.addEventListener('change', () => { currentPage = 1; fetchAndRender(); });
    recency.addEventListener('change', () => { currentPage = 1; fetchAndRender(); });
    perPageSelect.addEventListener('change', () => { currentPage = 1; fetchAndRender(); });
    prevPageBtn.addEventListener('click', () => goToPage(-1));
    nextPageBtn.addEventListener('click', () => goToPage(1));

    selectAll.addEventListener('change', () => {
        document.querySelectorAll('.cb:not(:disabled)').forEach(cb => {
            cb.checked = selectAll.checked;
            if (cb.checked) checkedIds.add(cb.value); else checkedIds.delete(cb.value);
        });
        count();
    });

    selectAll2.addEventListener('change', () => {
        document.querySelectorAll('.cb:not(:disabled)').forEach(cb => {
            cb.checked = selectAll2.checked;
            if (cb.checked) checkedIds.add(cb.value); else checkedIds.delete(cb.value);
        });
        count();
    });

    list.addEventListener('change', (e) => {
        if (e.target.classList.contains('cb')) {
            if (e.target.checked) checkedIds.add(e.target.value); else checkedIds.delete(e.target.value);
            count();
        }
    });

    form.addEventListener('submit', function(e) {
        document.querySelectorAll('input[name="contact_ids[]"][type="hidden"]').forEach(el => el.remove());
        checkedIds.forEach(id => {
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'contact_ids[]';
            hidden.value = id;
            form.appendChild(hidden);
        });

        const checkedCount = checkedIds.size;
        if (checkedCount === 0) {
            e.preventDefault();
            alert('Select at least one contact');
            return;
        }
        if (!confirm(`Send to ${checkedCount} contact(s)?`)) {
            e.preventDefault();
        }
    });

    fetchAndRender();
});
</script>

@endsection