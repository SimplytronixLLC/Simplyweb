<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="google-site-verification" content="shUNSrnGelHNLCFmanqkSPvPfum5FnECrQlT0-dis4s" />
    <meta name="robots" content="index, follow">

    @hasSection('seo')
        @yield('seo')
    @else
        <title>{{$settings->meta_title}}</title>
        <meta data-rh="true" name="title" content="{{$settings->meta_title}}">
        <meta data-rh="true" name="keywords" content="{{$settings->meta_keyword}}">
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
    @endif

   @hasSection('seo')
    {{-- canonical is provided by the page's own seo section --}}
@else
    <link rel="canonical" href="<?php echo url()->current(); ?>">
@endif
    <link rel="icon" type="image/png" href="{{ url('public/uploads')}}/{{@$settings->icon}}" />

    <link rel="stylesheet" href="{{ url('public/assets/front/css/plugins/swiper-bundle.min.css')}}">
    <link rel="stylesheet" href="{{ url('public/assets/front/css/plugins/glightbox.min.css')}}">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700&family=Rubik:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ url('public/assets/front/css/vendor/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{ url('public/assets/front/css/style.css')}}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">


    @hasSection('Schema')
        @yield('Schema')
    @else
        <script type="application/ld+json">
        {
          "@context": "https://schema.org",
          "@type": "Organization",
          "name": "{{ $settings->title ?? 'Simplytronix' }}",
          "url": "{{ url('/') }}",
          "logo": "{{ url('/public/logo.png') }}"
          @if(!empty($settings->phone))
          ,"telephone": "{{ $settings->phone }}"
          @endif
          @if(!empty($settings->address))
          ,"address": "{{ $settings->address }}"
          @endif
        }
        </script>
    @endif

    {!! @$settings->google_analytics !!}

    <script type="text/javascript">
        (function(c,l,a,r,i,t,y){
            c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
            t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
            y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
        })(window, document, "clarity", "script", "y0jt7rq3kg");
    </script>

    <style>
        /* --- Active nav link --- */
        .header__menu--link.active {
            color: #f5c4b3 !important;
            text-decoration: underline;
            text-underline-offset: 4px;
        }

        /* --- Nav hover --- */
        .header__menu--link:hover {
            color: #f5c4b3 !important;
        }

        /* --- Sticky toolbar label --- */
        .offcanvas__stikcy--toolbar__label {
            font-size: 11px;
            letter-spacing: 0.03em;
        }

        /* ═══════════════════════════════════════════════
           FULL-WIDTH NAV BAR (no category sidebar)
        ═══════════════════════════════════════════════ */
        .header__bottom--inner {
            justify-content: space-between;
        }

        /* Nav fills the full bar width */
        .header__right--area {
            width: 100%;
        }

        .header__menu--wrapper {
            gap: 0;
            width: 100%;
            justify-content: flex-start;
        }

        .header__menu--items {
            flex: 1;
            text-align: center;
        }

        .header__menu--link {
            display: block;
            padding: 12px 8px;
            white-space: nowrap;
        }

        /* ═══════════════════════════════════════════════
           MOBILE NAV DRAWER
        ═══════════════════════════════════════════════ */

        .mob-nav-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.45);
            z-index: 88888;
        }
        .mob-nav-overlay.open { display: block; }

        .mob-nav-drawer {
            position: fixed;
            top: 0;
            right: -100%;
            width: 280px;
            max-width: 90vw;
            height: 100%;
            background: #fff;
            z-index: 99999;
            display: flex;
            flex-direction: column;
            transition: right .25s ease;
            box-shadow: -4px 0 20px rgba(0,0,0,0.15);
        }
        .mob-nav-drawer.open { right: 0; }

        .mob-nav-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 16px;
            border-bottom: 1px solid #eee;
            background: #D85A30;
        }
        .mob-nav-head span {
            color: #fff;
            font-size: 14px;
            font-weight: 600;
        }
        .mob-nav-close {
            background: none;
            border: none;
            color: #fff;
            font-size: 22px;
            line-height: 1;
            cursor: pointer;
            padding: 0 4px;
        }

        .mob-nav-list {
            list-style: none;
            margin: 0;
            padding: 8px 0;
            overflow-y: auto;
            flex: 1;
        }
        .mob-nav-list li a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 13px 20px;
            font-size: 14px;
            color: #333;
            text-decoration: none;
            border-bottom: 1px solid #f5f5f5;
            transition: background .12s, color .12s;
        }
        .mob-nav-list li a:hover,
        .mob-nav-list li a.active {
            background: #fff3ef;
            color: #D85A30;
        }
        .mob-nav-list li a svg {
            flex-shrink: 0;
            opacity: .5;
        }
        .mob-nav-list li a.active svg { opacity: 1; }
        
        /* Hide hamburger by default */
        #mobNavOpen {
            display: none !important;
        }
        
        /* Show only on mobile */
        @media (max-width: 991.98px) {
            #mobNavOpen {
                display: flex !important;
                align-items: center;
                justify-content: center;
            }
        }


    </style>

    @yield('style')
</head>

<body>

<!-- HEADER -->
<div class="main__header">
    <div class="container-fluid px-10">
        <div class="main__header--inner position__relative d-flex justify-content-between align-items-center">


            <div class="main__logo">
                <h1 class="main__logo--title">
                    <a class="main__logo--link" href="{{url('/')}}">
                        <img class="main__logo--img" src="{{url('public/assets/front/img/logo/logo.jpg')}}" alt="logo-img">
                    </a>
                </h1>
            </div>

            <div class="header__search--widget d-none d-lg-block header__sticky--none">
                <form class="d-flex header__search--form border-radius-5" action="{{ url('shop') }}" method="GET">
                    <div class="header__search--box">
                        <label>
                            <input class="header__search--input" required="" name="key" placeholder="Part #/ Keyword" type="text">
                        </label>
                        <button class="header__search--button bg__primary text-white" aria-label="search button" type="submit">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M15.6952 14.4991L11.7663 10.5588C12.7765 9.4008 13.33 7.94381 13.33 6.42703C13.33 2.88322 10.34 0 6.66499 0C2.98997 0 0 2.88322 0 6.42703C0 9.97085 2.98997 12.8541 6.66499 12.8541C8.04464 12.8541 9.35938 12.4528 10.4834 11.6911L14.4422 15.6613C14.6076 15.827 14.8302 15.9184 15.0687 15.9184C15.2944 15.9184 15.5086 15.8354 15.6711 15.6845C16.0166 15.364 16.0276 14.8325 15.6952 14.4991ZM6.66499 1.67662C9.38141 1.67662 11.5913 3.8076 11.5913 6.42703C11.5913 9.04647 9.38141 11.1775 6.66499 11.1775C3.94857 11.1775 1.73869 9.04647 1.73869 6.42703C1.73869 3.8076 3.94857 1.67662 6.66499 1.67662Z" fill="currentColor" />
                            </svg>
                        </button>
                    </div>
                </form>
            </div>

            

           
            <button id="mobNavOpen" aria-label="Open navigation menu"
                style="display:none;background:none;border:none;padding:6px 8px;cursor:pointer;align-items:center;justify-content:center;flex-shrink:0;">
                <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" fill="none" stroke="#333" stroke-width="2.2" stroke-linecap="round" viewBox="0 0 24 24">
                    <line x1="3" y1="6"  x2="21" y2="6"/>
                    <line x1="3" y1="12" x2="21" y2="12"/>
                    <line x1="3" y1="18" x2="21" y2="18"/>
                </svg>
            </button>

            <div class="header__account header__sticky--block">
                <ul class="header__account--wrapper d-flex align-items-center">
                    <li class="header__account--items header__account--search__items d-sm-2-none">
                        <a class="header__account--btn" href="{{ url('/shop') }}">
                            <svg class="product__items--action__btn--svg" xmlns="http://www.w3.org/2000/svg" width="22.51" height="20.443" viewBox="0 0 512 512">
                                <path d="M221.09 64a157.09 157.09 0 10157.09 157.09A157.1 157.1 0 00221.09 64z" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="32" />
                                <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-miterlimit="10" stroke-width="32" d="M338.29 338.29L448 448" />
                            </svg>
                            <span class="visually-hidden">Search</span>
                        </a>
                    </li>
                </ul>
            </div>

        </div>
    </div>

    <div class="header__bottom bg__primary">
        <div class="container">
            <div class="header__bottom--inner position__relative d-flex align-items-center">

                <!-- Main nav links — full width, no category sidebar -->
                <div class="header__right--area d-flex justify-content-between align-items-center w-100">

                    {{-- Desktop nav --}}
                    <div class="header__menu d-none d-lg-block w-100">
                        <nav class="header__menu--navigation">
                            <ul class="header__menu--wrapper d-flex flex-nowrap align-items-center">
                                <li class="header__menu--items me-3">
                                    <a class="header__menu--link text-white {{ Request::is('available-stock') ? 'active' : '' }}" href="{{ url('/available-stock') }}">Available Stock</a>
                                </li>
                                <li class="header__menu--items me-3">
                                    <a class="header__menu--link text-white {{ Request::is('about-us') ? 'active' : '' }}" href="{{ url('/about-us') }}">About Us</a>
                                </li>
                                <li class="header__menu--items me-3">
                                    <a class="header__menu--link text-white {{ Request::is('industries-we-serve') ? 'active' : '' }}" href="{{ url('/industries-we-serve') }}">Industries</a>
                                </li>
                                <li class="header__menu--items me-3">
                                    <a class="header__menu--link text-white {{ Request::is('bom-upload') ? 'active' : '' }}" href="{{ route('bom.upload') }}">Upload BOM</a>
                                </li>
                                <li class="header__menu--items me-3">
                                    <a class="header__menu--link text-white {{ Request::is('quality-assurance') ? 'active' : '' }}" href="{{ route('quality.assurance') }}">Quality</a>
                                </li>
                                <li class="header__menu--items me-3">
                                    <a class="header__menu--link text-white {{ Request::is('news-alerts') ? 'active' : '' }}" href="{{ url('/news-alerts') }}">News</a>
                                </li>
                                <li class="header__menu--items me-3">
                                    <a class="header__menu--link text-white {{ Request::is('contact-us') ? 'active' : '' }}" href="{{ url('/contact-us') }}">Contact</a>
                                </li>
                            </ul>
                        </nav>
                    </div>


                </div>

            </div>
        </div>
    </div>

    <!-- Offcanvas sticky toolbar -->
    <div class="offcanvas__stikcy--toolbar">
        <ul class="d-flex justify-content-between">
            <li class="offcanvas__stikcy--toolbar__list">
                <a class="offcanvas__stikcy--toolbar__btn" href="{{ url('/available-stock') }}">
                    <span class="offcanvas__stikcy--toolbar__icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                            <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
                        </svg>
                    </span>
                    <span class="offcanvas__stikcy--toolbar__label">Stock</span>
                </a>
            </li>
            <li class="offcanvas__stikcy--toolbar__list">
                <a class="offcanvas__stikcy--toolbar__btn search__open--btn" href="javascript:void(0)" data-offcanvas>
                    <span class="offcanvas__stikcy--toolbar__icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 512 512">
                            <path d="M221.09 64a157.09 157.09 0 10157.09 157.09" fill="none" stroke="currentColor" stroke-width="32"/>
                            <path d="M338.29 338.29L448 448" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="32"/>
                        </svg>
                    </span>
                    <span class="offcanvas__stikcy--toolbar__label">Search</span>
                </a>
            </li>
            <li class="offcanvas__stikcy--toolbar__list">
                <a class="offcanvas__stikcy--toolbar__btn" href="{{ url('/about-us') }}">
                    <span class="offcanvas__stikcy--toolbar__icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="7" r="4"/>
                            <path d="M5.5 21a6.5 6.5 0 0113 0"/>
                        </svg>
                    </span>
                    <span class="offcanvas__stikcy--toolbar__label">About</span>
                </a>
            </li>
            <li class="offcanvas__stikcy--toolbar__list">
                <a class="offcanvas__stikcy--toolbar__btn" href="{{ url('/contact-us') }}">
                    <span class="offcanvas__stikcy--toolbar__icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 16.92V21a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014 2h4.09"/>
                        </svg>
                    </span>
                    <span class="offcanvas__stikcy--toolbar__label">Contact</span>
                </a>
            </li>
        </ul>
    </div>
    <!-- End Offcanvas sticky toolbar -->

    <!-- Mobile search overlay -->
    <div class="predictive__search--box">
        <div class="predictive__search--box__inner">
            <h2 class="predictive__search--title">Search Products</h2>
            <form class="predictive__search--form" action="{{ url('/shop') }}" method="GET">
                <label>
                    <input class="predictive__search--input" name="key" placeholder="Part # / Keyword" type="text" required>
                </label>
                <button class="predictive__search--button text-white" aria-label="search button" type="submit">
                    <svg class="product__items--action__btn--svg" xmlns="http://www.w3.org/2000/svg" width="30.51" height="25.443" viewBox="0 0 512 512">
                        <path d="M221.09 64a157.09 157.09 0 10157.09 157.09A157.1 157.1 0 00221.09 64z" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="32" />
                        <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-miterlimit="10" stroke-width="32" d="M338.29 338.29L448 448" />
                    </svg>
                </button>
            </form>
        </div>
        <button class="predictive__search--close__btn" aria-label="search close" data-offcanvas>
            <svg class="predictive__search--close__icon" xmlns="http://www.w3.org/2000/svg" width="40.51" height="30.443" viewBox="0 0 512 512">
                <path fill="currentColor" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M368 368L144 144M368 144L144 368" />
            </svg>
        </button>
    </div>

</div>
<!-- END HEADER -->

{{-- ── Mobile nav drawer ── --}}
<div class="mob-nav-overlay" id="mobNavOverlay"></div>
<div class="mob-nav-drawer" id="mobNavDrawer">
    <div class="mob-nav-head">
        <span>Menu</span>
        <button class="mob-nav-close" id="mobNavClose" aria-label="Close menu">&#x2715;</button>
    </div>
    <ul class="mob-nav-list">
        <li>
            <a href="{{ url('/available-stock') }}" class="{{ Request::is('available-stock') ? 'active' : '' }}">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                Available Stock
            </a>
        </li>
        <li>
            <a href="{{ url('/about-us') }}" class="{{ Request::is('about-us') ? 'active' : '' }}">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="7" r="4"/><path d="M5.5 21a6.5 6.5 0 0113 0"/></svg>
                About Us
            </a>
        </li>
        <li>
            <a href="{{ url('/industries-we-serve') }}" class="{{ Request::is('industries-we-serve') ? 'active' : '' }}">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 21h18M9 21V9l6-6v18M5 21V13l4-4"/></svg>
                Industries
            </a>
        </li>
        <li>
            <a href="{{ route('bom.upload') }}" class="{{ Request::is('bom-upload') ? 'active' : '' }}">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                Upload BOM
            </a>
        </li>
        <li>
            <a href="{{ route('quality.assurance') }}" class="{{ Request::is('quality-assurance') ? 'active' : '' }}">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                Quality
            </a>
        </li>
        <li>
            <a href="{{ url('/news-alerts') }}" class="{{ Request::is('news-alerts') ? 'active' : '' }}">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l6 6v8a2 2 0 01-2 2z"/><polyline points="15 2 15 8 21 8"/></svg>
                News
            </a>
        </li>
        <li>
            <a href="{{ url('/contact-us') }}" class="{{ Request::is('contact-us') ? 'active' : '' }}">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 01-2.18 2A19.79 19.79 0 013.07 3.18 2 2 0 015 1h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L9.09 8.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
                Contact
            </a>
        </li>
    </ul>
</div>

@yield('content')

<footer class="footer__section footer__bg">
    <div class="container">

        <div class="newsletter__area">
            <div class="newsletter__inner d-flex justify-content-between align-items-center">
                <div class="newsletter__content">
                    <h2 class="newsletter__title">Subscribe <span class="text__secondary">Newsletter</span></h2>
                    <p class="newsletter__desc">Don't wait make a smart & logical quote here. Its pretty easy.</p>
                </div>
                <div class="newsletter__subscribe">
                    <form class="newsletter__subscribe--form" action="#">
                        <label>
                            <input class="newsletter__subscribe--input" placeholder=" Enter Your Email" type="text">
                        </label>
                        <button class="newsletter__subscribe--button" type="submit">Subscribe Now</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="main__footer">
            <div class="row">

                <div class="col-lg-3 col-md-6">
                    <div class="footer__widget">
                        <h2 class="footer__widget--title">Company
                            <button class="footer__widget--button" aria-label="footer widget button"></button>
                        </h2>
                        <ul class="footer__widget--menu footer__widget--inner">
                            <li class="footer__widget--menu__list">
                                <a class="footer__widget--menu__text" href="{{ url('/about-us') }}">SimplyTronix - Global Electronic Components Distributor</a>
                            </li>
                            <li class="footer__widget--menu__list">
                                <a class="footer__widget--menu__text" href="{{ route('contact_us') }}">Contact Us</a>
                            </li>
                            <li class="footer__widget--menu__list">
                                <a class="footer__widget--menu__text" href="{{url('/')}}">Careers</a>
                            </li>
                            <li class="footer__widget--menu__list">
                                <a class="footer__widget--menu__text" href="{{ route('terms') }}" target="_blank">Terms and Conditions</a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="footer__widget">
                        <h2 class="footer__widget--title">Support
                            <button class="footer__widget--button" aria-label="footer widget button"></button>
                        </h2>
                        <ul class="footer__widget--menu footer__widget--inner">
                            <li class="footer__widget--menu__list">
                                <a class="footer__widget--menu__text" href="{{ route('bom.upload') }}">Upload BOM</a>
                            </li>
                            <li class="footer__widget--menu__list">
                                <a class="footer__widget--menu__text" href="{{url('/')}}">Track Order</a>
                            </li>
                            <li class="footer__widget--menu__list">
                                <a class="footer__widget--menu__text" href="{{ route('quality.assurance') }}">Quality Assurance</a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="footer__widget">
                        <h2 class="footer__widget--title">Connect with us
                            <button class="footer__widget--button" aria-label="footer widget button"></button>
                        </h2>
                        <div class="footer__widget--inner">
                            <p><span style="color:#ffffff;">+1 302-600-2554</span></p>
                            <p class="footer__widget--desc">
                                9:30am to 12pm GMT +5:30<br>
                                9:30am to 5:30 pm GMT -4:00<br>
                                Monday through Friday.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="footer__widget">
                        <h2 class="footer__widget--title">Registered Office
                            <button class="footer__widget--button" aria-label="footer widget button"></button>
                        </h2>
                        <div class="footer__widget--inner">
                            <p style="color:#ffffff; margin:0;">
                                Simplytronix LLC<br>
                                1007 N Orange St<br>
                                Suite# 1382<br>
                                Wilmington, DE 19801<br>
                                United States
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="footer__bottom">
            <div class="container">
                <div class="footer__bottom--inenr d-flex justify-content-center align-items-center">
                    <p class="copyright__content">
                        <span class="text__secondary">© 2026</span> Powered by
                        <a class="copyright__content--link" target="_blank" href="{{url('/')}}">Simplytronix</a>.
                        All Rights Reserved.
                        <a href="https://simplytronix.com/" style="color:white">Simplytronix LLC</a>
                    </p>
                </div>
            </div>
        </div>

    </div>
</footer>

<button id="scroll__top" class="active">
    <svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512">
        <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="48" d="M112 244l144-144 144 144M256 120v292"></path>
    </svg>
</button>

{{-- Vendor scripts --}}
<script src="{{ url('public/assets/front/js/vendor/popper.js')}}"></script>
<script src="{{ url('public/assets/front/js/vendor/bootstrap.min.js')}}"></script>
<script src="{{ url('public/assets/front/js/plugins/swiper-bundle.min.js')}}"></script>
<script src="{{ url('public/assets/front/js/plugins/glightbox.min.js')}}"></script>
<script src="{{ url('public/assets/front/js/script.js')}}"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>


{{-- Mobile layout fixes --}}
<script>
(function () {
    function toggleMenu() {
        document.querySelectorAll('.offcanvas__header--menu__open').forEach(function(el) {
            el.style.display = window.innerWidth <= 991 ? 'none' : '';
        });
    }

    toggleMenu();
    window.addEventListener('resize', toggleMenu);
})();
</script>

{{-- Mobile nav drawer --}}
<script>
(function () {
    const open    = document.getElementById('mobNavOpen');
    const close   = document.getElementById('mobNavClose');
    const drawer  = document.getElementById('mobNavDrawer');
    const overlay = document.getElementById('mobNavOverlay');
    if (!open || !drawer) return;

    function openDrawer()  { drawer.classList.add('open');  overlay.classList.add('open');  document.body.style.overflow = 'hidden'; }
    function closeDrawer() { drawer.classList.remove('open'); overlay.classList.remove('open'); document.body.style.overflow = ''; }

    open.addEventListener('click', openDrawer);
    close.addEventListener('click', closeDrawer);
    overlay.addEventListener('click', closeDrawer);
})();
</script>

{{-- Session duration tracking --}}
<script>
(function () {
    const startTime = Date.now();
    const visitorId = '{{ request()->cookie("visitor_id") ?? "" }}';
    if (!visitorId) return;

    function sendDuration() {
        const seconds = Math.round((Date.now() - startTime) / 1000);
        if (seconds < 2) return;
        navigator.sendBeacon('/track-session-duration', JSON.stringify({
            _token: '{{ csrf_token() }}',
            seconds: seconds
        }));
    }

    window.addEventListener('beforeunload', sendDuration);
    window.addEventListener('pagehide', sendDuration);
    document.addEventListener('visibilitychange', function () {
        if (document.visibilityState === 'hidden') sendDuration();
    });
})();
</script>

{{-- Part search tracking --}}
<script>
function trackPartSearch(partNumber, source = 'search') {
    if (!partNumber) return;
    fetch('{{ route('track.part.search') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ part_number: partNumber, source: source })
    });
}
</script>

@yield('footer')

</body>
</html>