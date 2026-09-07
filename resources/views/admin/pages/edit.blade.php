@extends('admin.includes.masterpage-admin')

@section('content')

<style>
    .sx-editor { font-family: 'Poppins', sans-serif; max-width: 780px; }

    .sx-editor .sx-editor-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
    }
    .sx-editor .sx-editor-top h4 { font-weight: 600; margin: 0; }
    .sx-editor .sx-back {
        font-size: 12.5px;
        font-weight: 600;
        color: #4F46E5;
        text-decoration: none;
    }

    .sx-editor .sx-alert {
        background: #DCFCE7;
        color: #16A34A;
        font-size: 13px;
        font-weight: 600;
        border-radius: 10px;
        padding: 10px 14px;
        margin-bottom: 16px;
    }

    .sx-field { margin-bottom: 20px; }
    .sx-field-label {
        display: block;
        font-size: 12.5px;
        font-weight: 600;
        color: #1A1F2B;
        margin-bottom: 6px;
    }
    .sx-input {
        width: 100%;
        border: 1px solid #E7EAF0;
        border-radius: 8px;
        padding: 9px 11px;
        font-size: 13.5px;
        font-family: inherit;
        color: #1A1F2B;
        background: #fff;
    }
    .sx-input:focus { outline: none; border-color: #4F46E5; }
    .sx-textarea { resize: vertical; }
    .sx-image-preview {
        display: block;
        max-width: 160px;
        margin-top: 8px;
        border-radius: 6px;
        border: 1px solid #E7EAF0;
    }
    .sx-image-row { display: flex; gap: 8px; align-items: center; }
    .sx-image-row .sx-image-input { flex: 1; }
    .sx-btn-browse {
        white-space: nowrap;
        border: 1px solid #E7EAF0;
        background: #fff;
        color: #1A1F2B;
        font-size: 12.5px;
        font-weight: 600;
        padding: 9px 14px;
        border-radius: 8px;
        cursor: pointer;
    }
    .sx-btn-browse:disabled { opacity: 0.6; cursor: default; }

    .sx-repeater {
        border: 1px solid #E7EAF0;
        border-radius: 10px;
        padding: 14px;
        background: #F7F8FA;
    }
    .sx-repeater-item {
        position: relative;
        background: #fff;
        border: 1px solid #E7EAF0;
        border-radius: 10px;
        padding: 16px 40px 4px 16px;
        margin-bottom: 12px;
    }
    .sx-repeater-item .sx-field:last-child { margin-bottom: 8px; }
    .sx-repeater-remove {
        position: absolute;
        top: 8px;
        right: 10px;
        border: none;
        background: none;
        color: #DC2626;
        font-size: 18px;
        line-height: 1;
        cursor: pointer;
        padding: 4px;
    }
    .sx-repeater-add {
        border: 1px dashed #4F46E5;
        background: #fff;
        color: #4F46E5;
        font-size: 12.5px;
        font-weight: 600;
        border-radius: 8px;
        padding: 8px 14px;
        cursor: pointer;
    }
    .sx-repeater template { display: none; }

    .sx-save {
        border: none;
        background: #4F46E5;
        color: #fff;
        font-weight: 600;
        font-size: 13.5px;
        padding: 11px 22px;
        border-radius: 8px;
        cursor: pointer;
        margin-top: 8px;
    }
</style>

<div class="sx-editor">

    <div class="sx-editor-top">
        <h4>Edit: {{ $label }}</h4>
        <a href="{{ route('admin.pages.index') }}" class="sx-back">&larr; All pages</a>
    </div>

    @if(session('success'))
        <div class="sx-alert">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.pages.update', $slug) }}">
        @csrf
        @method('PUT')

        @foreach($fields as $field)
            @include('admin.pages.partials.field', [
                'field' => $field,
                'name' => 'content[' . $field['key'] . ']',
                'value' => $page->get($field['key']),
            ])
        @endforeach

        <button type="submit" class="sx-save">Save Changes</button>
    </form>

</div>

{{-- TinyMCE for richtext fields. Replace the src below with your licensed
     TinyMCE build/API key if you have one; the {{ env("TINYMCE_API_KEY") }} CDN build shows
     a small watermark notice but works fine for internal admin use. --}}
<script src="https://cdn.tiny.cloud/1/{{ env("TINYMCE_API_KEY") }}/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
<script>
tinymce.init({
    selector: '.sx-richtext',
    height: 280,
    menubar: false,
    plugins: 'link lists image table code',
    toolbar: 'undo redo | bold italic | bullist numlist | link image table | code',
    branding: false
});

document.querySelectorAll('[data-repeater]').forEach(function (repeater) {
    var list = repeater.querySelector('.sx-repeater-items');
    var template = repeater.querySelector('template').innerHTML;
    var addBtn = repeater.querySelector('.sx-repeater-add');
    var counter = list.children.length;

    function bindRemove(node) {
        var btn = node.querySelector('.sx-repeater-remove');
        if (btn) {
            btn.addEventListener('click', function () {
                node.remove();
            });
        }
    }

    addBtn.addEventListener('click', function () {
        var html = template.split('__INDEX__').join(counter++);
        var wrapper = document.createElement('div');
        wrapper.innerHTML = html.trim();
        var node = wrapper.firstElementChild;
        list.appendChild(node);
        bindRemove(node);
    });

    Array.prototype.forEach.call(list.children, bindRemove);
});

(function () {
    var csrfInput = document.querySelector('input[name="_token"]');
    var csrfToken = csrfInput ? csrfInput.value : '';

    document.addEventListener('click', function (e) {
        var browseBtn = e.target.closest('.sx-btn-browse');
        if (!browseBtn) return;
        var row = browseBtn.closest('.sx-image-row');
        row.querySelector('.sx-image-file-input').click();
    });

    document.addEventListener('change', function (e) {
        var fileInput = e.target.closest('.sx-image-file-input');
        if (!fileInput || !fileInput.files.length) return;

        var row = fileInput.closest('.sx-image-row');
        var textInput = row.querySelector('.sx-image-input');
        var browseBtn = row.querySelector('.sx-btn-browse');
        var preview = row.parentElement.querySelector('.sx-image-preview');

        var formData = new FormData();
        formData.append('file', fileInput.files[0]);

        browseBtn.disabled = true;
        browseBtn.textContent = 'Uploading...';

        fetch('{{ route('admin.pages.upload-image') }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            body: formData,
        })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (data.success) {
                    textInput.value = data.path;
                    if (preview) {
                        preview.src = data.path;
                        preview.style.display = 'block';
                    }
                } else {
                    alert(data.message || 'Upload failed.');
                }
            })
            .catch(function () { alert('Upload failed.'); })
            .finally(function () {
                browseBtn.disabled = false;
                browseBtn.textContent = 'Browse';
                fileInput.value = '';
            });
    });
})();
</script>

@endsection
