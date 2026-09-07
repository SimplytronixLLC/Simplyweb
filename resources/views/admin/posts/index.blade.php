@extends('admin.includes.masterpage-admin')

@section('content')

<style>
.badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: 600;
    display: inline-block;
}

.badge-success {
    background-color: #28a745 !important;
    color: #fff !important;
}

.badge-draft {
    background-color: #fd7e14 !important;
    color: #fff !important;
}
</style>

<div class="row">
<div class="col-12">

@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

<div class="card">
<div class="card-header d-flex justify-content-between align-items-center">
    <h4>Blog Posts</h4>
    <a href="{{ route('admin.posts.create') }}" class="btn btn-primary">
        + Add Post
    </a>
</div>

<div class="card-body">

<table class="table table-bordered table-striped">
<thead>
<tr>
    <th width="5%">ID</th>
    <th width="35%">Title</th>
    <th width="15%">Status</th>
    <th width="20%">Date</th>
    <th width="25%">Action</th>
</tr>
</thead>

<tbody>

@forelse($posts as $post)
<tr>
    <td>{{ $post->id }}</td>

    <td>
        <strong>{{ $post->title }}</strong><br>
        <small style="color:#777;">/{{ $post->slug }}</small>
    </td>

    <td>
        @if($post->is_published)
            <span class="badge badge-success">Published</span>
        @else
            <span class="badge badge-draft">Draft</span>
        @endif
    </td>

    <td>
        {{ $post->created_at ? $post->created_at->format('d M Y') : '-' }}<br>
        <small>
            {{ $post->created_at ? $post->created_at->format('h:i A') : '' }}
        </small>
    </td>

    <td>
        <a href="{{ route('admin.posts.edit', $post->id) }}"
           class="btn btn-sm btn-primary">
            Edit
        </a>

        <a href="{{ url('blog/'.$post->slug) }}"
           target="_blank"
           class="btn btn-sm btn-success">
            View
        </a>
    </td>
</tr>

@empty
<tr>
    <td colspan="5" class="text-center">
        No blog posts found.
    </td>
</tr>
@endforelse

</tbody>

</table>

</div>
</div>

</div>
</div>

@endsection