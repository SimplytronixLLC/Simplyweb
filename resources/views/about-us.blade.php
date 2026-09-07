@extends('includes.front')

@section('seo')
<title>About Us - {{ $settings->meta_title ?? 'Simplytronix' }}</title>
<meta name="description" content="{{ $settings->meta_description ?? 'Simplytronix is your trusted partner in authentic electronic components.' }}">
<meta name="keywords" content="{{ $settings->meta_keyword ?? 'electronic components, Simplytronix' }}">
@stop

@section('content')
<main class="main__content_wrapper">

<style>
    /* Light-touch refinements only — inherits the site's existing Bootstrap
       theme (colors, container widths, card styles). No new color system. */
    .sx-stats { border-top: 1px solid rgba(0,0,0,.06); border-bottom: 1px solid rgba(0,0,0,.06); }
    .sx-stats__num { font-size: clamp(1.6rem, 3vw, 2.1rem); font-weight: 700; line-height: 1; }
    .sx-stats__label { font-size: .9rem; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; color: #495057; margin-top: .5rem; }
    .sx-qa-card { height: 100%; }
    .sx-qa-card .fa-2x { width: 1.6em; }
    .sx-standards-box { max-width: 720px; margin: 2rem auto 0; padding: 2rem; background: #fff; border-radius: 8px; }
    .sx-standards-box p { font-size: 1.05rem; margin-bottom: 1rem; }
    .sx-standard-pill {
        display: inline-block; padding: .6rem 1.25rem; margin: .3rem;
        border: 1.5px solid var(--bs-primary, #0d6efd); border-radius: 50px;
        font-size: 1rem; font-weight: 600; color: var(--bs-primary, #0d6efd);
    }
    .sx-industry-chip { display: inline-block; padding: .4rem 1rem; margin: .25rem; background: #f1f3f5; border-radius: 4px; font-size: .9rem; color: #212529; }
    .sx-serve-card { height: 100%; border-top: 3px solid var(--bs-primary, #0d6efd); }
    .sx-serve-card .sx-serve-icon { width: 42px; height: 42px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem; }
</style>

    <!-- Hero Section — matches the breadcrumb pattern used sitewide -->
    <section class="breadcrumb__section breadcrumb__bg" style="position: relative;">
        <div style="position: absolute; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.5); z-index:1;"></div>
        <div class="container" style="position: relative; z-index: 2;">
            <div class="row row-cols-1">
                <div class="col text-center">
                    <div class="breadcrumb__content text-white py-2">
                        <h1 class="breadcrumb__content--title mb-3" style="color: #fff;">About Simplytronix</h1>
                        <p class="lead" style="color: #fff; font-size: 1.05rem; text-shadow: 0 1px 3px rgba(0,0,0,.4);">Your Trusted Partner in Genuine Electronic Components</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats strip -->
    <section class="sx-stats bg-white py-4">
        <div class="container">
            <div class="row text-center g-3">
                <div class="col-6 col-md-3">
                    <div class="sx-stats__num text-primary">2020</div>
                    <div class="sx-stats__label">Founded</div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="sx-stats__num text-primary">2,500+</div>
                    <div class="sx-stats__label">Registered Customers</div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="sx-stats__num text-primary">100+</div>
                    <div class="sx-stats__label">Daily Orders</div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="sx-stats__num text-primary">30+</div>
                    <div class="sx-stats__label">Countries Served</div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Company Section -->
    <section class="about__section section--padding">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4">
                    <h2 class="section__title mb-3">Our Journey</h2>
                    <p>
                        Simplytronix was founded in 2020 to support electronics hobbyists, engineers, and businesses in overcoming challenges related to obtaining genuine electronic components and enabling small-scale production.
                    </p>
                    <p>
                        To fulfill this mission, Simplytronix ensures the authenticity of every component we offer. All parts supplied by us are tested at par with the manufacturer's standards, and test reports are available on request.
                    </p>
                    <p>
                        Through years of dedicated effort, Simplytronix has grown into a global distributor with a vast selection of components. We serve over 2,500 registered customers and handle 100+ orders daily. Customers trust us for fast delivery, quality assurance, and transparency.
                    </p>
                </div>
                <div class="col-lg-6">
                    <img src="https://images.unsplash.com/photo-1600880292203-757bb62b4baf?ixlib=rb-4.0.3&auto=format&fit=crop&w=1350&q=80" alt="Simplytronix Team" class="img-fluid rounded shadow">
                    <!-- TODO: swap for a real Simplytronix warehouse/team photo when available -->
                </div>
            </div>
        </div>
    </section>

    <!-- Quality Assurance highlights, pulled from the real QA process -->
    <section class="section--padding bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section__title">How We Verify Every Component</h2>
                <p class="lead">Structured inspection aligned with recognized counterfeit-mitigation and verification standards.</p>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-6 col-lg-4">
                    <div class="card sx-qa-card shadow-sm border-0 text-center p-4">
                        <div class="mb-3"><i class="fas fa-search fa-2x text-primary"></i></div>
                        <h5>External Visual Inspection</h5>
                        <p>40x microscopy inspection to detect resurfacing, remarking, oxidation, and marking inconsistencies.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card sx-qa-card shadow-sm border-0 text-center p-4">
                        <div class="mb-3"><i class="fas fa-x-ray fa-2x text-success"></i></div>
                        <h5>X-Ray Analysis</h5>
                        <p>Non-destructive internal inspection verifying die structure, bonding integrity, and internal construction.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card sx-qa-card shadow-sm border-0 text-center p-4">
                        <div class="mb-3"><i class="fas fa-microchip fa-2x text-info"></i></div>
                        <h5>Decapsulation &amp; Die Verification</h5>
                        <p>Controlled decapsulation confirming manufacturer logo and semiconductor authenticity.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card sx-qa-card shadow-sm border-0 text-center p-4">
                        <div class="mb-3"><i class="fas fa-flask fa-2x text-warning"></i></div>
                        <h5>Solvent &amp; Resurfacing Testing</h5>
                        <p>Heated solvent testing to detect secondary coatings or surface tampering.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card sx-qa-card shadow-sm border-0 text-center p-4">
                        <div class="mb-3"><i class="fas fa-ruler-combined fa-2x text-danger"></i></div>
                        <h5>Mechanical Verification</h5>
                        <p>Dimensional validation against manufacturer specifications.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card sx-qa-card shadow-sm border-0 text-center p-4">
                        <div class="mb-3"><i class="fas fa-file-alt fa-2x text-secondary"></i></div>
                        <h5>Documentation Review</h5>
                        <p>Verification of labeling, packaging integrity, and traceability records.</p>
                    </div>
                </div>
            </div>

            <div class="sx-standards-box text-center shadow-sm">
                <p><strong>Aligned with:</strong></p>
                <span class="sx-standard-pill">SAE AS6081</span>
                <span class="sx-standard-pill">AS6171</span>
                <span class="sx-standard-pill">IDEA-STD-1010</span>
                <span class="sx-standard-pill">ISO 9001:2015</span>
                <span class="sx-standard-pill">JEDEC</span>
                <span class="sx-standard-pill">J-STD-033</span>
                <div class="mt-4">
                    <a href="/quality-assurance" class="btn btn-primary">View Full Quality Assurance Process</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Us -->
    <section class="section--padding">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section__title">Why Choose Simplytronix?</h2>
                <p class="lead">Delivering genuine components, global support, and unmatched quality assurance since 2020.</p>
            </div>

            <div class="row g-4">
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-0 text-center p-4">
                        <div class="mb-3"><i class="fas fa-check-circle fa-2x text-primary"></i></div>
                        <h5>Authentic Components</h5>
                        <p>Every part undergoes stringent testing aligned with OEM standards. Test reports available on request.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-0 text-center p-4">
                        <div class="mb-3"><i class="fas fa-warehouse fa-2x text-success"></i></div>
                        <h5>Global Inventory</h5>
                        <p>Vast stock in state-of-the-art warehouses enables us to fulfill over 100 orders daily.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-0 text-center p-4">
                        <div class="mb-3"><i class="fas fa-globe fa-2x text-info"></i></div>
                        <h5>Worldwide Shipping</h5>
                        <p>We serve 2,500+ customers across 30+ countries with fast and reliable global delivery.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-0 text-center p-4">
                        <div class="mb-3"><i class="fas fa-industry fa-2x text-warning"></i></div>
                        <h5>Every Sector, Every Application</h5>
                        <p>From industrial automation to aerospace and medical — <a href="/industries-we-serve">see all industries we serve</a>.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-0 text-center p-4">
                        <div class="mb-3"><i class="fas fa-headset fa-2x text-danger"></i></div>
                        <h5>Expert Support</h5>
                        <p>Get assistance from real humans who understand components and supply chain challenges.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-0 text-center p-4">
                        <div class="mb-3"><i class="fas fa-dollar-sign fa-2x text-secondary"></i></div>
                        <h5>Fair Pricing</h5>
                        <p>Our pricing is competitive without compromising authenticity or service quality.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Who We Serve — replaces the old fabricated testimonials -->
    <section class="section--padding bg-light">
        <div class="container">
            <h2 class="section__title text-center mb-5">Who We Serve</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card sx-serve-card shadow-sm border-0 p-4">
                        <div class="sx-serve-icon" style="background:#e7f1ff;">
                            <i class="fas fa-microchip text-primary"></i>
                        </div>
                        <h5>Product Engineers</h5>
                        <p class="text-muted mb-0">Genuine components delivered quickly, trusted for critical supply chain needs.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card sx-serve-card shadow-sm border-0 p-4">
                        <div class="sx-serve-icon" style="background:#e6f7ee;">
                            <i class="fas fa-clipboard-check text-success"></i>
                        </div>
                        <h5>Procurement &amp; Sourcing Teams</h5>
                        <p class="text-muted mb-0">Responsive, documented sourcing — what you order is what arrives, with paperwork to prove it.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card sx-serve-card shadow-sm border-0 p-4">
                        <div class="sx-serve-icon" style="background:#fff4e5;">
                            <i class="fas fa-rocket text-warning"></i>
                        </div>
                        <h5>Hardware Startups</h5>
                        <p class="text-muted mb-0">Fast turnaround on rare and hard-to-find parts, so sourcing never stalls your build.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="section--padding">
        <div class="container">
            <div class="bg-primary text-white rounded p-5 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <h3 class="mb-1 text-white">Ready to source with confidence?</h3>
                    <p class="mb-0">Upload a BOM or request a quote — our team responds fast.</p>
                </div>
                <div>
                    <a href="/get-a-quote" class="btn btn-light me-2">Request a Quote</a>
                    <a href="/contact-us" class="btn btn-outline-light">Contact Us</a>
                </div>
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