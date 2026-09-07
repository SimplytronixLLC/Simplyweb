<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CachedProduct;
use Illuminate\Support\Facades\Storage;

class DownloadProductImages extends Command
{
    protected $signature = 'products:download-images';
    protected $description = 'Download Mouser images, convert to WebP, store locally, update DB';

    public function handle()
    {
        $this->info("Starting image download process...");

        $total = CachedProduct::where('image', 'LIKE', 'http%')->count();
        $this->info("Total products with external images: {$total}");

        CachedProduct::where('image', 'LIKE', 'http%')
            ->chunk(200, function ($products) {

                foreach ($products as $product) {

                    try {

                        if (!$product->image || !str_starts_with($product->image, 'http')) {
                            continue;
                        }

                        $imageUrl = $product->image;

                        $ch = curl_init($imageUrl);

                        curl_setopt_array($ch, [
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_TIMEOUT => 30,
                            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0 Safari/537.36',
                            CURLOPT_HTTPHEADER => [
                                'Accept: image/webp,image/apng,image/*,*/*;q=0.8',
                                'Referer: https://www.mouser.com/'
                            ],
                        ]);
                        
                        $imageContents = curl_exec($ch);
                        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                        curl_close($ch);
                        
                        if ($httpCode !== 200 || !$imageContents) {
                            $this->warn("HTTP {$httpCode}: {$product->product_key}");
                            continue;
                        }

                        if (!$imageContents) {
                            $this->warn("Failed: {$product->product_key}");
                            continue;
                        }

                        $imagick = new \Imagick();
                        $imagick->readImageBlob($imageContents);
                        $imagick->setImageFormat('webp');
                        $imagick->setImageCompressionQuality(75);

                        $safeKey = preg_replace('/[^A-Za-z0-9\-]/', '_', $product->product_key);
$filename = 'products/' . $safeKey . '.webp';
                        $fullPath = storage_path('app/public/' . $filename);

                        if ($imagick->writeImage($fullPath)) {
                            if (file_exists($fullPath)) {
                                $product->image = 'storage/' . $filename;
                                $product->save();
                                $this->info("Converted: {$product->product_key}");
                            } else {
                                $this->warn("File not saved: {$product->product_key}");
                            }
                        } else {
                            $this->warn("Write failed: {$product->product_key}");
                        }

                        $product->image = 'storage/' . $filename;
                        $product->save();

                        $this->info("Converted: {$product->product_key}");

                        usleep(200000); // 0.2 sec delay (avoid rate limit)

                    } catch (\Exception $e) {
                        $this->error("Error: {$product->product_key}");
                    }
                }

                sleep(1); // pause between chunks
            });

        $this->info("Image download completed.");
    }
}