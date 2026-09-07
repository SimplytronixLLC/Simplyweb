@extends('includes.front')

@php
    $page = \App\Models\Page::content('terms');
@endphp

@section('title', $page->get('title', 'Terms and Conditions'))

@section('content')
<div class="container py-5">
    <h1>{{ $page->get('title', 'Terms and Conditions') }}</h1>
    <div>
        {!! $page->get('body', '<p>This is a styled HTML version of your Terms and Conditions for best readability across all devices.</p>') !!}
    </div>
</div>
@endsection
