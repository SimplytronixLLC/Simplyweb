@extends('includes.front')

@push('meta')
    <title>404 - Page Not Found | {{ $settings->meta_title ?? 'Simplytronix' }}</title>
    <meta name="robots" content="noindex, follow">
    <meta name="description" content="Page not found. Search for your required part number or explore our available inventory.">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
@endpush

@section('content')
<section class="error-area py-120">
    <div class="container">
        <div class="row">
            <div class="col-md-7 mx-auto text-center">
                <div class="error-wrapper">

                    {{-- Error Code --}}
                    <h1 class="display-1 fw-bold text-danger mb-0">404</h1>
                    <h2 class="mb-3">Page Not Found</h2>

                    <p class="mb-4">
                        The page you are looking for might have been renamed,
                        or is temporarily unavailable. Please search for the required
                        part number or explore our available inventory.
                    </p>

                    {{-- Part Number Search --}}
                    <div class="mb-4">
                        <form action="{{ url('/available-stock') }}" method="GET">
                            <div class="input-group">
                                <input
                                    type="text"
                                    name="search"
                                    class="form-control form-control-lg"
                                    placeholder="Search part number e.g. FFSP2065A"
                                    value="{{ request('search') }}"
                                    autofocus
                                >
                                <button class="theme-btn" type="submit">
                                    <i class="fa-solid fa-magnifying-glass"></i> Search
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="d-flex justify-content-center gap-3 flex-wrap mb-4">
                        <a href="{{ url('/') }}" class="theme-btn">
                            <i class="fa-solid fa-house"></i> Go Back Home
                        </a>

                        <a href="{{ url('/available-stock') }}" class="theme-btn">
                            <i class="fa-solid fa-box-open"></i> Browse Available Stock
                        </a>

                        <a href="{{ url('/get-a-quote') }}" class="theme-btn">
                            <i class="fa-solid fa-file-lines"></i> Request a Quote
                        </a>
                    </div>

                    {{-- Related Products --}}
                    @if(isset($relatedProducts) && $relatedProducts->count())
                        <div class="mt-5">
                            <h4 class="text-center mb-4">
                                You might be looking for one of these
                            </h4>

                            <div class="related-products-container">
                                <div class="related-products-grid">

                                    @foreach($relatedProducts as $product)
                                        <div>
                                            @php
                                                // Manufacturer segment is cosmetic.
                                                $mfrSlug = \Illuminate\Support\Str::slug(
                                                    $product->manufacturer ?: 'manufacturer'
                                                );

                                                // Mirror ProductController@show encoding.
                                                $partEncoded = str_replace(
                                                    ['/', '#'],
                                                    ['__', '--'],
                                                    $product->product_key
                                                );
                                            @endphp

                                            <a
                                                href="{{ url('/product/'.$mfrSlug.'/'.$partEncoded) }}"
                                                class="text-decoration-none text-dark"
                                            >
                                                <div class="card h-100 shadow-sm">

                                                    @if($product->image)
                                                        <img
                                                            src="{{ $product->image }}"
                                                            class="card-img-top p-2"
                                                            alt="{{ $product->product_key }}"
                                                            loading="lazy"
                                                        >
                                                    @else
                                                        <div class="card-img-placeholder d-flex align-items-center justify-content-center">
                                                            <i class="fa-solid fa-microchip fa-2x text-muted"></i>
                                                        </div>
                                                    @endif

                                                    <div class="card-body p-2 text-center">
                                                        <p class="product-name mb-1">
                                                            {{ $product->product_key }}
                                                        </p>

                                                        <p class="product-mfr mb-1">
                                                            {{ $product->manufacturer }}
                                                        </p>

                                                        <p class="product-cta mb-0">
                                                            Contact for Pricing
                                                        </p>
                                                    </div>

                                                </div>
                                            </a>
                                        </div>
                                    @endforeach

                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Contact Info --}}
                    <div class="mt-3 p-3 bg-light rounded">
                        <p class="mb-1">
                            <strong>Need assistance?</strong>
                        </p>

                        <p class="mb-0">
                            <i class="fa-solid fa-envelope me-1"></i>
                            Email us at
                            <a href="mailto:sales@simplytronix.com">
                                sales@simplytronix.com
                            </a>
                        </p>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('footer')
@endsection

<style>
    .related-products-container {
        display: flex;
        justify-content: center;
        padding: 1rem 0;
    }

    .related-products-grid {
        display: grid;
        grid-template-columns: repeat(5, minmax(150px, 1fr));
        gap: 1rem;
        width: 100%;
        max-width: 1200px;
    }

    @media (max-width: 1024px) {
        .related-products-grid {
            grid-template-columns: repeat(3, minmax(120px, 1fr));
        }
    }

    @media (max-width: 768px) {
        .related-products-grid {
            grid-template-columns: repeat(2, minmax(100px, 1fr));
        }
    }

    @media (max-width: 480px) {
        .related-products-grid {
            grid-template-columns: 1fr;
        }
    }

    .related-products-grid a {
        display: block;
        text-decoration: none;
        color: inherit;
    }

    .related-products-grid .card {
        display: flex;
        flex-direction: column;
        height: 100%;
        border: 1px solid #ddd;
        border-radius: 6px;
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .related-products-grid .card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.12) !important;
    }

    .related-products-grid .card-img-top,
    .related-products-grid .card-img-placeholder {
        height: 100px;
        width: 100%;
        object-fit: contain;
        flex-shrink: 0;
        padding: 0.5rem;
        background: #f9f9f9;
    }

    .related-products-grid .card-body {
        flex: 1;
        display: flex;
        flex-direction: column;
        padding: 0.75rem;
    }

    .related-products-grid .product-name {
        font-weight: 600;
        font-size: 1.2rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-bottom: 0.5rem;
    }

    .related-products-grid .product-mfr {
        font-size: 1.0rem;
        color: #999;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-bottom: 0.5rem;
    }

    .related-products-grid .product-cta {
        font-size: 1.0rem;
        color: #0066cc;
        margin-top: auto;
    }
</style>