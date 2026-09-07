<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use DB;
use App\Models\CachedProduct;
use App\Models\Category;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        \Illuminate\Support\Facades\View::composer('errors.404', function ($view) {
            $path = trim(request()->path(), '/');
            $segments = explode('/', $path);

            $manufacturerRaw = null;
            $partRaw = null;

            if (($segments[0] ?? null) === 'product' && count($segments) >= 3) {
                $manufacturerRaw = $segments[1];
                $partRaw = implode('/', array_slice($segments, 2));
            } elseif (($segments[0] ?? null) === 'product' && count($segments) === 2) {
                $manufacturerRaw = $segments[1];
            } else {
                $partRaw = end($segments) ?: null;
            }

            $relatedProducts = collect();

            if ($partRaw) {
                // Mirror ProductController@show's decode exactly, so the
                // extracted part matches how product_key is actually stored.
                $part = rawurldecode($partRaw);
                $part = str_replace(['__', '--'], ['/', '#'], $part);
                $part = strtoupper(trim($part));

                $alnum  = preg_replace('/[^A-Z0-9]/', '', $part);
                $prefix = substr($alnum, 0, 6);

                if (strlen($prefix) >= 4) {
                    // Prefix match uses the idx_product_key BTREE index - fast.
                    $query = \App\Models\CachedProduct::query()
                        ->where('product_key', 'LIKE', $prefix . '%');

                    if ($manufacturerRaw) {
                        $manufacturerGuess = str_replace('-', ' ', $manufacturerRaw);
                        $withMfr = (clone $query)
                            ->where('manufacturer', 'LIKE', '%' . $manufacturerGuess . '%');
                        if ($withMfr->count() > 0) {
                            $query = $withMfr;
                        }
                    }

                    $relatedProducts = $query->limit(10)->get();

                    // Fallback only if the fast prefix match came up short -
                    // this is an unindexed contains-scan, so keep it bounded.
                    if ($relatedProducts->count() < 10 && strlen($part) >= 3) {
                        $more = \App\Models\CachedProduct::query()
                            ->where('product_key', 'LIKE', '%' . $part . '%')
                            ->whereNotIn('id', $relatedProducts->pluck('id'))
                            ->limit(10 - $relatedProducts->count())
                            ->get();
                        $relatedProducts = $relatedProducts->merge($more);
                    }
                }
            }

            $view->with('relatedProducts', $relatedProducts);
        });

        // Share $settings and $categories with every view
        View::composer('*', function ($view) {
            try {
                $view->with('settings',
                    DB::table('settings')->where('id', 1)->first()
                );

                $categories = Category::whereNull('parent_id')
                    ->with('children.children')
                    ->orderBy('name')
                    ->limit(10)
                    ->get();

                $view->with('categories', $categories);

            } catch (\Exception $e) {
                $view->with('settings', null);
                $view->with('categories', collect());
            }
        });

        // Share $categoryCounts only with the layout file
        View::composer('includes.front', function ($view) {
            try {
                $categoryCounts = CachedProduct::selectRaw('category_id, count(*) as total')
                    ->groupBy('category_id')
                    ->pluck('total', 'category_id');

                $view->with('categoryCounts', $categoryCounts);

            } catch (\Exception $e) {
                $view->with('categoryCounts', collect());
            }
        });
    }
}