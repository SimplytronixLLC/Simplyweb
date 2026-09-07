<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Generic, schema-driven static page content.
 *
 * Each row is one editable page (matched to a slug defined in
 * config/page_schemas.php). `content` holds every field's current value,
 * keyed by the field's `key` from the schema. Repeater fields are stored
 * as plain arrays of associative arrays.
 *
 * Usage in a Blade view:
 *
 *   @php $page = \App\Models\Page::content('about-us'); @endphp
 *   {{ $page->get('hero_title', 'About Simplytronix') }}
 *   @foreach($page->get('testimonials', []) as $t) ... @endforeach
 *
 * If no row exists yet for a slug (nothing has been edited in the admin),
 * get() simply falls back to the default you pass in — so pages keep
 * rendering exactly as before until someone edits them.
 */
class Page extends Model
{
    protected $fillable = ['slug', 'title', 'content', 'meta'];

    protected $casts = [
        'content' => 'array',
        'meta' => 'array',
    ];

    /**
     * Fetch (or build an empty, unsaved) page for a given slug.
     */
    public static function content(string $slug): self
    {
        return static::firstOrNew(
            ['slug' => $slug],
            ['content' => [], 'meta' => []]
        );
    }

    /**
     * Read a field's value, falling back to $default when the page hasn't
     * been customized yet or the key doesn't exist. Supports dot notation
     * for nested repeater fields if ever needed (e.g. 'offices.0.phone').
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return data_get($this->content ?? [], $key, $default);
    }
}
