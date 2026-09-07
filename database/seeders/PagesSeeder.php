<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

/**
 * Pre-fills the `pages` table with the content that's currently hardcoded
 * in each Blade file, so the admin editor opens showing exactly what's
 * live today instead of blank fields. Safe to re-run — it only fills in
 * pages that don't exist yet (updateOrInsert-free, uses firstOrCreate).
 */
class PagesSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedPage('terms', 'Terms and Conditions', [
            'title' => 'Terms and Conditions',
            'body' => '<p>This is a styled HTML version of your Terms and Conditions for best readability across all devices.</p>',
        ]);

        $this->seedPage('upload', 'BOM Upload', [
            'meta_description' => 'Upload your Bill of Materials (BOM) and receive consolidated pricing from SimplyTronix.',
            'hero_title' => 'Upload Your BOM',
            'hero_subtitle' => 'Submit your Bill of Materials and receive consolidated pricing fast.',
            'form_heading' => 'Got More Than One Part?',
            'form_description' => 'Upload your complete Bill of Materials and receive consolidated pricing & availability.',
        ]);

        $this->seedPage('about-us', 'About Us', [
            'meta_description' => 'Simplytronix is your trusted partner in authentic electronic components.',
            'hero_title' => 'About Simplytronix',
            'hero_subtitle' => 'Your Trusted Partner in Genuine Electronic Components',

            'journey_heading' => 'Our Journey',
            'journey_paragraph_1' => '<p>Simplytronix was founded in 2020 to support electronics hobbyists, engineers, and businesses in overcoming challenges related to obtaining genuine electronic components and enabling small-scale production.</p>',
            'journey_paragraph_2' => "<p>To fulfill this mission, Simplytronix ensures the authenticity of every component we offer. All parts supplied by us are tested at par with the manufacturer's standards, and test reports are available on request.</p>",
            'journey_paragraph_3' => '<p>Through years of dedicated effort, Simplytronix has grown into a global distributor with a vast selection of components. We serve over 2500 registered customers and handle 100+ orders daily. Customers trust us for fast delivery, quality assurance, and transparency.</p>',
            'journey_image' => 'https://images.unsplash.com/photo-1600880292203-757bb62b4baf?ixlib=rb-4.0.3&auto=format&fit=crop&w=1350&q=80',

            'why_choose_heading' => 'Why Choose Simplytronix?',
            'why_choose_subheading' => 'Delivering genuine components, global support, and unmatched quality assurance since 2020.',
            'why_choose_cards' => [
                ['icon' => 'fas fa-check-circle', 'icon_color' => 'text-primary', 'title' => 'Authentic Components', 'description' => 'Every part undergoes stringent testing aligned with OEM standards. Test reports available on request.'],
                ['icon' => 'fas fa-warehouse', 'icon_color' => 'text-success', 'title' => 'Global Inventory', 'description' => 'Vast stock in state-of-the-art warehouses enables us to fulfill over 100 orders daily.'],
                ['icon' => 'fas fa-globe', 'icon_color' => 'text-info', 'title' => 'Worldwide Shipping', 'description' => 'We serve 2500+ customers across 30+ countries with fast and reliable global delivery.'],
                ['icon' => 'fas fa-user-shield', 'icon_color' => 'text-warning', 'title' => 'Trusted by Professionals', 'description' => 'From engineers to sourcing managers, our clients trust our consistency and quality.'],
                ['icon' => 'fas fa-headset', 'icon_color' => 'text-danger', 'title' => 'Expert Support', 'description' => 'Get assistance from real humans who understand components and supply chain challenges.'],
                ['icon' => 'fas fa-dollar-sign', 'icon_color' => 'text-secondary', 'title' => 'Fair Pricing', 'description' => 'Our pricing is competitive without compromising authenticity or service quality.'],
            ],

            'testimonials_heading' => 'What Our Clients Say',
            'testimonials' => [
                ['photo' => 'https://randomuser.me/api/portraits/men/32.jpg', 'name' => 'John R., Product Engineer', 'quote' => 'Simplytronix consistently delivers genuine components quickly. We trust them with our critical supply chain needs.'],
                ['photo' => 'https://randomuser.me/api/portraits/women/44.jpg', 'name' => 'Maria S., Procurement Manager', 'quote' => 'Their support team is responsive and knowledgeable. We always receive exactly what we order, with test reports to back it up.'],
                ['photo' => 'https://randomuser.me/api/portraits/men/76.jpg', 'name' => 'L. Knobbs, Founder, IoT Startup', 'quote' => 'Finding rare components used to be a hassle. Simplytronix made our sourcing faster and stress-free.'],
            ],
        ]);

        $this->seedPage('contact-us', 'Contact Us', [
            'hero_title' => 'Contact Us',
            'hero_subtitle' => 'Global sourcing support with offices in USA & India',
            'form_heading' => 'Get in Touch',
            'why_heading' => 'Why Simplytronix?',
            'why_body' => "Global sourcing network\nRFQ response within 24 hours\nSupport for hard-to-find components",
            'offices' => [
                [
                    'region_label' => 'USA Office',
                    'company_line' => 'Simplytronix LLC',
                    'address' => "1007 N Orange St, 4th Floor Suite #1382\nWilmington, DE 19801\nUnited States",
                    'phone' => '+1 302-600-2554',
                    'email' => 'info@simplytronix.com',
                    'map_embed_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2916.3061246658103!2d-75.55989931562665!3d39.73709359821858!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c6fd66239de7f7%3A0xb89847a87ae22294!2s1007n%20Orange%20St%2C%20Wilmington%2C%20DE%2019801%2C%20USA!5e1!3m2!1sen!2sin!4v1777730965911!5m2!1sen!2sin',
                ],
                [
                    'region_label' => 'India Office',
                    'company_line' => '(Operated by Chipaxia Pvt Ltd)',
                    'address' => "3rd Floor, Westend Mall\nRajendra Nagar, Indore\nMadhya Pradesh 452012\nIndia",
                    'phone' => '+91 0731-4067829',
                    'email' => 'sales@simplytronix.com',
                    'map_embed_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3499.5687862957548!2d75.82313477508056!3d22.66355062942702!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3962fd7ee72478e5%3A0x36d9764a0b325341!2sWestend%20Indore!5e1!3m2!1sen!2sin!4v1777729654198!5m2!1sen!2sin',
                ],
            ],
        ]);

        $this->seedPage('quality-assurance', 'Quality Assurance', [
            'meta_description' => 'Simplytronix applies structured inspection procedures aligned with SAE AS6081, AS6171 and IDEA-STD-1010. Advanced testing is conducted by independent third-party laboratories to ensure authenticity and reliability.',
            'hero_title' => 'Quality Assurance',
            'hero_subtitle' => 'Authenticity Verification & Component Inspection',
            'intro_paragraph_1' => '<p>At <strong>Simplytronix</strong>, quality control is embedded into our sourcing workflow. Our inspection procedures are aligned with internationally recognized counterfeit mitigation and verification standards to protect your supply chain.</p>',
            'intro_paragraph_2' => '<p>Advanced analytical testing is conducted through <strong>independent third-party laboratories</strong> to ensure unbiased verification and component authenticity.</p>',

            'standards_heading' => 'Standards & Industry Alignment',
            'standards' => [
                ['name' => 'SAE AS6081', 'description' => 'Counterfeit Electronic Parts Mitigation'],
                ['name' => 'AS6171', 'description' => 'Counterfeit Detection Test Methods'],
                ['name' => 'IDEA-STD-1010', 'description' => 'Electronic Component Acceptability'],
                ['name' => 'ISO 9001:2015 Principles', 'description' => 'Quality Management Practices'],
                ['name' => 'JEDEC Standards', 'description' => 'Semiconductor Reliability & Packaging'],
                ['name' => 'J-STD-033', 'description' => 'Moisture Sensitivity Handling'],
            ],

            'inspection_cards' => [
                ['title' => 'External Visual Inspection', 'description' => '40x microscopy inspection to detect resurfacing, remarking, oxidation, and marking inconsistencies.'],
                ['title' => 'X-Ray Analysis', 'description' => 'Non-destructive internal inspection verifying die structure, bonding integrity, and internal construction.'],
                ['title' => 'Decapsulation & Die Verification', 'description' => 'Controlled decapsulation confirming manufacturer logo and semiconductor authenticity.'],
                ['title' => 'Solvent & Resurfacing Testing', 'description' => 'Heated solvent testing to detect secondary coatings or surface tampering.'],
                ['title' => 'Mechanical Verification', 'description' => 'Dimensional validation against manufacturer specifications.'],
                ['title' => 'Documentation Review', 'description' => 'Verification of labeling, packaging integrity, and traceability records.'],
            ],

            'sample_report_heading' => 'Sample Laboratory Report',
            'sample_report_text' => 'Check out our sample laboratory report demonstrating our inspection methodology.',
            'sample_report_pdf' => 'public/uploads/test-reports/copy_watermark.pdf',

            'why_trust_heading' => 'Why Customers Trust Simplytronix',
            'why_trust_items' => [
                ['text' => 'Independent third-party laboratory testing support'],
                ['text' => 'Structured counterfeit risk mitigation procedures'],
                ['text' => 'Full traceability and documentation assistance'],
                ['text' => 'Hard-to-find and EOL sourcing expertise'],
                ['text' => 'Responsive RFQ turnaround'],
            ],
        ]);

        $this->seedPage('industries', 'Industries We Serve', [
            'meta_description' => 'Simplytronix supports OEMs and EMS providers across industrial, automotive, medical, telecom, data center, power, aerospace, and test & measurement industries with reliable electronic component sourcing.',
            'hero_title' => 'Industries we serve',
            'hero_subtitle' => 'Supporting OEMs, EMS providers, and technology-driven businesses worldwide with reliable electronic component sourcing.',
            'intro_html' => "<p>At <strong>Simplytronix</strong>, our experience across diverse applications lets us understand industry-specific requirements, compliance needs, and supply chain challenges — especially during shortages and constrained markets.</p><p><em>We don't just supply components. We help keep production moving.</em></p>",

            'industries' => [
                ['tag' => 'Manufacturing', 'img' => 'industrial-automation.jfif', 'alt' => 'Industrial Automation', 'title' => 'Industrial & automation', 'desc' => 'Control systems, smart manufacturing, and automation equipment for factory floors worldwide.', 'apps' => "PLCs and industrial controllers\nSensors and actuators\nPower management modules\nEmbedded control systems", 'note' => '<strong>Why it matters:</strong> Reliability, long lifecycle components, and consistent supply are critical in industrial environments.'],
                ['tag' => 'Mobility', 'img' => 'automotive-electronics.jfif', 'alt' => 'Automotive Electronics', 'title' => 'Automotive & transportation', 'desc' => 'Components for modern vehicles, EV powertrains, and Tier 1 & 2 suppliers globally.', 'apps' => "Infotainment systems\nADAS and safety electronics\nPowertrain electronics\nBody control modules", 'note' => '<strong>Our focus:</strong> Quality-conscious sourcing while navigating long lead times and obsolescence challenges.'],
                ['tag' => 'Healthcare', 'img' => 'medical-electronics.jfif', 'alt' => 'Medical Electronics', 'title' => 'Medical & healthcare', 'desc' => 'Accuracy, reliability, and compliance-grade components for life-critical medical electronics.', 'apps' => "Diagnostic equipment\nPatient monitoring systems\nImaging devices\nLaboratory instruments", 'note' => '<strong>What we prioritize:</strong> Stable sourcing, traceability, and components suitable for regulated environments.'],
                ['tag' => 'Connectivity', 'img' => 'telecom-networking.jfif', 'alt' => 'Telecommunications', 'title' => 'Telecommunications & networking', 'desc' => 'High-speed, always-on infrastructure components for telecom networks and next-gen connectivity.', 'apps' => "Network switches and routers\nRF and wireless modules\nData transmission systems\nSignal integrity solutions", 'note' => '<strong>Key value:</strong> Dependable component availability for fast-evolving technologies.'],
                ['tag' => 'Computing', 'img' => 'data-center-computing.jfif', 'alt' => 'Data Centers', 'title' => 'Data centers & computing', 'desc' => 'Enterprise and hyperscale data center computing hardware at any volume, on any timeline.', 'apps' => "Servers and storage systems\nPower supplies and cooling\nHigh-speed memory and processors\nNetworking ASICs", 'note' => '<strong>Our advantage:</strong> Experience with memory, logic, and hard-to-find parts during demand spikes.'],
                ['tag' => 'Energy', 'img' => 'power-energy.jfif', 'alt' => 'Power and Energy', 'title' => 'Power & energy', 'desc' => 'From grid infrastructure to renewable energy systems — powering the energy transition worldwide.', 'apps' => "Power converters and inverters\nEnergy monitoring systems\nRenewable energy controllers\nSmart grid hardware", 'note' => '<strong>What matters most:</strong> Component durability, efficiency ratings, and supply continuity.'],
                ['tag' => 'Defense', 'img' => 'aerospace-electronics.jfif', 'alt' => 'Aerospace Electronics', 'title' => 'Aerospace & defense', 'desc' => 'Precision sourcing for non-classified aerospace and defense-adjacent manufacturing applications.', 'apps' => "Avionics support systems\nGround equipment electronics\nTest and measurement systems", 'note' => '<strong>Our approach:</strong> Precision sourcing with attention to quality documentation and traceability.'],
                ['tag' => 'Test & Measurement', 'img' => 'test-measurement.jfif', 'alt' => 'Test and Measurement', 'title' => 'Test & measurement', 'desc' => 'Specialized and low-volume components for R&D labs and production test environments worldwide.', 'apps' => "Oscilloscopes and analyzers\nCalibration equipment\nIndustrial test systems", 'note' => '<strong>Why customers choose us:</strong> Deep support for specialized and hard-to-source components.'],
            ],

            'value_heading' => 'How we add value across industries',
            'value_items' => [
                ['icon' => 'ti ti-alert-triangle', 'label' => 'Shortage support'],
                ['icon' => 'ti ti-search', 'label' => 'EOL & hard-to-find parts'],
                ['icon' => 'ti ti-adjustments-horizontal', 'label' => 'Low MOQ flexibility'],
                ['icon' => 'ti ti-clock', 'label' => 'Fast RFQ response'],
                ['icon' => 'ti ti-headset', 'label' => 'Dedicated sales support'],
            ],

            'cta_heading' => "Let's support your industry",
            'cta_text' => "If your industry isn't listed above, chances are we can still help. Our sourcing expertise extends across a broad range of applications and markets.",
        ]);
    }

    protected function seedPage(string $slug, string $title, array $content): void
    {
        Page::firstOrCreate(
            ['slug' => $slug],
            ['title' => $title, 'content' => $content, 'meta' => []]
        );
    }
}
