@extends('admin.includes.masterpage-admin')

@section('content')

<div class="row">
<div class="col-lg-10">

<div class="card">

<div class="card-header">
    <h4>Create Blog Post</h4>
</div>

<div class="card-body">

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<form method="POST"
      action="{{ route('admin.posts.store') }}"
      enctype="multipart/form-data">

@csrf

<div class="mb-3">
    <label>Title</label>

    <input type="text"
           name="title"
           class="form-control"
           value="{{ old('title') }}"
           
           required>
</div>

<div class="mb-3">
    <label>Slug</label>

    <input type="text"
           id="slug"
           name="slug"
           value="{{ old('slug') }}"
           class="form-control">
</div>

<div class="mb-3">
    <label>Excerpt</label>

    <textarea name="excerpt"
              class="form-control"
              rows="4">{{ old('excerpt') }}</textarea>
</div>

<div class="mb-3">
    <label>Featured Image</label>

    <input type="file"
           name="featured_image"
           class="form-control">
</div>

<div class="mb-3">
    <label>Content</label>

    <textarea name="content"
              id="content"
              class="form-control summernote">{{ old('content') }}</textarea>
</div>

<div class="mb-3">
    <label>Status</label>

    <select name="is_published" class="form-control">
        <option value="1" {{ old('is_published') == '1' ? 'selected' : '' }}>
            Publish
        </option>

        <option value="0" {{ old('is_published') == '0' ? 'selected' : '' }}>
            Draft
        </option>
    </select>
</div>

<button type="submit" class="btn btn-success">
    Publish Post
</button>

<button type="button" class="btn btn-outline-secondary" onclick="previewPost()">
    Preview
</button>


</form>

<!-- Hidden preview form - opens in new tab, posts current editor content -->
<form id="previewForm"
      method="POST"
      action="{{ route('admin.posts.preview') }}"
      target="_blank"
      style="display:none;">
    @csrf
    <input type="hidden" name="title" id="preview_title">
    <input type="hidden" name="excerpt" id="preview_excerpt">
    <input type="hidden" name="featured_image_data" id="preview_image_data">
    <textarea name="content" id="preview_content"></textarea>
</form>

<script>
function previewPost()
{
    document.getElementById('preview_title').value =
        document.querySelector('input[name="title"]').value;

    document.getElementById('preview_excerpt').value =
        document.querySelector('textarea[name="excerpt"]').value;

    var currentContent = tinymce.get('content').getContent();
    document.getElementById('preview_content').value = currentContent;

    var fileInput = document.querySelector('input[name="featured_image"]');
    var file = fileInput && fileInput.files && fileInput.files[0];

    if (file) {
        var reader = new FileReader();
        reader.onload = function (e) {
            document.getElementById('preview_image_data').value = e.target.result;
            document.getElementById('previewForm').submit();
        };
        reader.readAsDataURL(file);
    } else {
        document.getElementById('preview_image_data').value = '';
        document.getElementById('previewForm').submit();
    }
}
</script>

</div>
</div>

</div>
</div>

<script>
function convertToSlug(text)
{
    let slug = text.toLowerCase()
        .replace(/ /g,'-')
        .replace(/[^\w-]+/g,'');

    document.getElementById("slug").value = slug;
}
</script>

@endsection