@extends('includes.front')

@php
    $page = \App\Models\Page::content('about-us');

    $whyChooseCards = $page->get('why_choose_cards', [
        ['icon' => 'fas fa-check-circle', 'icon_color' => 'text-primary', 'title' => 'Authentic Components', 'description' => 'Every part undergoes stringent testing aligned with OEM standards. Test reports available on request.'],
        ['icon' => 'fas fa-warehouse', 'icon_color' => 'text-success', 'title' => 'Global Inventory', 'description' => 'Vast stock in state-of-the-art warehouses enables us to fulfill over 100 orders daily.'],
        ['icon' => 'fas fa-globe', 'icon_color' => 'text-info', 'title' => 'Worldwide Shipping', 'description' => 'We serve 2500+ customers across 30+ countries with fast and reliable global delivery.'],
        ['icon' => 'fas fa-user-shield', 'icon_color' => 'text-warning', 'title' => 'Trusted by Professionals', 'description' => 'From engineers to sourcing managers, our clients trust our consistency and quality.'],
        ['icon' => 'fas fa-headset', 'icon_color' => 'text-danger', 'title' => 'Expert Support', 'description' => 'Get assistance from real humans who understand components and supply chain challenges.'],
        ['icon' => 'fas fa-dollar-sign', 'icon_color' => 'text-secondary', 'title' => 'Fair Pricing', 'description' => 'Our pricing is competitive without compromising authenticity or service quality.'],
    ]);

    $testimonials = $page->get('testimonials', [
        ['photo' => 'https://randomuser.me/api/portraits/men/32.jpg', 'name' => 'John R., Product Engineer', 'quote' => 'Simplytronix consistently delivers genuine components quickly. We trust them with our critical supply chain needs.'],
        ['photo' => 'https://randomuser.me/api/portraits/women/44.jpg', 'name' => 'Maria S., Procurement Manager', 'quote' => 'Their support team is responsive and knowledgeable. We always receive exactly what we order, with test reports to back it up.'],
        ['photo' => 'https://randomuser.me/api/portraits/men/76.jpg', 'name' => 'L. Knobbs, Founder, IoT Startup', 'quote' => 'Finding rare components used to be a hassle. Simplytronix made our sourcing faster and stress-free.'],
    ]);
@endphp

@section('seo')
<title>About Us - {{ $settings->meta_title ?? 'Simplytronix' }}</title>
<meta name="description" content="{{ $page->get('meta_description', $settings->meta_description ?? 'Simplytronix is your trusted partner in authentic electronic components.') }}">
<meta name="keywords" content="{{ $settings->meta_keyword ?? 'electronic components, Simplytronix' }}">
@stop

@section('content')
<main class="main__content_wrapper">

    <!-- Hero Section with Overlay -->
    <section class="breadcrumb__section breadcrumb__bg" style="position: relative;">
        <div style="position: absolute; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.5); z-index:1;"></div>
        <div class="container" style="position: relative; z-index: 2;">
            <div class="row row-cols-1">
                <div class="col text-center">
                    <div class="breadcrumb__content text-white py-2">
                        <h1 class="breadcrumb__content--title mb-3" style="color: #fff;">{{ $page->get('hero_title', 'About Simplytronix') }}</h1>
                        <p class="lead" style="color: #fff;">{{ $page->get('hero_subtitle', 'Your Trusted Partner in Genuine Electronic Components') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Company Section -->
    <section class="about__section section--padding">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4">
                    <h2 class="section__title mb-3">{{ $page->get('journey_heading', 'Our Journey') }}</h2>
                    {!! $page->get('journey_paragraph_1', '<p>Simplytronix was founded in 2020 to support electronics hobbyists, engineers, and businesses in overcoming challenges related to obtaining genuine electronic components and enabling small-scale production.</p>') !!}
                    {!! $page->get('journey_paragraph_2', "<p>To fulfill this mission, Simplytronix ensures the authenticity of every component we offer. All parts supplied by us are tested at par with the manufacturer's standards, and test reports are available on request.</p>") !!}
                    {!! $page->get('journey_paragraph_3', '<p>Through years of dedicated effort, Simplytronix has grown into a global distributor with a vast selection of components. We serve over 2500 registered customers and handle 100+ orders daily. Customers trust us for fast delivery, quality assurance, and transparency.</p>') !!}
                </div>
                <div class="col-lg-6">
                    <img src="{{ $page->get('journey_image', 'https://images.unsplash.com/photo-1600880292203-757bb62b4baf?ixlib=rb-4.0.3&auto=format&fit=crop&w=1350&q=80') }}" alt="Simplytronix Team" class="img-fluid rounded shadow">
                </div>
            </div>
        </div>
    </section>

    <section class="section--padding bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section__title">{{ $page->get('why_choose_heading', 'Why Choose Simplytronix?') }}</h2>
            <p class="lead">{{ $page->get('why_choose_subheading', 'Delivering genuine components, global support, and unmatched quality assurance since 2020.') }}</p>
        </div>

        <div class="row g-4">
            @foreach($whyChooseCards as $card)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm border-0 text-center p-4">
                    <div class="mb-3">
                        <i class="{{ $card['icon'] ?? 'fas fa-check-circle' }} fa-2x {{ $card['icon_color'] ?? 'text-primary' }}"></i>
                    </div>
                    <h5>{{ $card['title'] ?? '' }}</h5>
                    <p>{{ $card['description'] ?? '' }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

    <!-- Testimonials Section -->
    <section class="testimonial__section section--padding bg-light">
        <div class="container">
            <h2 class="section__title text-center mb-5">{{ $page->get('testimonials_heading', 'What Our Clients Say') }}</h2>
            <div class="row text-center">
                @foreach($testimonials as $t)
                <div class="col-md-4 mb-4">
                    <img src="{{ $t['photo'] ?? '' }}" class="rounded-circle mb-3" width="80" height="80" alt="Client Photo">
                    <p class="fw-bold">{{ $t['name'] ?? '' }}</p>
                    <p class="text-muted">"{{ $t['quote'] ?? '' }}"</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

</main>

<!-- SCHEMA: ABOUT PAGE -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "AboutPage",
  "name": "About Simplytronix",
  "description": "Simplytronix is a global distributor of genuine electronic components, supporting OEMs, engineers, and businesses with authentic sourcing and quality assurance.",
  "mainEntity": {
    "@type": "Organization",
    "name": "Simplytronix",
    "foundingDate": "2020",
    "url": "https://www.simplytronix.com",
    "sameAs": [
      "https://www.linkedin.com/company/simplytronix-llc/"
    ]
  }
}
</script>

@stop
