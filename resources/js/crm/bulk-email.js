/**
 * Bulk Email Manager
 * Handles contact selection, search/filtering, composition, and signature management
 */

const BulkEmailManager = (() => {
    // Private variables
    let dataTable;
    const SUMMERNOTE_TOOLBAR = [
        ['style', ['bold', 'italic', 'underline', 'clear']],
        ['font', ['fontname', 'fontsize', 'color']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['insert', ['link', 'picture', 'hr']],
        ['view', ['fullscreen', 'codeview']]
    ];

    const SUMMERNOTE_CONFIG = {
        toolbar: SUMMERNOTE_TOOLBAR,
        height: 260,
        placeholder: 'Write your message here...',
        callbacks: {
            onInit: function() {
                console.log('Summernote initialized');
            }
        }
    };

    const SIGNATURE_CONFIG = {
        toolbar: SUMMERNOTE_TOOLBAR,
        height: 160,
        placeholder: 'Your signature — e.g. name, title, phone, logo image URL...'
    };

    // Initialize on document ready
    const init = () => {
        initSummernote();
        initDataTable();
        attachEventListeners();
        updateSelectedCount();
    };

    /**
     * Initialize Summernote editors
     */
    const initSummernote = () => {
        $('#bulkBody').summernote(SUMMERNOTE_CONFIG);
        $('#signatureEditor').summernote(SIGNATURE_CONFIG);
    };

    /**
     * Initialize DataTable with configuration
     */
    const initDataTable = () => {
        dataTable = $('#bulkContactsTable').DataTable({
            pageLength: 25,
            lengthMenu: [[25, 50, 100, 5000], [25, 50, 100, 'All']],
            columnDefs: [
                { orderable: false, targets: 0 },
                { width: '40px', targets: 0 }
            ],
            language: {
                search: '_INPUT_',
                searchPlaceholder: 'Search table...'
            },
            dom: '<"dt-toolbar"l>t<"dt-pagination"p>',
            drawCallback: function() {
                // Re-attach checkbox listeners after table redraw
                attachCheckboxListeners();
            }
        });
    };

    /**
     * Attach event listeners to form elements
     */
    const attachEventListeners = () => {
        // Checkbox changes
        attachCheckboxListeners();

        // Select all checkbox
        $('#selectAll').on('change', handleSelectAllChange);

        // Search functionality
        $('#bulkSearch').on('input', handleSearch);

        // Source filter
        $('#sourceFilter').on('change', handleSourceFilter);

        // Signature insertion
        $('#insertSignatureBtn').on('click', handleInsertSignature);

        // Save signature
        $('#saveSignatureBtn').on('click', handleSaveSignature);

        // Form submission
        $('#bulkEmailForm').on('submit', handleFormSubmit);
    };

    /**
     * Attach listeners to individual checkboxes
     */
    const attachCheckboxListeners = () => {
        $(document).off('change', '.contact-checkbox').on('change', '.contact-checkbox', () => {
            updateSelectedCount();
        });
    };

    /**
     * Update the count of selected contacts in UI
     */
    const updateSelectedCount = () => {
        const count = $('.contact-checkbox:checked').length;
        $('#selectedCountStat').text(count);
        $('#selectedCountBtn').text(count);
        
        // Update button state
        const $btn = $('#bulkSendBtn');
        if (count === 0) {
            $btn.prop('disabled', true).addClass('disabled');
        } else {
            $btn.prop('disabled', false).removeClass('disabled');
        }
    };

    /**
     * Handle "Select all visible" checkbox
     */
    const handleSelectAllChange = function() {
        const isChecked = $(this).is(':checked');
        
        // Only select visible (filtered) rows
        dataTable.rows({ search: 'applied' }).nodes().to$().find('.contact-checkbox').prop('checked', isChecked);
        
        updateSelectedCount();
    };

    /**
     * Handle free-text search
     */
    const handleSearch = function() {
        const searchTerm = $(this).val();
        dataTable.search(searchTerm).draw();
        
        // Reset "select all" checkbox when searching
        $('#selectAll').prop('checked', false);
    };

    /**
     * Handle source filter dropdown
     */
    const handleSourceFilter = function() {
        const source = $(this).val();
        
        if (source === 'all') {
            // Clear filter
            dataTable.column(4).search('').draw();
        } else {
            // Map source value to badge label
            const labelMap = {
                'quote': 'Quote',
                'visitor': 'Visitor',
                'winback': 'Winback'
            };
            const label = labelMap[source] || '';
            
            dataTable.column(4).search(label).draw();
        }
        
        // Reset "select all" checkbox when filtering
        $('#selectAll').prop('checked', false);
    };

    /**
     * Insert saved signature into compose body
     */
    const handleInsertSignature = function(e) {
        e.preventDefault();
        
        const signature = $('#signatureEditor').summernote('code');
        const currentBody = $('#bulkBody').summernote('code');
        
        if (!currentBody || currentBody === '<p><br></p>') {
            // If body is empty, just insert signature
            $('#bulkBody').summernote('code', signature);
        } else {
            // Append signature with line break
            $('#bulkBody').summernote('code', currentBody + '<br><br>' + signature);
        }
        
        // Focus on the editor
        $('#bulkBody').summernote('focus');
    };

    /**
     * Save signature via AJAX
     */
    const handleSaveSignature = function(e) {
        e.preventDefault();
        
        const $btn = $(this);
        const signature = $('#signatureEditor').summernote('code');
        const $notification = $('#signatureSavedNote');
        
        // Validate signature
        if (!signature || signature === '<p><br></p>') {
            alert('Signature cannot be empty.');
            return;
        }
        
        // Update button state
        $btn.prop('disabled', true).text('Saving...');
        
        // Send AJAX request
        $.ajax({
            url: $('meta[name="csrf-token"]').data('url') || getSignatureRoute(),
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                signature: signature
            },
            success: () => {
                handleSaveSuccess($btn, $notification);
            },
            error: (xhr) => {
                handleSaveError($btn, xhr);
            }
        });
    };

    /**
     * Get signature save route (fallback)
     */
    const getSignatureRoute = () => {
        // Try to get from data attribute, fallback to hardcoded path
        return document.querySelector('[data-signature-route]')?.dataset.signatureRoute ||
               '/admin/crm/bulk-email/signature';
    };

    /**
     * Handle successful signature save
     */
    const handleSaveSuccess = ($btn, $notification) => {
        // Reset button
        $btn.prop('disabled', false).text('Save signature');
        
        // Show success notification
        $notification.addClass('show');
        
        // Hide notification after 1.8 seconds
        setTimeout(() => {
            $notification.removeClass('show');
        }, 1800);
    };

    /**
     * Handle signature save error
     */
    const handleSaveError = ($btn, xhr) => {
        $btn.prop('disabled', false).text('Save signature');
        
        console.error('Failed to save signature:', xhr);
        
        const errorMsg = xhr.responseJSON?.message || 'Failed to save signature — check console.';
        alert(errorMsg);
    };

    /**
     * Handle form submission
     */
    const handleFormSubmit = function(e) {
        // Sync Summernote content to hidden textarea
        $('#bulkBody').val($('#bulkBody').summernote('code'));
        
        // Get selected count
        const selectedCount = $('.contact-checkbox:checked').length;
        
        // Validation checks
        if (selectedCount === 0) {
            e.preventDefault();
            alert('Select at least one contact first.');
            return false;
        }
        
        if ($('#bulkBody').summernote('isEmpty')) {
            e.preventDefault();
            alert('Write a message first.');
            return false;
        }
        
        // Confirmation before sending
        const confirmed = confirm(
            `Send this email to ${selectedCount} contact${selectedCount !== 1 ? 's' : ''}?\n\nThis cannot be undone.`
        );
        
        if (!confirmed) {
            e.preventDefault();
            return false;
        }
        
        // Show loading state
        const $btn = $('#bulkSendBtn');
        $btn.prop('disabled', true).text('Sending...');
    };

    /**
     * Public API
     */
    return {
        init: init,
        updateSelectedCount: updateSelectedCount,
        getSelectedCount: () => $('.contact-checkbox:checked').length,
        clearSelection: () => {
            $('.contact-checkbox').prop('checked', false);
            updateSelectedCount();
        },
        selectAll: () => {
            $('#selectAll').prop('checked', true).trigger('change');
        }
    };
})();

// Initialize when DOM is ready
$(document).ready(() => {
    BulkEmailManager.init();
});
