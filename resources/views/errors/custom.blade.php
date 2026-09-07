@extends('includes.front')

@section('seo')
<title>Error - {{ $settings->meta_title ?? 'SimplyTronix' }}</title>
@stop

@section('content')
<section class="pt-5 pb-5">
    <div class="container text-center">
        <h2 class="mb-3">Something went wrong</h2>

        <p class="mb-3">
            {{ $message ?? 'An unexpected error occurred.' }}
        </p>

        @if(!empty($details))
            <div class="text-start mt-4">
                <pre style="background:#f8f8f8;padding:15px;border-radius:5px;">
{{ print_r($details, true) }}
                </pre>
            </div>
        @endif

        <a href="{{ url('/') }}" class="primary__btn mt-4">
            Go back to Home
        </a>
    </div>
</section>
@endsection
