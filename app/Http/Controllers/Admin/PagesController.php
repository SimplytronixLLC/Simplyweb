<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;

class PagesController extends Controller
{
    protected function schemas(): array
    {
        return config('page_schemas', []);
    }

    /**
     * List every editable page with a "Customized" / "Using defaults" badge.
     */
    public function index()
    {
        $schemas = $this->schemas();
        $pages = Page::whereIn('slug', array_keys($schemas))->get()->keyBy('slug');

        return view('admin.pages.index', compact('schemas', 'pages'));
    }

    /**
     * Show the dynamic edit form for one page, built from its schema.
     */
    public function edit(string $slug)
    {
        $schemas = $this->schemas();
        abort_unless(isset($schemas[$slug]), 404);

        $page = Page::content($slug);
        $fields = $schemas[$slug]['fields'];
        $label = $schemas[$slug]['label'] ?? $slug;

        return view('admin.pages.edit', compact('page', 'fields', 'slug', 'label'));
    }

    /**
     * Save submitted field values back onto the page's content JSON.
     */
    public function update(Request $request, string $slug)
    {
        $schemas = $this->schemas();
        abort_unless(isset($schemas[$slug]), 404);

        $content = $request->input('content', []);
        $content = $this->reindexRepeaters($schemas[$slug]['fields'], $content);

        $page = Page::firstOrNew(['slug' => $slug]);
        $page->title = $schemas[$slug]['label'] ?? $slug;
        $page->content = $content;
        $page->save();

        return redirect()
            ->route('admin.pages.edit', $slug)
            ->with('success', 'Page updated.');
    }

    /**
     * Handle an ad-hoc image upload triggered by an image field's "Browse"
     * button (top-level or inside a repeater row — both use this same
     * endpoint). Returns the public URL as JSON so the JS can drop it
     * straight into the field's text input.
     */
    public function uploadImage(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpg,jpeg,png,webp,gif,svg|max:5120',
        ]);

        $file = $request->file('file');
        $filename = time() . '_' . preg_replace('/[^A-Za-z0-9_\-\.]/', '', $file->getClientOriginalName());

        $destinationPath = public_path('uploads/pages');
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        $file->move($destinationPath, $filename);

        return response()->json([
            'success' => true,
            'path' => asset('uploads/pages/' . $filename),
        ]);
    }

    /**
     * Repeater rows arrive keyed by whatever index they had in the form
     * (which can have gaps after a row was removed client-side). Re-index
     * them to a clean 0..n array before saving.
     */
    protected function reindexRepeaters(array $fields, array $content): array
    {
        foreach ($fields as $field) {
            if (($field['type'] ?? null) === 'repeater' && isset($content[$field['key']]) && is_array($content[$field['key']])) {
                $content[$field['key']] = array_values($content[$field['key']]);
            }
        }

        return $content;
    }
}