<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    private function extractBodyContent($html)
    {
        // Safety net: if content is HTML-entity-encoded (e.g. raw HTML pasted
        // into Summernote's WYSIWYG pane instead of its Code View), decode it
        if (stripos($html, '&lt;!doctype') !== false || stripos($html, '&lt;html') !== false) {
            $html = html_entity_decode($html, ENT_QUOTES | ENT_HTML5);
            $html = str_replace(['<p>', '</p>', '<br>', '<br/>', '<br />'], "\n", $html);
        }

        // Check if this is a full document
        if (stripos($html, '<!DOCTYPE') !== false || stripos($html, '<html') !== false) {
            
            // Extract content between <body> tags
            preg_match('/<body[^>]*>(.*?)<\/body>/is', $html, $matches);

            // Preserve <style> blocks from <head> - without this, class-based
            // CSS (stat-card, hero, chart-container etc.) has no styling at all
            preg_match_all('/<style[^>]*>.*?<\/style>/is', $html, $styleMatches);
            $styles = isset($styleMatches[0]) ? implode("\n", $styleMatches[0]) : '';

            // Preserve external <script src="..."> tags from <head> - without this,
            // libraries like Chart.js never load and inline scripts fail silently
            preg_match('/<head[^>]*>(.*?)<\/head>/is', $html, $headMatches);
            $headContent = isset($headMatches[1]) ? $headMatches[1] : '';
            preg_match_all('/<script[^>]+src=["\'][^"\']+["\'][^>]*><\/script>/i', $headContent, $scriptMatches);
            $scripts = isset($scriptMatches[0]) ? implode("\n", $scriptMatches[0]) : '';

            if (isset($matches[1])) {
                // Order MUST be: styles → external scripts → content with canvas elements
                // Styles must load BEFORE any inline script runs, otherwise chart
                // containers have no defined height when Chart.js measures them,
                // causing the canvas to grow indefinitely (responsive resize loop)
                return trim($styles . "\n" . $scripts . "\n" . $matches[1]);
            }
            
            // Fallback: just remove DOCTYPE and html/head tags
            $content = preg_replace('/<\?xml[^>]*\?>/i', '', $html);
            $content = preg_replace('/<!DOCTYPE[^>]*>/i', '', $content);
            $content = preg_replace('/<html[^>]*>/i', '', $content);
            $content = preg_replace('/<\/html>/i', '', $content);
            $content = preg_replace('/<head[^>]*>.*?<\/head>/is', '', $content);
            
            return trim($content);
        }
        
        return $html;
    }
    public function index()
    {
        $posts = Post::latest()->get();

        return view('admin.posts.index', compact('posts'));
    }

    public function create()
    {
        return view('admin.posts.create');
    }
    
    public function store(Request $request)
    {
        $content = trim(strip_tags($request->content));

        if (empty($content)) {
            return back()
                ->withInput()
                ->with('error', 'Content field is required');
        }

        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|mimes:jpg,jpeg,png,webp,jfif|max:2048'
        ]);

        try {
            $imagePath = null;

            if ($request->hasFile('featured_image')) {
                $file = $request->file('featured_image');
                $destination = public_path('uploads/blog');

                if (!File::exists($destination)) {
                    File::makeDirectory($destination, 0755, true);
                }

                $mime = $file->getMimeType();

                if (in_array($mime, ['image/jpeg', 'image/jpg'])) {
                    $filename = time() . '_' . uniqid() . '.jpg';
                    $fullPath = $destination . '/' . $filename;
                    
                    $image = @imagecreatefromjpeg($file->getPathname());
                    if (!$image) {
                        throw new \Exception('Invalid JPG image');
                    }

                    imagejpeg($image, $fullPath, 90);
                    imagedestroy($image);
                } else {
                    $extension = strtolower($file->getClientOriginalExtension());
                    $filename = time() . '_' . uniqid() . '.' . $extension;
                    $file->move($destination, $filename);
                }

                $imagePath = 'uploads/blog/' . $filename;
            }

            $slug = $request->slug
                ? Str::slug($request->slug)
                : Str::slug($request->title);

            $originalSlug = $slug;
            $count = 1;

            while (Post::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $count;
                $count++;
            }

            // Extract body content to remove document-level tags
            $cleanContent = $this->extractBodyContent($request->content);

            $post = Post::create([
                'title' => $request->title,
                'slug' => $slug,
                'excerpt' => $request->excerpt,
                'content' => $cleanContent,  // Use cleaned content
                'featured_image' => $imagePath,
                'is_published' => $request->is_published ?? 1
            ]);

            try {
                $this->sendToMake($post);
            } catch (\Exception $e) {
                Log::error('Make webhook failed: ' . $e->getMessage());
            }

            return redirect()
                ->route('admin.posts.index')
                ->with('success', 'Post Created');

        } catch (\Exception $e) {
            Log::error('Post save failed: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }


    public function edit($id)
    {
        $post = Post::findOrFail($id);

        return view('admin.posts.edit', compact('post'));
    }

    public function preview(Request $request)
    {
        $request->validate([
            'title'   => 'nullable|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string',
            'featured_image_data' => 'nullable|string',
        ]);

        // Run through the exact same extraction logic used on save,
        // so the preview matches what would actually be published.
        $cleanContent = $this->extractBodyContent($request->content);

        $post = new Post([
            'title'   => $request->title ?: '(Untitled Preview)',
            'excerpt' => $request->excerpt,
            'content' => $cleanContent,
        ]);

        // Featured image is never uploaded/saved for a preview - it's either
        // a base64 data URL (new file picked but not submitted yet) or the
        // existing post's relative image path (editing, no new file chosen).
        // Attached as a dynamic attribute purely for the view, not persisted.
        if ($request->filled('featured_image_data')) {
            $imageData = $request->featured_image_data;

            $post->preview_image = str_starts_with($imageData, 'data:')
                ? $imageData
                : asset($imageData);
        }

        // Not persisted - preview only
        return view('admin.posts.preview', compact('post'));
    }

    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        $content = trim(strip_tags($request->content));

        if (empty($content)) {
            return back()
                ->withInput()
                ->with('error', 'Content field is required');
        }

        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|mimes:jpg,jpeg,png,webp,jfif|max:2048'
        ]);

        try {
            $imagePath = $post->featured_image;

            if ($request->hasFile('featured_image')) {
                if ($post->featured_image && file_exists(public_path($post->featured_image))) {
                    unlink(public_path($post->featured_image));
                }

                $file = $request->file('featured_image');
                $destination = public_path('uploads/blog');

                if (!File::exists($destination)) {
                    File::makeDirectory($destination, 0755, true);
                }

                $mime = $file->getMimeType();

                if (in_array($mime, ['image/jpeg', 'image/jpg'])) {
                    $filename = time() . '_' . uniqid() . '.jpg';
                    $fullPath = $destination . '/' . $filename;
                    
                    $image = @imagecreatefromjpeg($file->getPathname());
                    if (!$image) {
                        throw new \Exception('Invalid JPG image');
                    }

                    imagejpeg($image, $fullPath, 90);
                    imagedestroy($image);
                } else {
                    $extension = strtolower($file->getClientOriginalExtension());
                    $filename = time() . '_' . uniqid() . '.' . $extension;
                    $file->move($destination, $filename);
                }

                $imagePath = 'uploads/blog/' . $filename;
            }

            $slug = $request->slug
                ? Str::slug($request->slug)
                : Str::slug($request->title);

            $originalSlug = $slug;
            $count = 1;

            while (
                Post::where('slug', $slug)
                    ->where('id', '!=', $post->id)
                    ->exists()
            ) {
                $slug = $originalSlug . '-' . $count;
                $count++;
            }

            // Extract body content to remove document-level tags
            $cleanContent = $this->extractBodyContent($request->content);

            $post->update([
                'title' => $request->title,
                'slug' => $slug,
                'excerpt' => $request->excerpt,
                'content' => $cleanContent,  // Use cleaned content
                'featured_image' => $imagePath,
                'is_published' => $request->is_published ?? 1
            ]);

            try {
                $this->sendToMake($post);
            } catch (\Exception $e) {
                Log::error('Make webhook failed: ' . $e->getMessage());
            }

            return redirect()
                ->route('admin.posts.index')
                ->with('success', 'Post Updated');

        } catch (\Exception $e) {
            Log::error('Post update failed: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    private function sendToMake($post)
    {
        if (!$post->is_published) return;

        $imageUrl = null;

        if ($post->featured_image) {
            $imageUrl = url('public/' . ltrim($post->featured_image, '/'));
        }

        Http::post('https://hook.eu1.make.com/mcg7t1m7275cwq3lnmwmjobatudaa1ul', [
            'title'   => $post->title,
            'excerpt' => Str::limit(strip_tags($post->excerpt), 180),
            'link'    => url('/blog/' . $post->slug),
            'image'   => $imageUrl
        ]);
    }
}