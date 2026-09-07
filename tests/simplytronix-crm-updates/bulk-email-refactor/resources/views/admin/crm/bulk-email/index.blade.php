@extends('admin.includes.masterpage-admin')

@section('content')

@if(Session::has('status'))
    <div class="alert alert-success alert-dismissable fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        {{ Session::get('status') }}
    </div>
@endif

<x-admin.crm.bulk-email.stats-bar :contacts="$contacts" />

<div class="bulk-email-wrapper">
    <div class="bulk-email-grid">
        <x-admin.crm.bulk-email.contact-selector :contacts="$contacts" />
        <x-admin.crm.bulk-email.compose-panel />
    </div>
</div>

<x-admin.crm.bulk-email.signature-editor :signature="$signature" />

@endsection

@push('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-bs4.min.css" rel="stylesheet">
    <link href="{{ asset('css/crm/bulk-email.css') }}" rel="stylesheet">
@endpush

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-bs4.min.js"></script>
    <script src="{{ asset('js/crm/bulk-email.js') }}"></script>
@endpush
