<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CachedProduct;
use App\Helpers\CategoryMatcher;

class CategoryMapper extends Command
{
    protected $signature = 'products:rebuild';
    protected $description = 'Assign categories using description only';

    public function handle()
    {
        \DB::table('cached_products')->update([
            'category_level_1'=>null,
            'category_level_2'=>null,
            'category_level_3'=>null,
            'category_id'=>null
        ]);

        $total = CachedProduct::count();

        $this->info("Total: ".$total);

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        CachedProduct::chunk(500, function ($products) use ($bar) {

            foreach ($products as $product) {

                $description = is_string($product->description) ? $product->description : '';

                [$l1, $l2, $l3] = CategoryMatcher::matchFromDescription($description);

                if (!$l3) {
                    $bar->advance();
                    continue;
                }

                $product->update([
                    'category_id'=>$l3->id,
                    'category_level_1'=>$l1 ? $l1->id : null,
                    'category_level_2'=>$l2 ? $l2->id : null,
                    'category_level_3'=>$l3->id
                ]);

                $bar->advance();
            }

        });

        $bar->finish();
        $this->newLine(2);
        $this->info('Rebuild complete');
    }
}