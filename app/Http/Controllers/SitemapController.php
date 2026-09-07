<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\CachedProduct;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

class SitemapController extends Controller
{
    /**
     * Products per sitemap file
     */
    protected int $perFile = 2000;

    /**
     * Base query for products that are worthy of indexing.
     */
    protected function indexableProducts()
    {
        return CachedProduct::query()
            ->where('specs_synced', 1)
            ->whereNotNull('specs')
            ->where('specs', '<>', '');
    }

    /*
    |--------------------------------------------------------------------------
    | Sitemap Index
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $totalProducts = $this->indexableProducts()->count();

        $totalProductSitemaps = max(
            1,
            (int) ceil($totalProducts / $this->perFile)
        );

        $latestProductUpdate = $this->indexableProducts()->max('updated_at');

        $lastmod = $latestProductUpdate
            ? Carbon::parse($latestProductUpdate)->toDateString()
            : now()->toDateString();

        return response()->stream(function () use ($totalProductSitemaps, $lastmod) {

            echo '<?xml version="1.0" encoding="UTF-8"?>';
            echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

            // Static sitemap
            echo '<sitemap>';
            echo '<loc>' . url('/sitemap-static.xml') . '</loc>';
            echo '<lastmod>' . $lastmod . '</lastmod>';
            echo '</sitemap>';

            // Product sitemaps
            for ($i = 1; $i <= $totalProductSitemaps; $i++) {

                echo '<sitemap>';
                echo '<loc>' . url("/sitemap-products-{$i}.xml") . '</loc>';
                echo '<lastmod>' . $lastmod . '</lastmod>';
                echo '</sitemap>';

            }

            echo '</sitemapindex>';

        }, 200, [
            'Content-Type' => 'application/xml',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Static Sitemap
    |--------------------------------------------------------------------------
    */
    public function static()
    {
        return response()->stream(function () {

            echo '<?xml version="1.0" encoding="UTF-8"?>';
            echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

            // Homepage
            $this->printUrl(
                url('/'),
                now()->toAtomString(),
                'daily',
                '1.0'
            );

            // Static Pages
            $pages = [
                '/about-us',
                '/contact-us',
                '/industries-we-serve',
                '/available-stock',
                '/news-alerts',
            ];

            foreach ($pages as $page) {

                $this->printUrl(
                    url($page),
                    now()->toAtomString(),
                    'weekly',
                    '0.8'
                );

            }

            // Categories
            Category::select('slug', 'updated_at')
                ->orderBy('id')
                ->chunk(500, function ($categories) {

                    foreach ($categories as $category) {

                        $this->printUrl(
                            url('/category/' . $category->slug),
                            optional($category->updated_at)->toAtomString() ?? now()->subDays(7)->toAtomString(),
                            'weekly',
                            '0.8'
                        );

                    }

                });

            // Blog Posts
            Post::where('is_published', 1)
                ->select('slug', 'updated_at')
                ->orderBy('id')
                ->chunk(500, function ($posts) {

                    foreach ($posts as $post) {

                        $this->printUrl(
                            url('/blog/' . $post->slug),
                            optional($post->updated_at)->toAtomString() ?? now()->subDays(7)->toAtomString(),
                            'weekly',
                            '0.9'
                        );

                    }

                });

            echo '</urlset>';

        }, 200, [
            'Content-Type' => 'application/xml',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Product Sitemap
    |--------------------------------------------------------------------------
    */
    public function products($page)
    {
        if (!is_numeric($page) || $page < 1) {
            abort(404);
        }

        $products = $this->indexableProducts()
            ->select([
                'product_key',
                'manufacturer',
                'updated_at',
            ])
            ->orderBy('id')
            ->forPage((int)$page, $this->perFile)
            ->get();

        if ($products->isEmpty()) {
            abort(404);
        }

        return response()->stream(function () use ($products) {

            echo '<?xml version="1.0" encoding="UTF-8"?>';
            echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

            foreach ($products as $product) {

                $manufacturerSlug = Str::slug($product->manufacturer);

                $safePart = rawurlencode(
                    str_replace(
                        ['/', '#'],
                        ['__', '--'],
                        strtoupper(trim($product->product_key))
                    )
                );

                echo '<url>';

                echo '<loc>';
                echo htmlspecialchars(
                    url("product/{$manufacturerSlug}/{$safePart}"),
                    ENT_XML1
                );
                echo '</loc>';

                echo '<lastmod>';
                echo optional($product->updated_at)->toAtomString()
                    ?? now()->subDays(30)->toAtomString();
                echo '</lastmod>';

                echo '<changefreq>weekly</changefreq>';
                echo '<priority>0.7</priority>';

                echo '</url>';

            }

            echo '</urlset>';

        }, 200, [
            'Content-Type' => 'application/xml',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | XML Helper
    |--------------------------------------------------------------------------
    */
    protected function printUrl(
        string $loc,
        string $lastmod,
        string $changefreq,
        string $priority
    ): void {

        echo '<url>';
        echo '<loc>' . htmlspecialchars($loc, ENT_XML1) . '</loc>';
        echo '<lastmod>' . $lastmod . '</lastmod>';
        echo '<changefreq>' . $changefreq . '</changefreq>';
        echo '<priority>' . $priority . '</priority>';
        echo '</url>';

    }
}