@extends('includes.front')

@section('seo')
    <title>Simplifying your semiconductor supply chain</title>
    <meta data-rh="true" name="title" content="{{$settings->meta_title}}">
    <meta data-rh="true" name="keywords" content="Electronic Components Semiconductor Independent Distributor">
    <meta data-rh="true" name="description" content="{{$settings->meta_description}}">
    <meta data-rh="true" name="language" content="en">

    <meta data-rh="true" property="og:url" content="{{url('/')}}">
    <meta data-rh="true" property="og:site_name" content="{{$settings->meta_title}}">
    <meta data-rh="true" property="og:type" content="website">
    <meta data-rh="true" property="og:title" content="{{$settings->meta_title}}">
    <meta data-rh="true" property="og:description" content="{{$settings->meta_description}}">
    <meta data-rh="true" property="og:image" content="{{url('public')}}/{{$settings->logo}}">

    <meta data-rh="true" name="twitter:title" content="{{$settings->meta_title}}">
    <meta data-rh="true" name="twitter:site" content="@yourTwitterHandle">
    <meta data-rh="true" name="twitter:description" content="{{$settings->meta_description}}">
    <meta data-rh="true" name="twitter:creator" content="">
    <meta data-rh="true" name="twitter:card" content="summary_large_image">
    <meta data-rh="true" name="twitter:image:src" content="{{url('public')}}/{{$settings->logo}}">
    <meta data-rh="true" name="twitter:image" content="{{url('public')}}/{{$settings->logo}}">
@stop

<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-TLBX2JMYEL"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'G-TLBX2JMYEL');
</script>

@section('content')

<main class="main__content_wrapper">

    {{-- ============================================================
         Hero Slider — full-width, text left / image right
    ============================================================ --}}
<section class="hero-section" style="margin-bottom: 0px !important;">        <div class="hero-slider swiper" id="heroSwiper">
            <div class="swiper-wrapper">

                {{-- Slide 1 --}}
                <div class="swiper-slide">
                    <div class="hero-slide">
                        <div class="hero-slide__content">
                            <p class="hero-slide__eyebrow">GLobal Distribution</p>
                            <h1 class="hero-slide__title">Your Trusted Source for Electronic Components</h1>
                            <p class="hero-slide__body">Simplytronix sources hard-to-find, long-lead-time, and obsolete semiconductors from verified suppliers worldwide — with 3rd-party testing to back every shipment.</p>
                            <div class="hero-slide__actions">
                                <a class="hero-btn hero-btn--primary" href="{{ route('get_a_quote') }}">Request a Quote</a>
                                <a class="hero-btn hero-btn--ghost" href="{{ url('/available-stock') }}">Browse Stock</a>
                            </div>
                        </div>
                        <div class="hero-slide__media">
                            <img src="public/assets/front/img/slider/circuit-board-973311.webp"
                                 alt="Circuit board — electronic components distributor"
                                 fetchpriority="high" decoding="async">
                        </div>
                    </div>
                </div>

                {{-- Slide 2 --}}
                <div class="swiper-slide">
                    <div class="hero-slide">
                        <div class="hero-slide__content">
                            <p class="hero-slide__eyebrow">Quality Assurance</p>
                            <h1 class="hero-slide__title">Every Part Tested by Independent Labs</h1>
                            <p class="hero-slide__body">We partner with certified 3rd-party testing facilities to verify authenticity, functionality, and compliance — so counterfeit parts never reach your production line.</p>
                            <div class="hero-slide__actions">
                                <a class="hero-btn hero-btn--primary" href="{{ route('quality.assurance') }}">Our QA Process</a>
                                <a class="hero-btn hero-btn--ghost" href="{{ route('get_a_quote') }}">Get a Quote</a>
                            </div>
                        </div>
                        <div class="hero-slide__media">
                            <img src="public/assets/front/img/slider/Slider2.jpg" alt="3rd party component testing">
                        </div>
                    </div>
                </div>

                {{-- Slide 3 --}}
                <div class="swiper-slide">
                    <div class="hero-slide">
                        <div class="hero-slide__content">
                            <p class="hero-slide__eyebrow">Global Logistics</p>
                            <h1 class="hero-slide__title">Fast Fulfilment, Worldwide Delivery</h1>
                            <p class="hero-slide__body">With warehouses on two continents and freight partnerships across 80+ countries, we ship next-day on in-stock parts and consolidate multi-line orders to cut your freight costs.</p>
                            <div class="hero-slide__actions">
                                <a class="hero-btn hero-btn--primary" href="{{ route('get_a_quote') }}">Start an Order</a>
                                <a class="hero-btn hero-btn--ghost" href="{{ url('/contact-us') }}">Contact Us</a>
                            </div>
                        </div>
                        <div class="hero-slide__media">
                            <img src="public/assets/front/img/slider/Slider3.jpg" alt="Logistics and warehousing">
                        </div>
                    </div>
                </div>

            </div>

            {{-- Nav arrows --}}
            <button class="hero-arrow hero-arrow--prev" aria-label="Previous slide">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
            </button>
            <button class="hero-arrow hero-arrow--next" aria-label="Next slide">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
            </button>

            {{-- Pagination dots --}}
            <div class="hero-pagination swiper-pagination"></div>
        </div>
    </section>

    {{-- ============================================================
         Stats counter banner
    ============================================================ --}}
    <div class="counterup__banner--section counterup__banner__bg2 section--padding" id="funfactId">
        <div class="container">
            <div class="row row-cols-1 align-items-center">
                <div class="col">
                    <div class="counterup__banner--inner position__relative d-flex align-items-center justify-content-between">
                        <div class="counterup__items text-center">
                            <h2 class="counterup__title fs-2 fw-bold">Warehouses</h2>
                            <span class="counterup__number js-counter" data-count="2">0</span>
                        </div>
                        <div class="counterup__items text-center">
                            <h2 class="counterup__title fs-2 fw-bold">Customers</h2>
                            <span class="counterup__number js-counter" data-count="1254">0</span>
                        </div>
                        <div class="counterup__items text-center">
                            <h2 class="counterup__title fs-2 fw-bold">Daily Orders</h2>
                            <span class="counterup__number js-counter" data-count="100">0</span>
                        </div>
                        <div class="counterup__items text-center">
                            <h2 class="counterup__title fs-2 fw-bold">Manufacturers</h2>
                            <span class="counterup__number js-counter" data-count="2200">0</span>
                        </div>
                        <div class="counterup__items text-center">
                            <h2 class="counterup__title fs-2 fw-bold">SKUs</h2>
                            <span class="counterup__number js-counter" data-count="22764">0</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================
         Quick action cards (Upload BOM, RFQ, QMS, Stock)
    ============================================================ --}}
    <section class="categories__section section--padding">
        <div class="container">
            <div class="row mb--n25">

                <div class="col-lg-3 col-md-3 col-sm-6 col-6 mb-25">
                    <div class="categories__card text-center">
                        <a class="categories__card--link" href="{{ route('bom.upload') }}">
                            <span class="categories__icon">
                                <svg width="36" height="47" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><rect x="8" y="2" width="24" height="28" rx="2" fill="#2FB776"></rect><path d="M8 23H32V28C32 29.1046 31.1046 30 30 30H10C8.89543 30 8 29.1046 8 28V23Z" fill="url(#paint0_linear_87_7712)"></path><rect x="20" y="16" width="12" height="7" fill="#229C5B"></rect><rect x="20" y="9" width="12" height="7" fill="#27AE68"></rect><path d="M8 4C8 2.89543 8.89543 2 10 2H20V9H8V4Z" fill="#1D854F"></path><rect x="8" y="9" width="12" height="7" fill="#197B43"></rect><rect x="8" y="16" width="12" height="7" fill="#1B5B38"></rect><path d="M8 12C8 10.3431 9.34315 9 11 9H17C18.6569 9 20 10.3431 20 12V24C20 25.6569 18.6569 27 17 27H8V12Z" fill="#000000" fill-opacity="0.3"></path><rect y="7" width="18" height="18" rx="2" fill="url(#paint1_linear_87_7712)"></rect><path d="M13 21L10.1821 15.9L12.8763 11H10.677L9.01375 14.1286L7.37801 11H5.10997L7.81787 15.9L5 21H7.19931L8.97251 17.6857L10.732 21H13Z" fill="white"></path><defs><linearGradient id="paint0_linear_87_7712" x1="8" y1="26.5" x2="32" y2="26.5" gradientUnits="userSpaceOnUse"><stop stop-color="#163C27"></stop><stop offset="1" stop-color="#2A6043"></stop></linearGradient><linearGradient id="paint1_linear_87_7712" x1="0" y1="16" x2="18" y2="16" gradientUnits="userSpaceOnUse"><stop stop-color="#185A30"></stop><stop offset="1" stop-color="#176F3D"></stop></linearGradient></defs></g></svg>
                            </span>
                            <span class="categories__subtitle">Upload a Parts List to Check Pricing &amp; Availability</span>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-md-3 col-sm-6 col-6 mb-25">
                    <div class="categories__card text-center">
                        <a class="categories__card--link" href="{{ route('get_a_quote') }}">
                            <span class="categories__icon">
                                <svg width="50" height="47" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512 512" xml:space="preserve" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><path style="fill:#FFFFFF;" d="M4,462.4V113.6h504v303.856c-0.016,24.808-20.112,44.92-44.92,44.944H4z"></path><path style="fill:#AAC1CE;" d="M504,117.6v299.856c-0.016,22.6-18.32,40.92-40.92,40.944H8V117.6H504 M512,109.6H0v356.8h463.08c27.016,0,48.92-21.904,48.92-48.92c0-0.008,0-0.016,0-0.024V109.6z"></path><path style="fill:#25B6D2;" d="M31.92,45.6h448.16c17.632,0,31.92,14.288,31.92,31.92c0,0.008,0,0.016,0,0.024v32.184H0V77.544C-0.016,59.912,14.264,45.616,31.896,45.6C31.904,45.6,31.912,45.6,31.92,45.6z"></path><polygon style="fill:#FFFFFF;" points="460.928,93.36 143.088,93.36 159.088,62.008 460.928,62.008 "></polygon><rect x="105.416" y="206.44" style="fill:#415E72;" width="301.168" height="197.864"></rect><rect x="105.416" y="171.696" style="fill:#E04F5F;" width="301.168" height="34.752"></rect><rect x="206.104" y="337.92" style="fill:#25B6D2;" width="99.792" height="34.752"></rect><g><rect x="159.312" y="281.76" style="fill:#FFFFFF;" width="193.36" height="34.752"></rect><rect x="159.312" y="235.984" style="fill:#FFFFFF;" width="193.36" height="34.752"></rect></g><polygon style="fill:#25B6D2;" points="206.104,359.904 206.104,337.912 228.096,337.912 "></polygon></g></svg>
                            </span>
                            <span class="categories__subtitle">RFQ</span>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-md-3 col-sm-6 col-6 mb-25">
                    <div class="categories__card text-center">
                        <a class="categories__card--link" href="{{ route('quality.assurance') }}">
                            <span class="categories__icon">
                                <svg width="50" height="47" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512 512" xml:space="preserve" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><path style="fill:#38ABEC;" d="M486.881,435.574H25.119c-9.446,0-17.102-7.656-17.102-17.102V93.528c0-9.446,7.656-17.102,17.102-17.102h461.762c9.446,0,17.102,7.656,17.102,17.102v324.944C503.983,427.918,496.327,435.574,486.881,435.574z"></path><path style="fill:#E7EBEC;" d="M452.676,392.818H59.324c-4.722,0-8.551-3.829-8.551-8.551V127.733c0-4.722,3.829-8.551,8.551-8.551h393.353c4.722,0,8.551,3.829,8.551,8.551v256.534C461.228,388.99,457.399,392.818,452.676,392.818z"></path><circle style="fill:#FF5800;" cx="161.937" cy="221.795" r="59.858"></circle><circle style="fill:#FDD349;" cx="161.937" cy="221.795" r="34.205"></circle></g></svg>
                            </span>
                            <span class="categories__subtitle">Quality Management System Certificate</span>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-md-3 col-sm-6 col-6 mb-25">
                    <div class="categories__card text-center">
                        <a class="categories__card--link" href="{{ url('/available-stock') }}">
                            <span class="categories__icon">
                                <svg width="50" height="47" viewBox="0 -2 20 20" xmlns="http://www.w3.org/2000/svg" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><g id="delivery-truck" transform="translate(-2 -4)"><path id="secondary" fill="#2ca9bc" d="M20.24,10.81,19,10.5l-.79-2.77a1,1,0,0,0-1-.73H13V17h2a2,2,0,0,1,4,0h1a1,1,0,0,0,1-1V11.78A1,1,0,0,0,20.24,10.81Z"></path><path id="primary" d="M9.17,17H13V6a1,1,0,0,0-1-1H5" fill="none" stroke="#000000" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path><path id="primary-2" data-name="primary" d="M3,13v3a1,1,0,0,0,1,1h.87" fill="none" stroke="#000000" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path><path id="primary-3" data-name="primary" d="M14.87,17H13V7h4.25a1,1,0,0,1,1,.73L19,10.5l1.24.31a1,1,0,0,1,.76,1V16a1,1,0,0,1-1,1h-.89" fill="none" stroke="#000000" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path><path id="primary-4" data-name="primary" d="M9,17a2,2,0,1,1-2-2A2,2,0,0,1,9,17Zm8-2a2,2,0,1,0,2,2A2,2,0,0,0,17,15ZM3,9H9" fill="none" stroke="#000000" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></g></g></svg>
                            </span>
                            <span class="categories__subtitle">560,000 Parts in Stock</span>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ============================================================
         Why Simplytronix — text-rich value prop section
    ============================================================ --}}
    <section class="why-section section--padding">
        <div class="container">
            <div class="row align-items-center g-5">

                <div class="col-lg-5">
                    <p class="why-section__eyebrow">Why Simplytronix</p>
                    <h2 class="why-section__title">Reliable supply for every stage of your product lifecycle</h2>
                    <p class="why-section__lead">From pre-production sourcing to end-of-life component procurement, we keep your lines running. Our team specialises in semiconductors, passive components, connectors, and electromechanical parts from over 2,200 manufacturers.</p>
                    <a href="{{ url('/about-us') }}" class="why-section__cta">
                        Learn about us
                        <svg width="12" height="8" viewBox="0 0 12 8" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M11.8335 3.6178L8.26381 0.157332C8.21395 0.107774 8.1532 0.0681771 8.08544 0.0410843C8.01768 0.0139915 7.94441 0 7.87032 0C7.79624 0 7.72297 0.0139915 7.65521 0.0410843C7.58745 0.0681771 7.5267 0.107774 7.47684 0.157332C7.37199 0.262044 7.31393 0.39827 7.31393 0.539537C7.31393 0.680805 7.37199 0.817024 7.47684 0.921736L10.0943 3.45837H0.55625C0.405122 3.46829 0.26375 3.52959 0.160556 3.62994C0.057363 3.73029 0 3.86225 0 3.99929C0 4.13633 0.057363 4.26829 0.160556 4.36864C0.26375 4.46899 0.405122 4.53029 0.55625 4.54021H10.0927L7.47527 7.07826C7.37042 7.18298 7.31235 7.3192 7.31235 7.46047C7.31235 7.60174 7.37042 7.73796 7.47527 7.84267C7.52513 7.89223 7.58588 7.93182 7.65364 7.95892C7.7214 7.98601 7.79467 8 7.86875 8C7.94284 8 8.0161 7.98601 8.08386 7.95892C8.15162 7.93182 8.21238 7.89223 8.26223 7.84267L11.8335 4.38932C11.9406 4.28419 12 4.14649 12 4.00356C12 3.86063 11.9406 3.72293 11.8335 3.6178Z" fill="currentColor"/></svg>
                    </a>
                </div>

                <div class="col-lg-7">
                    <div class="why-pillars">

                        <div class="why-pillar">
                            <div class="why-pillar__icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" xmlns="http://www.w3.org/2000/svg"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622C17.176 19.29 21 14.591 21 9a12.02 12.02 0 00-.382-3.016z"/></svg>
                            </div>
                            <div>
                                <h3 class="why-pillar__title">Anti-Counterfeit Guarantee</h3>
                                <p class="why-pillar__body">Every batch is inspected against IDEA-STD-1010 and AS6081 standards before it leaves our warehouse. Suspect parts are quarantined and reported — full stop.</p>
                            </div>
                        </div>

                        <div class="why-pillar">
                            <div class="why-pillar__icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            </div>
                            <div>
                                <h3 class="why-pillar__title">Same-Day Shipping on Stock</h3>
                                <p class="why-pillar__body">Orders placed before 2 PM EST ship the same business day from our Delaware or international warehouse, with full tracking from pick to door.</p>
                            </div>
                        </div>

                        <div class="why-pillar">
                            <div class="why-pillar__icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" xmlns="http://www.w3.org/2000/svg"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                            </div>
                            <div>
                                <h3 class="why-pillar__title">Dedicated Account Support</h3>
                                <p class="why-pillar__body">You get a named account manager, not a ticket queue. We respond to RFQs within 4 business hours and provide COOs, lot traceability, and full paperwork on request.</p>
                            </div>
                        </div>

                        <div class="why-pillar">
                            <div class="why-pillar__icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" xmlns="http://www.w3.org/2000/svg"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                            </div>
                            <div>
                                <h3 class="why-pillar__title">Competitive, Transparent Pricing</h3>
                                <p class="why-pillar__body">No hidden fees. Line-item pricing on every quote, volume breaks clearly stated, and no minimum order value — from a single IC to pallet quantities.</p>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ============================================================
         Banner (Manufacturers / Categories images)
    ============================================================ --}}
    <section class="banner__section section--padding pt-0">
        <div class="container">
            <div class="row mb--n30">
                <div class="col-lg-6 col-md-6 mb-30">
                    <div class="banner__items position__relative">
                        <a class="banner__thumbnail display-block" href="{{ url('/manufacturers') }}">
                            <img class="banner__thumbnail--img banner__max--height" src="public/assets/front/img/banner/man.png" alt="Browse Manufacturers">
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 mb-30">
                    <div class="banner__items position__relative">
                        <a class="banner__thumbnail display-block" href="{{ url('/categories') }}">
                            <img class="banner__thumbnail--img banner__max--height" src="public/assets/front/img/banner/cat.png" alt="Browse Categories">
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================
         Industries We Serve — text section
    ============================================================ --}}
    <section class="industries-section section--padding" style="background:#f8fafc;">
        <div class="container">
            <div class="text-center mb-5">
                <p class="why-section__eyebrow" style="text-align:center;">Industries We Serve</p>
                <h2 class="section__heading--maintitle">Components for every sector</h2>
                <p class="industries-section__intro">Simplytronix supplies procurement teams across defence, industrial automation, automotive, medical devices, telecommunications, and consumer electronics — wherever component availability is critical.</p>
            </div>
            <div class="industries-grid">
                <div class="industry-card">
                    <div class="industry-card__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                    </div>
                    <h3 class="industry-card__title">Defence &amp; Aerospace</h3>
                    <p class="industry-card__body">MIL-spec and hi-rel components with full lot traceability, COC, and compliance documentation available on every order.</p>
                </div>
                <div class="industry-card">
                    <div class="industry-card__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                    </div>
                    <h3 class="industry-card__title">Industrial Automation</h3>
                    <p class="industry-card__body">PLCs, motor drivers, power management ICs, and sensors — sourced fast when your line is down and every hour costs money.</p>
                </div>
                <div class="industry-card">
                    <div class="industry-card__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    </div>
                    <h3 class="industry-card__title">Medical Devices</h3>
                    <p class="industry-card__body">Supply assurance for FDA-regulated device manufacturers, with the documentation and traceability your audits require.</p>
                </div>
                <div class="industry-card">
                    <div class="industry-card__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="2"/><path d="M16.24 7.76a6 6 0 010 8.49m-8.49-.01a6 6 0 010-8.49m11.31-2.82a10 10 0 010 14.14m-14.14 0a10 10 0 010-14.14"/></svg>
                    </div>
                    <h3 class="industry-card__title">Telecommunications</h3>
                    <p class="industry-card__body">RF components, optical transceivers, and network silicon from tier-1 and independent sources, shipped globally.</p>
                </div>
                <div class="industry-card">
                    <div class="industry-card__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                    </div>
                    <h3 class="industry-card__title">Automotive</h3>
                    <p class="industry-card__body">AEC-Q100/Q200 qualified parts for ADAS, powertrain, and infotainment systems, with the supplier diversity your BOM demands.</p>
                </div>
                <div class="industry-card">
                    <div class="industry-card__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                    </div>
                    <h3 class="industry-card__title">Energy &amp; Power</h3>
                    <p class="industry-card__body">Power conversion ICs, IGBTs, MOSFETs, and capacitors for renewable energy infrastructure and grid management systems.</p>
                </div>
            </div>
            <div class="text-center mt-4">
                <a href="{{ url('/industries-we-serve') }}" class="hero-btn hero-btn--primary" style="display:inline-flex;">View all industries</a>
            </div>
        </div>
    </section>

    {{-- ============================================================
         Shop by Categories — improved card grid
    ============================================================ --}}
    <section class="section--padding">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-4">
                <h2 class="section__heading--maintitle mb-0">Shop by <span style="color:#D85A30;">Categories</span></h2>
                <a href="{{ url('/categories') }}" class="sbc-view-all">
                    View all
                    <svg width="12" height="10" viewBox="0 0 12 8" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M11.8335 3.6178L8.26381 0.157332C8.21395 0.107774 8.1532 0.0681771 8.08544 0.0410843C8.01768 0.0139915 7.94441 0 7.87032 0C7.79624 0 7.72297 0.0139915 7.65521 0.0410843C7.58745 0.0681771 7.5267 0.107774 7.47684 0.157332C7.37199 0.262044 7.31393 0.39827 7.31393 0.539537C7.31393 0.680805 7.37199 0.817024 7.47684 0.921736L10.0943 3.45837H0.55625C0.405122 3.46829 0.26375 3.52959 0.160556 3.62994C0.057363 3.73029 0 3.86225 0 3.99929C0 4.13633 0.057363 4.26829 0.160556 4.36864C0.26375 4.46899 0.405122 4.53029 0.55625 4.54021H10.0927L7.47527 7.07826C7.37042 7.18298 7.31235 7.3192 7.31235 7.46047C7.31235 7.60174 7.37042 7.73796 7.47527 7.84267C7.52513 7.89223 7.58588 7.93182 7.65364 7.95892C7.7214 7.98601 7.79467 8 7.86875 8C7.94284 8 8.0161 7.98601 8.08386 7.95892C8.15162 7.93182 8.21238 7.89223 8.26223 7.84267L11.8335 4.38932C11.9406 4.28419 12 4.14649 12 4.00356C12 3.86063 11.9406 3.72293 11.8335 3.6178Z" fill="currentColor"/></svg>
                </a>
            </div>

            @if($categories->isNotEmpty())
                <div class="sbc-grid">
                    @foreach($categories as $cat)
                        <a class="sbc-card" href="{{ url('/category/'.$cat->slug) }}">
                            <div class="sbc-card__icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" xmlns="http://www.w3.org/2000/svg">
                                    <rect x="2" y="2" width="9" height="9" rx="1"/><rect x="13" y="2" width="9" height="9" rx="1"/>
                                    <rect x="2" y="13" width="9" height="9" rx="1"/><rect x="13" y="13" width="9" height="9" rx="1"/>
                                </svg>
                            </div>
                            <div class="sbc-card__name">{{ $cat->name }}</div>
                            @if(isset($categoryCounts[$cat->id]))
                                <div class="sbc-card__count">{{ number_format($categoryCounts[$cat->id]) }} parts</div>
                            @endif
                        </a>
                    @endforeach
                </div>
            @else
                <p class="text-muted">No categories available.</p>
            @endif
        </div>
    </section>

</main>

{{-- ============================================================
     Page-specific styles
============================================================ --}}
<style>
/* ── Hero ─────────────────────────────────────────── */
.hero-section {
    width: 100%;
    overflow: hidden;
}

.hero-slide {
    display: flex;
    align-items: stretch;
    min-height: 420px;
    background: #0f1a2e;
}

.hero-slide__content {
    flex: 0 0 48%;
    padding: 56px 48px 56px 60px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    z-index: 2;
}

.hero-slide__eyebrow {
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: #D85A30;
    margin: 0 0 14px;
}

.hero-slide__title {
    font-size: clamp(24px, 3vw, 40px);
    font-weight: 700;
    line-height: 1.2;
    color: #ffffff;
    margin: 0 0 16px;
}

.hero-slide__body {
    font-size: 15px;
    line-height: 1.7;
    color: rgba(255,255,255,0.72);
    margin: 0 0 28px;
    max-width: 420px;
}

.hero-slide__actions {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.hero-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    border-radius: 4px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    transition: background 0.18s, color 0.18s, border-color 0.18s;
}
.hero-btn--primary {
    background: #D85A30;
    color: #fff;
    border: 2px solid #D85A30;
}
.hero-btn--primary:hover {
    background: #bf4e28;
    border-color: #bf4e28;
    color: #fff;
    text-decoration: none;
}
.hero-btn--ghost {
    background: transparent;
    color: #fff;
    border: 2px solid rgba(255,255,255,0.4);
}
.hero-btn--ghost:hover {
    border-color: #fff;
    color: #fff;
    text-decoration: none;
}

.hero-slide__media {
    flex: 1;
    overflow: hidden;
    position: relative;
}
.hero-slide__media img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
    display: block;
}

/* Gradient fade from content into image */
.hero-slide__media::before {
    content: '';
    position: absolute;
    left: 0; top: 0; bottom: 0;
    width: 80px;
    background: linear-gradient(to right, #0f1a2e, transparent);
    z-index: 1;
}

/* Arrows */
.hero-arrow {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    z-index: 10;
    background: rgba(255,255,255,0.12);
    border: 1px solid rgba(255,255,255,0.2);
    color: #fff;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background 0.15s;
}
.hero-arrow:hover { background: rgba(255,255,255,0.25); }
.hero-arrow--prev { left: 16px; }
.hero-arrow--next { right: 16px; }

/* Pagination */
.hero-pagination { bottom: 14px !important; }
.hero-pagination .swiper-pagination-bullet { background: rgba(255,255,255,0.5); opacity: 1; }
.hero-pagination .swiper-pagination-bullet-active { background: #D85A30; }

/* Mobile hero */
@media (max-width: 767px) {
    .hero-slide { flex-direction: column; min-height: auto; }
    .hero-slide__content { flex: none; padding: 36px 24px 28px; }
    .hero-slide__media { height: 220px; flex: none; }
    .hero-slide__media::before { display: none; }
    .hero-slide__body { font-size: 14px; }
}

/* ── Why section ───────────────────────────────────── */
.why-section { background: #fff; }
.why-section__eyebrow {
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: #D85A30;
    margin: 0 0 12px;
}
.why-section__title {
    font-size: clamp(22px, 2.5vw, 32px);
    font-weight: 700;
    line-height: 1.25;
    color: #111827;
    margin: 0 0 16px;
}
.why-section__lead {
    font-size: 15px;
    line-height: 1.7;
    color: #4b5563;
    margin: 0 0 28px;
}
.why-section__cta {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    font-weight: 600;
    color: #D85A30;
    text-decoration: none;
}
.why-section__cta:hover { text-decoration: underline; color: #D85A30; }

.why-pillars { display: flex; flex-direction: column; gap: 24px; }
.why-pillar {
    display: flex;
    gap: 16px;
    align-items: flex-start;
}
.why-pillar__icon {
    flex-shrink: 0;
    width: 42px;
    height: 42px;
    background: #FAECE7;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #D85A30;
}
.why-pillar__icon svg { width: 20px; height: 20px; }
.why-pillar__title {
    font-size: 15px;
    font-weight: 600;
    color: #111827;
    margin: 0 0 4px;
}
.why-pillar__body {
    font-size: 13.5px;
    line-height: 1.6;
    color: #6b7280;
    margin: 0;
}

/* ── Industries section ────────────────────────────── */
.industries-section__intro {
    font-size: 15px;
    line-height: 1.7;
    color: #4b5563;
    max-width: 640px;
    margin: 12px auto 0;
}
.industries-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}
.industry-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    padding: 24px 20px;
    transition: box-shadow 0.2s, border-color 0.2s;
}
.industry-card:hover {
    border-color: #D85A30;
    box-shadow: 0 4px 16px rgba(216,90,48,0.09);
}
.industry-card__icon {
    width: 40px;
    height: 40px;
    background: #FAECE7;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #D85A30;
    margin-bottom: 14px;
}
.industry-card__icon svg { width: 20px; height: 20px; }
.industry-card__title {
    font-size: 15px;
    font-weight: 600;
    color: #111827;
    margin: 0 0 8px;
}
.industry-card__body {
    font-size: 13.5px;
    line-height: 1.6;
    color: #6b7280;
    margin: 0;
}
@media (max-width: 992px) {
    .industries-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 576px) {
    .industries-grid { grid-template-columns: 1fr; }
}

/* ── Category grid ─────────────────────────────────── */
.sbc-view-all {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    font-weight: 500;
    color: #D85A30;
    text-decoration: none;
}
.sbc-view-all:hover { text-decoration: underline; }

.sbc-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 14px;
}
.sbc-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    background: #ffffff;
    border: 1px solid #e8e8e8;
    border-radius: 10px;
    padding: 18px 12px 14px;
    text-decoration: none;
    transition: border-color 0.2s, box-shadow 0.2s;
}
.sbc-card:hover {
    border-color: #D85A30;
    box-shadow: 0 4px 14px rgba(216,90,48,0.1);
    text-decoration: none;
}
.sbc-card__icon {
    width: 40px;
    height: 40px;
    background: #FAECE7;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 10px;
    color: #D85A30;
}
.sbc-card__icon svg { width: 20px; height: 20px; }
.sbc-card__name {
    font-size: 13px;
    font-weight: 500;
    color: #1f2937;
    line-height: 1.35;
    margin-bottom: 4px;
}
.sbc-card__count { font-size: 11px; color: #9ca3af; }

@media (max-width: 992px) { .sbc-grid { grid-template-columns: repeat(3, 1fr); } }
@media (max-width: 576px) {
    .sbc-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; }
    .sbc-card { padding: 14px 10px; }
}
</style>

{{-- Hero Swiper init --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof Swiper !== 'undefined') {
        new Swiper('#heroSwiper', {
            loop: true,
            autoplay: { delay: 6000, disableOnInteraction: false },
            navigation: {
                nextEl: '.hero-arrow--next',
                prevEl: '.hero-arrow--prev',
            },
            pagination: {
                el: '.hero-pagination',
                clickable: true,
            },
        });
    }
});
</script>

@stop

@section('footer')
<style>
.main__content_wrapper {
    padding-top: 0 !important;
    padding-bottom: 0 !important;
}
</style>
@stop