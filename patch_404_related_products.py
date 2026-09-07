#!/usr/bin/env python3
"""
Patch: Show 10 related products (2x5 grid) on 404 page, matched by part number
extracted from the dead URL. Uses App\\Models\\CachedProduct (the model
ProductController@show actually queries) and an indexed prefix LIKE for speed
at 254K+ rows.

Run from Laravel root: python3 patch_404_related_products.py
"""
import re
import shutil
import datetime

def backup(path):
    stamp = datetime.datetime.now().strftime("%Y%m%d%H%M%S")
    bak = f"{path}.bak-{stamp}"
    shutil.copy(path, bak)
    print(f"Backed up {path} -> {bak}")
    return bak

# ---------------------------------------------------------------------------
# 1. AppServiceProvider.php - register view composer for errors.404
# ---------------------------------------------------------------------------
provider_path = "app/Providers/AppServiceProvider.php"

with open(provider_path, "r") as f:
    content = f.read()

if "errors.404" in content:
    print("AppServiceProvider already has an errors.404 composer, skipping insert.")
else:
    backup(provider_path)

    composer_block = '''
        \\Illuminate\\Support\\Facades\\View::composer('errors.404', function ($view) {
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
                    $query = \\App\\Models\\CachedProduct::query()
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
                        $more = \\App\\Models\\CachedProduct::query()
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
'''

    pattern = re.compile(r'(public function boot\s*\([^)]*\)\s*(?::\s*void)?\s*\{)')
    match = pattern.search(content)

    if not match:
        print("ERROR: Could not find 'public function boot()' in AppServiceProvider.php.")
        print("No changes made to this file - insert the composer block manually:")
        print(composer_block)
    else:
        new_content = content[:match.end()] + composer_block + content[match.end():]
        with open(provider_path, "w") as f:
            f.write(new_content)
        print(f"Patched {provider_path}: registered errors.404 view composer.")

# ---------------------------------------------------------------------------
# 2. errors/404.blade.php - add 2x5 related products grid with fixed alignment
# ---------------------------------------------------------------------------
view_path = "resources/views/errors/404.blade.php"

with open(view_path, "r") as f:
    view_content = f.read()

if "related-products-grid" in view_content:
    print("404.blade.php already has the related products grid, skipping insert.")
else:
    backup(view_path)

    grid_block = '''
                    {{-- Related Products (matched from dead URL's part number) --}}
                    @if(isset($relatedProducts) && $relatedProducts->count())
                    <style>
                        .related-products-grid .col {
                            display: flex;
                        }
                        .related-products-grid .card {
                            display: flex;
                            flex-direction: column;
                            width: 100%;
                        }
                        .related-products-grid .card-img-top,
                        .related-products-grid .card-img-placeholder {
                            height: 120px;
                            flex-shrink: 0;
                        }
                        .related-products-grid .card-body {
                            flex: 1;
                            display: flex;
                            flex-direction: column;
                            justify-content: space-between;
                        }
                        .related-products-grid .card-body p {
                            margin-bottom: 0.5rem;
                        }
                        .related-products-grid .card-body p:last-child {
                            margin-bottom: 0;
                        }
                    </style>
                    <div class="mt-5 text-start">
                        <h4 class="text-center mb-4">You might be looking for one of these</h4>
                        <div class="row row-cols-2 row-cols-md-5 g-3 related-products-grid">
                            @foreach($relatedProducts as $product)
                            <div class="col">
                                @php
                                    // Manufacturer segment is not validated by the route,
                                    // it's purely cosmetic - a slug is safe here.
                                    $mfrSlug = \\Illuminate\\Support\\Str::slug($product->manufacturer ?: 'manufacturer');
                                    // Mirror ProductController@show's encode direction exactly
                                    // (reverse of its __ -> / and -- -> # decode).
                                    $partEncoded = str_replace(['/', '#'], ['__', '--'], $product->product_key);
                                @endphp
                                <a href="{{ url('/product/'.$mfrSlug.'/'.$partEncoded) }}"
                                   class="text-decoration-none text-dark h-100">
                                    <div class="card h-100 shadow-sm">
                                        @if($product->image)
                                        <img src="{{ $product->image }}" class="card-img-top p-2" alt="{{ $product->product_key }}" loading="lazy" style="object-fit:contain;">
                                        @else
                                        <div class="card-img-placeholder d-flex align-items-center justify-content-center bg-light">
                                            <i class="fa-solid fa-microchip fa-2x text-muted"></i>
                                        </div>
                                        @endif
                                        <div class="card-body p-2 text-center d-flex flex-column">
                                            <p class="mb-1 small fw-bold" style="word-break:break-word;min-height:2.4em;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">{{ $product->product_key }}</p>
                                            <p class="mb-0 small text-muted" style="word-break:break-word;min-height:1.2em;display:-webkit-box;-webkit-line-clamp:1;-webkit-box-orient:vertical;overflow:hidden;">{{ $product->manufacturer }}</p>
                                            <p class="mb-0 small text-primary mt-auto">Contact for Pricing</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
'''

    anchor = "                    {{-- Contact Info --}}"
    if anchor not in view_content:
        print("ERROR: Could not find the '{{-- Contact Info --}}' anchor in 404.blade.php.")
        print("No changes made to this file - insert the grid block manually before Contact Info:")
        print(grid_block)
    else:
        new_view_content = view_content.replace(anchor, grid_block + "\n" + anchor)
        with open(view_path, "w") as f:
            f.write(new_view_content)
        print(f"Patched {view_path}: added 2x5 related products grid with aligned layout.")

print("\nDone. Clear config/view cache and test:")
print("  php artisan view:clear && php artisan config:clear")
print("  Then visit a URL that will genuinely 404, e.g.:")
print("  https://simplytronix.com/product/infineon-technologies/CY62136EV30LL-BOGUS999")