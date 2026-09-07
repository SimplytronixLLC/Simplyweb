@extends('includes.front')

@php
    $page = \App\Models\Page::content('terms');
@endphp

@section('content')
<main class="main__content_wrapper">

    <!-- Breadcrumb Section -->
    <section class="breadcrumb__section breadcrumb__bg">
        <div class="container">
            <div class="row">
                <div class="col text-center">
                    <div class="breadcrumb__content">
                        <h1 class="breadcrumb__content--title mb-3">{{ $page->get('title', 'Terms & Conditions') }}</h1>
                        <ul class="breadcrumb__content--menu d-flex justify-content-center">
                            <li class="breadcrumb__content--menu__items"><a href="{{ url('/') }}">Home</a></li>
                            <li class="breadcrumb__content--menu__items"><span>Terms & Conditions</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Terms Content Section -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col">

                    {!! $page->get('body', '<p>This is a styled HTML version of your Terms and Conditions for best readability across all devices.</p>') !!}

                    <p class="mt-4"><strong>Last Updated:</strong> {{ now()->format('F d, Y') }}</p>

                </div>
            </div>
        </div>
    </section>

</main>
@endsection
