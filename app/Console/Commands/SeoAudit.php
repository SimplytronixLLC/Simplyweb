<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SeoAudit extends Command
{
    protected $signature   = 'seo:audit';
    protected $description = 'Rebuild SEO audit cache for all products';

    public function handle()
    {
        $this->info('Starting SEO audit...');

        $total   = DB::table('cached_products')->count();
        $bar     = $this->output->createProgressBar($total);
        $chunk   = [];
        $batchSz = 500;

        DB::table('cached_products')
            ->select('id','product_key','manufacturer','category','image','datasheet',
         'specs','description','digikey_raw','meta_title',
         'meta_description','unit_price')
            ->orderBy('id')
            ->chunk(500, function ($products) use (&$chunk, &$batchSz, $bar) {

                foreach ($products as $p) {

             // Title & desc are generated dynamically if key fields exist
                $hasGeneratedTitle = !empty($p->product_key) && !empty($p->manufacturer);
                $hasGeneratedDesc  = !empty($p->product_key) && !empty($p->description);
                
                $effectiveTitle = $p->meta_title ?? ($hasGeneratedTitle
                    ? trim(substr($p->product_key . ' ' . $p->manufacturer . ' ' . ($p->category ?? ''), 0, 60))
                    : '');
                $effectiveDesc  = $p->meta_description ?? $p->description ?? '';
                
                $titleLen = strlen($effectiveTitle);
                $descLen  = strlen($effectiveDesc);
                
                $hasImage      = !empty($p->image) ? 1 : 0;
                $hasDatasheet  = !empty($p->datasheet) ? 1 : 0;
                $hasSpecs      = !empty($p->specs) && $p->specs !== '[]' ? 1 : 0;
                $hasDesc       = !empty($p->description) ? 1 : 0;
                $hasMetaTitle  = ($hasGeneratedTitle || !empty($p->meta_title)) ? 1 : 0;
                $hasMetaDesc   = ($hasGeneratedDesc  || !empty($p->meta_description)) ? 1 : 0;
                $hasDigikey    = !empty($p->digikey_raw) && $p->digikey_raw !== '{}' ? 1 : 0;
                $titleOk       = ($titleLen >= 20 && $titleLen <= 60) ? 1 : 0;
                $descOk        = ($descLen  >= 50 && $descLen  <= 160) ? 1 : 0;
                $schemaOk      = ($hasImage && $hasDesc && !empty($p->manufacturer)) ? 1 : 0;

                    $score = ($hasImage * 15)
                           + ($hasDatasheet * 10)
                           + ($hasSpecs * 20)
                           + ($hasDesc * 15)
                           + ($hasMetaTitle * 10)
                           + ($hasMetaDesc * 10)
                           + ($hasDigikey * 10)
                           + ($titleOk * 5)
                           + ($descOk * 5);

                    $chunk[] = [
                        'product_id'      => $p->id,
                        'product_key'     => $p->product_key ?? '',
                        'manufacturer'    => $p->manufacturer ?? '',
                        'has_image'       => $hasImage,
                        'has_datasheet'   => $hasDatasheet,
                        'has_specs'       => $hasSpecs,
                        'has_description' => $hasDesc,
                        'has_meta_title'  => $hasMetaTitle,
                        'has_meta_desc'   => $hasMetaDesc,
                        'has_digikey_raw' => $hasDigikey,
                        'title_ok'        => $titleOk,
                        'desc_ok'         => $descOk,
                        'schema_ok'       => $schemaOk,
                        'score'           => $score,
                        'created_at'      => now(),
                        'updated_at'      => now(),
                    ];

                    if (count($chunk) >= $batchSz) {
                        $this->upsertChunk($chunk);
                        $chunk = [];
                    }

                    $bar->advance();
                }
            });

        if (!empty($chunk)) {
            $this->upsertChunk($chunk);
        }

        $bar->finish();
        $this->newLine();
        $this->info('SEO audit complete.');
        return 0;
    }

    private function upsertChunk(array $chunk): void
    {
        DB::table('seo_audit_cache')->upsert($chunk, ['product_id'], [
            'product_key','manufacturer','has_image','has_datasheet',
            'has_specs','has_description','has_meta_title','has_meta_desc',
            'has_digikey_raw','title_ok','desc_ok','schema_ok','score','updated_at'
        ]);
    }
}