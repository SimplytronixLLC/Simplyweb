@extends('includes.front')

@section('content')
<main class="main__content_wrapper">

<section class="section--padding">
<div class="container">
<div class="row justify-content-center">
<div class="col-lg-6 text-center">

<div class="alert alert-success p-4">
    <h3 class="mb-2">Thank you!</h3>

    <p>Your quote request has been submitted successfully.</p>

    <p>
    Our sales team will review your request and get in touch with you.
    </p>

    <p class="small text-muted">
    You will now be redirected to the homepage.
    </p>
</div>

</div>
</div>
</div>
</section>

<script>
    setTimeout(function () {
        window.location.href = "{{ url('/') }}";
    }, 4000); // 4 seconds
</script>

</main>
@endsection
