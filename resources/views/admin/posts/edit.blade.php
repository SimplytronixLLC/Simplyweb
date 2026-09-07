@extends('admin.includes.masterpage-admin')

@section('content')

<div class="row">
<div class="col-lg-10">

<div class="card">
<div class="card-header">
    <h4>Edit Blog Post</h4>
</div>

<div class="card-body">

<form method="POST"
      action="{{ route('admin.posts.update', $post->id) }}"
      enctype="multipart/form-data">
@csrf

<div class="mb-3">
    <label>Title</label>
    <input type="text"
           name="title"
           value="{{ $post->title }}"
           class="form-control"
           onkeyup="convertToSlug(this.value)"
           required>
</div>

<div class="mb-3">
    <label>Slug</label>
    <input type="text"
           id="slug"
           name="slug"
           value="{{ $post->slug }}"
           class="form-control">
</div>

<div class="mb-3">
    <label>Excerpt</label>
    <textarea name="excerpt" class="form-control">{{ $post->excerpt }}</textarea>
</div>

<div class="mb-3">
    <label>Featured Image</label>

    {{-- EXISTING IMAGE --}}
    @if($post->featured_image)
        <div class="mb-2">
            <img src="{{ asset($post->featured_image) }}"
                 style="width:150px; border-radius:6px;">
        </div>
    @endif

    <input type="file" name="featured_image" class="form-control">
    <small>Upload new image to replace existing</small>
</div>

<div class="mb-3">
    <label>Content</label>
    <textarea name="content"
              id="content"
              class="form-control summernote">{{ $post->content }}</textarea>
</div>

<div class="mb-3">
    <label>Status</label>
    <select name="is_published" class="form-control">
        <option value="1" {{ $post->is_published ? 'selected' : '' }}>Publish</option>
        <option value="0" {{ !$post->is_published ? 'selected' : '' }}>Draft</option>
    </select>
</div>

<button class="btn btn-success">Update Post</button>

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
    <input type="hidden" name="featured_image_data" id="preview_image_data" value="{{ $post->featured_image }}">
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
        // No new file chosen - keep the pre-filled existing image path
        document.getElementById('previewForm').submit();
    }
}
</script>

</div>
</div>

</div>
</div>

@endsection