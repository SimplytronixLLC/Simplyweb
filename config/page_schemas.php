<?php

/**
 * Defines every editable static page and its fields for the Pages Tool.
 *
 * Field types:
 *   text      - single-line input
 *   textarea  - plain multi-line input (no HTML formatting UI)
 *   richtext  - TinyMCE editor, value saved as HTML
 *   image     - text input for an image path/URL, with a small preview
 *   repeater  - a repeatable list of sub-fields (only text/textarea/image
 *               sub-fields are supported inside a repeater, to keep the
 *               "add row" cloning in the admin simple and reliable)
 *
 * To add a new editable page: add a slug here with a label + fields array,
 * then in the Blade view fetch it with Page::content('your-slug') and swap
 * hardcoded strings for $page->get('field_key', 'current default text').
 */

return [

    'terms' => [
        'label' => 'Terms and Conditions',
        'fields' => [
            ['key' => 'title', 'label' => 'Page Title', 'type' => 'text'],
            ['key' => 'body', 'label' => 'Body Content', 'type' => 'richtext'],
        ],
    ],

    'upload' => [
        'label' => 'BOM Upload',
        'fields' => [
            ['key' => 'meta_description', 'label' => 'SEO Meta Description', 'type' => 'textarea'],
            ['key' => 'hero_title', 'label' => 'Hero Title', 'type' => 'text'],
            ['key' => 'hero_subtitle', 'label' => 'Hero Subtitle', 'type' => 'textarea'],
            ['key' => 'form_heading', 'label' => 'Form Heading', 'type' => 'text'],
            ['key' => 'form_description', 'label' => 'Form Description', 'type' => 'textarea'],
        ],
    ],

    'about-us' => [
        'label' => 'About Us',
        'fields' => [
            ['key' => 'meta_description', 'label' => 'SEO Meta Description', 'type' => 'textarea'],
            ['key' => 'hero_title', 'label' => 'Hero Title', 'type' => 'text'],
            ['key' => 'hero_subtitle', 'label' => 'Hero Subtitle', 'type' => 'text'],

            ['key' => 'journey_heading', 'label' => 'Journey Heading', 'type' => 'text'],
            ['key' => 'journey_paragraph_1', 'label' => 'Journey Paragraph 1', 'type' => 'richtext'],
            ['key' => 'journey_paragraph_2', 'label' => 'Journey Paragraph 2', 'type' => 'richtext'],
            ['key' => 'journey_paragraph_3', 'label' => 'Journey Paragraph 3', 'type' => 'richtext'],
            ['key' => 'journey_image', 'label' => 'Journey Image URL', 'type' => 'image'],

            ['key' => 'why_choose_heading', 'label' => '"Why Choose Us" Heading', 'type' => 'text'],
            ['key' => 'why_choose_subheading', 'label' => '"Why Choose Us" Subheading', 'type' => 'text'],
            [
                'key' => 'why_choose_cards',
                'label' => 'Why Choose Us Cards',
                'type' => 'repeater',
                'fields' => [
                    ['key' => 'icon', 'label' => 'Icon Class (Font Awesome)', 'type' => 'text'],
                    ['key' => 'icon_color', 'label' => 'Icon Color Class (e.g. text-primary)', 'type' => 'text'],
                    ['key' => 'title', 'label' => 'Title', 'type' => 'text'],
                    ['key' => 'description', 'label' => 'Description', 'type' => 'textarea'],
                ],
            ],

            ['key' => 'testimonials_heading', 'label' => 'Testimonials Heading', 'type' => 'text'],
            [
                'key' => 'testimonials',
                'label' => 'Testimonials',
                'type' => 'repeater',
                'fields' => [
                    ['key' => 'photo', 'label' => 'Photo URL', 'type' => 'image'],
                    ['key' => 'name', 'label' => 'Name & Title', 'type' => 'text'],
                    ['key' => 'quote', 'label' => 'Quote', 'type' => 'textarea'],
                ],
            ],
        ],
    ],

    'contact-us' => [
        'label' => 'Contact Us',
        'fields' => [
            ['key' => 'hero_title', 'label' => 'Hero Title', 'type' => 'text'],
            ['key' => 'hero_subtitle', 'label' => 'Hero Subtitle', 'type' => 'text'],
            ['key' => 'form_heading', 'label' => 'Form Heading', 'type' => 'text'],
            ['key' => 'why_heading', 'label' => '"Why Simplytronix" Heading', 'type' => 'text'],
            ['key' => 'why_body', 'label' => '"Why Simplytronix" Body (one line per point)', 'type' => 'textarea'],
            [
                'key' => 'offices',
                'label' => 'Offices',
                'type' => 'repeater',
                'fields' => [
                    ['key' => 'region_label', 'label' => 'Region Label (e.g. USA Office)', 'type' => 'text'],
                    ['key' => 'company_line', 'label' => 'Company / Operator Line', 'type' => 'text'],
                    ['key' => 'address', 'label' => 'Address', 'type' => 'textarea'],
                    ['key' => 'phone', 'label' => 'Phone', 'type' => 'text'],
                    ['key' => 'email', 'label' => 'Email', 'type' => 'text'],
                    ['key' => 'map_embed_url', 'label' => 'Google Maps Embed URL', 'type' => 'textarea'],
                ],
            ],
        ],
    ],

    'quality-assurance' => [
        'label' => 'Quality Assurance',
        'fields' => [
            ['key' => 'meta_description', 'label' => 'SEO Meta Description', 'type' => 'textarea'],
            ['key' => 'hero_title', 'label' => 'Hero Title', 'type' => 'text'],
            ['key' => 'hero_subtitle', 'label' => 'Hero Subtitle', 'type' => 'text'],
            ['key' => 'intro_paragraph_1', 'label' => 'Intro Paragraph 1', 'type' => 'richtext'],
            ['key' => 'intro_paragraph_2', 'label' => 'Intro Paragraph 2', 'type' => 'richtext'],

            ['key' => 'standards_heading', 'label' => 'Standards Heading', 'type' => 'text'],
            [
                'key' => 'standards',
                'label' => 'Standards & Alignment List',
                'type' => 'repeater',
                'fields' => [
                    ['key' => 'name', 'label' => 'Standard Name', 'type' => 'text'],
                    ['key' => 'description', 'label' => 'Description', 'type' => 'text'],
                ],
            ],

            [
                'key' => 'inspection_cards',
                'label' => 'Inspection Method Cards',
                'type' => 'repeater',
                'fields' => [
                    ['key' => 'title', 'label' => 'Title', 'type' => 'text'],
                    ['key' => 'description', 'label' => 'Description', 'type' => 'textarea'],
                ],
            ],

            ['key' => 'sample_report_heading', 'label' => 'Sample Report Heading', 'type' => 'text'],
            ['key' => 'sample_report_text', 'label' => 'Sample Report Text', 'type' => 'textarea'],
            ['key' => 'sample_report_pdf', 'label' => 'Sample Report PDF Path', 'type' => 'text'],

            ['key' => 'why_trust_heading', 'label' => '"Why Customers Trust Us" Heading', 'type' => 'text'],
            [
                'key' => 'why_trust_items',
                'label' => 'Trust Points',
                'type' => 'repeater',
                'fields' => [
                    ['key' => 'text', 'label' => 'Point', 'type' => 'text'],
                ],
            ],
        ],
    ],

    'industries' => [
        'label' => 'Industries We Serve',
        'fields' => [
            ['key' => 'meta_description', 'label' => 'SEO Meta Description', 'type' => 'textarea'],
            ['key' => 'hero_title', 'label' => 'Hero Title', 'type' => 'text'],
            ['key' => 'hero_subtitle', 'label' => 'Hero Subtitle', 'type' => 'textarea'],
            ['key' => 'intro_html', 'label' => 'Intro Text', 'type' => 'richtext'],

            [
                'key' => 'industries',
                'label' => 'Industry Cards',
                'type' => 'repeater',
                'fields' => [
                    ['key' => 'tag', 'label' => 'Filter Tag', 'type' => 'text'],
                    ['key' => 'img', 'label' => 'Image', 'type' => 'image', 'preview_base' => 'public/assets/images/industries/'],
                    ['key' => 'alt', 'label' => 'Image Alt Text', 'type' => 'text'],
                    ['key' => 'title', 'label' => 'Title', 'type' => 'text'],
                    ['key' => 'desc', 'label' => 'Description', 'type' => 'textarea'],
                    ['key' => 'apps', 'label' => 'Applications (one per line)', 'type' => 'textarea'],
                    ['key' => 'note', 'label' => 'Modal Note (plain text, "Label: rest" ok)', 'type' => 'textarea'],
                ],
            ],

            ['key' => 'value_heading', 'label' => '"How We Add Value" Heading', 'type' => 'text'],
            [
                'key' => 'value_items',
                'label' => 'Value Strip Items',
                'type' => 'repeater',
                'fields' => [
                    ['key' => 'icon', 'label' => 'Icon Class (Tabler Icons)', 'type' => 'text'],
                    ['key' => 'label', 'label' => 'Label', 'type' => 'text'],
                ],
            ],

            ['key' => 'cta_heading', 'label' => 'CTA Heading', 'type' => 'text'],
            ['key' => 'cta_text', 'label' => 'CTA Text', 'type' => 'textarea'],
        ],
    ],

];
