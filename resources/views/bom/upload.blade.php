@extends('includes.front') 

@section('seo')

<title>BOM Upload - {{ $settings->meta_title ?? '' }}</title>
<meta name="title" content="{{ $settings->meta_title ?? '' }}">
<meta name="keywords" content="{{ $settings->meta_keyword ?? '' }}">
<meta name="description" content="Upload your Bill of Materials (BOM) and receive consolidated pricing from SimplyTronix.">
<meta name="language" content="en">

<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:site_name" content="{{ $settings->meta_title ?? '' }}">
<meta property="og:type" content="website">
<meta property="og:title" content="BOM Upload - {{ $settings->meta_title ?? '' }}">
<meta property="og:description" content="Submit your BOM for fast semiconductor sourcing support.">
<meta property="og:image" content="{{ url('public') }}/{{ $settings->logo ?? '' }}">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="BOM Upload - {{ $settings->meta_title ?? '' }}">
<meta name="twitter:description" content="Upload your BOM and get fast quote support.">
<meta name="twitter:image" content="{{ url('public') }}/{{ $settings->logo ?? '' }}">

@stop 

@section('content')

<main class="main__content_wrapper">
        
<section class="breadcrumb__section breadcrumb__bg" style="position: relative;">
    <div style="position: absolute; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.55); z-index:1;"></div>
    <div class="container" style="position: relative; z-index: 2;">
        <div class="row row-cols-1">
            <div class="col text-center">
                <div class="breadcrumb__content text-white py-2">
                    <h1 class="breadcrumb__content--title mb-2" style="color: #fff;">
                        Upload Your BOM
                    </h1>
                    <p class="mb-0" style="color: #fff; font-size: 16px;">
                        Submit your Bill of Materials and receive consolidated pricing fast.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="cart__section section--padding">
    <div class="container">
        <div class="cart__section--inner">
            <form id="bomUploadForm" action="{{ route('bom.process') }}" method="post" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-lg-3"></div>

                    <div class="col-lg-6">
                        <div class="account__login">

                            <div class="account__login--header mb-25">
                                <h2 class="account__login--header__title mb-10">
                                    Got More Than One Part?
                                </h2>
                                <p class="account__login--header__desc">
                                    Upload your complete Bill of Materials and receive consolidated pricing & availability.
                                </p>
                            </div>

                            @if(Session::has('fail'))
                                <div class="alert alert-danger">
                                    {{ Session::get('fail') }}
                                </div>
                            @endif

                            <div class="account__login--inner">

                                <label class="d-block">
                                    <input class="account__login--input" name="name" required placeholder="Name" type="text">
                                </label>

                                <label class="d-block">
                                    <input class="account__login--input" name="email" required placeholder="Email Address" type="email">
                                </label>

                                <label class="d-block">
                                    <input class="account__login--input" name="phone" required placeholder="Contact Number" type="text">
                                </label>

                                <label class="d-block">
                                    <input class="account__login--input" name="company" required placeholder="Company Name" type="text">
                                </label>

                                <label class="d-block">
                                    <input type="file" name="bom_file" class="account__login--input" accept=".csv,.xls,.xlsx" required>
                                </label>

                                <div class="progress mt-3 d-none" id="uploadProgressWrapper" style="height:20px;">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated"
                                        id="uploadProgressBar"
                                        style="width:0%">
                                        0%
                                    </div>
                                </div>

                                <label class="d-block mt-3">
                                    <textarea class="account__login--input" name="comments" placeholder="Additional Instructions"></textarea>
                                </label>

                            </div>

                            <div class="g-recaptcha mb-3"
                                 data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}">
                            </div>

                            <button class="account__login--btn primary__btn" type="submit">
                                Submit BOM
                            </button>

                        </div>
                    </div>

                    <div class="col-lg-3"></div>
                </div>

            </form>
        </div>
    </div>
</section>

<script src="https://www.google.com/recaptcha/api.js" async defer></script>

<script>
document.getElementById('bomUploadForm').addEventListener('submit', function(e) {
    e.preventDefault();

    let form = this;
    let formData = new FormData(form);
    let progressWrapper = document.getElementById('uploadProgressWrapper');
    let progressBar = document.getElementById('uploadProgressBar');
    let submitBtn = form.querySelector('button[type="submit"]');

    submitBtn.disabled = true;

    progressWrapper.classList.remove('d-none');
    progressBar.classList.remove('bg-danger','bg-success');
    progressBar.classList.add('progress-bar-animated');
    progressBar.style.width = '0%';
    progressBar.innerHTML = '0%';

    let xhr = new XMLHttpRequest();
    xhr.open('POST', form.action, true);
    xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
    xhr.setRequestHeader('Accept', 'application/json');

    xhr.upload.addEventListener('progress', function(e) {
        if (e.lengthComputable) {
            let percent = Math.round((e.loaded / e.total) * 100);
            progressBar.style.width = percent + '%';
            progressBar.innerHTML = percent + '%';
        }
    });

    xhr.onload = function() {

        submitBtn.disabled = false;

        let existing = document.getElementById('ajaxMessage');
        if (existing) existing.remove();

        if (xhr.status === 200) {

            let response = JSON.parse(xhr.responseText);

            progressBar.classList.remove('progress-bar-animated');
            progressBar.classList.add('bg-success');
            progressBar.innerHTML = 'Upload Complete';

            let successHTML = `
                <div id="ajaxMessage" class="alert alert-success text-center shadow-sm mt-4">
                    <h5 class="mb-2">✔ BOM Submitted Successfully</h5>
                    <p class="mb-1">
                        Your Reference ID: <strong>${response.bom_id}</strong>
                    </p>
                    <p class="mb-1">
                        Our team will contact you shortly.
                    </p>
                    <p class="mb-0">
                        For urgent inquiries email 
                        <a href="mailto:sales@simplytronix.com" class="fw-bold">
                            sales@simplytronix.com
                        </a>
                    </p>
                </div>
            `;

            document.querySelector('.account__login')
                .insertAdjacentHTML('afterbegin', successHTML);

            form.reset();

            if (typeof grecaptcha !== 'undefined') {
                grecaptcha.reset();
            }

        } else if (xhr.status === 422) {

            let response = JSON.parse(xhr.responseText);
            progressBar.classList.remove('progress-bar-animated');
            progressBar.classList.add('bg-danger');
            progressBar.innerHTML = 'Validation Failed';

            let errorMessage = 'Please check required fields.';
            if (response.errors) {
                errorMessage = Object.values(response.errors).flat().join('<br>');
            }

            let errorHTML = `
                <div id="ajaxMessage" class="alert alert-danger text-center shadow-sm mt-4">
                    ${errorMessage}
                </div>
            `;

            document.querySelector('.account__login')
                .insertAdjacentHTML('afterbegin', errorHTML);

        } else {

            progressBar.classList.remove('progress-bar-animated');
            progressBar.classList.add('bg-danger');
            progressBar.innerHTML = 'Upload Failed';

            let errorHTML = `
                <div id="ajaxMessage" class="alert alert-danger text-center shadow-sm mt-4">
                    Something went wrong. Please try again.
                </div>
            `;

            document.querySelector('.account__login')
                .insertAdjacentHTML('afterbegin', errorHTML);
        }
    };

    xhr.send(formData);
});
</script>

</main>

@stop

@section('footer') 
@stop