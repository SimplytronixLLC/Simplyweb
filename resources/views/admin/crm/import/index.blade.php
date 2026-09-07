@extends('admin.includes.masterpage-admin')

@section('content')

<div style="padding: 20px;">
    <h2>Import Master Contact List</h2>
    <p>Upload your Excel file to import 7,316 contacts</p>

    <div class="card" style="margin-top: 20px;">
        <div class="card-body">
            <form id="importForm" enctype="multipart/form-data">
                @csrf

                <div style="border: 2px dashed #3b82f6; padding: 40px; text-align: center; border-radius: 8px; margin-bottom: 20px;">
                    <input type="file" id="fileInput" name="file" accept=".xlsx,.xls" style="display: block; margin: 0 auto;">
                </div>

                <div id="fileSelected" style="display: none; margin-bottom: 20px; padding: 20px; background: #f0fdf4; border: 1px solid #86efac; border-radius: 6px;">
                    <p><strong>File Selected:</strong> <span id="selectedFile"></span></p>
                    <p id="previewInfo"></p>
                </div>

                <button type="submit" class="btn btn-primary" id="submitBtn" style="display: none; width: 100%; padding: 10px;">
                    Start Import
                </button>

                <div id="loading" style="display: none; text-align: center; padding: 20px;">
                    <p>Importing... <span id="stats"></span></p>
                </div>

                <div id="success" style="display: none; padding: 20px; background: #f0fdf4; border: 1px solid #86efac; border-radius: 6px;">
                    <h4 style="color: #166534;">✓ Import Complete!</h4>
                    <p>Created: <strong id="createdCount">0</strong></p>
                    <p>Updated: <strong id="updatedCount">0</strong></p>
                    <p>Skipped: <strong id="skippedCount">0</strong></p>
                </div>

                <div id="error" style="display: none; padding: 20px; background: #fee2e2; border: 1px solid #fca5a5; border-radius: 6px; color: #991b1b;">
                    <strong>Error:</strong> <span id="errorMsg"></span>
                </div>
            </form>
        </div>
    </div>

    <div class="card" style="margin-top: 20px;">
        <div class="card-body">
            <h4>Help</h4>
            <ul>
                <li>File must be Excel (.xlsx or .xls)</li>
                <li>Must have "Master List" sheet</li>
                <li>Total Contacts: {{ $contactCount }}</li>
            </ul>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('fileInput');
    const fileSelected = document.getElementById('fileSelected');
    const selectedFile = document.getElementById('selectedFile');
    const previewInfo = document.getElementById('previewInfo');
    const submitBtn = document.getElementById('submitBtn');
    const importForm = document.getElementById('importForm');

    // File selected
    fileInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            const file = this.files[0];
            selectedFile.textContent = file.name;
            fileSelected.style.display = 'block';
            submitBtn.style.display = 'block';

            // Try to preview
            previewFile(file);
        }
    });

    function previewFile(file) {
        const formData = new FormData();
        formData.append('file', file);
        formData.append('_token', document.querySelector('[name="_token"]').value);

        fetch('{{ route("admin.crm.import.preview") }}', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                previewInfo.innerHTML = `Found <strong>${data.valid_emails}</strong> valid emails in <strong>${data.total_rows}</strong> rows`;
            } else {
                previewInfo.innerHTML = `<span style="color: red;">Error: ${data.error}</span>`;
            }
        })
        .catch(err => {
            previewInfo.innerHTML = `<span style="color: red;">Error: ${err.message}</span>`;
        });
    }

    // Form submit
    importForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const file = fileInput.files[0];
        if (!file) {
            alert('Please select a file');
            return;
        }

        const formData = new FormData();
        formData.append('file', file);
        formData.append('skip_duplicates', 1);
        formData.append('_token', document.querySelector('[name="_token"]').value);

        document.getElementById('fileSelected').style.display = 'none';
        submitBtn.style.display = 'none';
        document.getElementById('loading').style.display = 'block';
        document.getElementById('success').style.display = 'none';
        document.getElementById('error').style.display = 'none';

        fetch('{{ route("admin.crm.import.store") }}', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('loading').style.display = 'none';

            if (data.success) {
                document.getElementById('createdCount').textContent = data.stats.created;
                document.getElementById('updatedCount').textContent = data.stats.updated;
                document.getElementById('skippedCount').textContent = data.stats.skipped;
                document.getElementById('success').style.display = 'block';
            } else {
                document.getElementById('errorMsg').textContent = data.error;
                document.getElementById('error').style.display = 'block';
            }
        })
        .catch(err => {
            document.getElementById('loading').style.display = 'none';
            document.getElementById('errorMsg').textContent = err.message;
            document.getElementById('error').style.display = 'block';
        });
    });
});
</script>

@endsection
