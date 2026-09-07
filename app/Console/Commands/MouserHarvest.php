<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\CachedProduct;
use App\Models\ApiUsage;
use Carbon\Carbon;

class MouserHarvest extends Command
{
    protected $signature = 'mouser:harvest';
    protected $description = 'Mouser Harvester Continuous Mode';

    private $apiKey;
    private $perPage = 50;
    private $apiCalls = 0;
    private $maxApiCalls = 500;

    private $dailyApiUsed = 0;
    private $dailyLimit = 1000;

    private $totalFetched = 0;
    private $totalInserted = 0;

    private $stopReason = 'Completed';

    public function handle()
    {
        $this->apiKey = config('services.mouser.key');

        if (function_exists('pcntl_signal')) {
            pcntl_signal(SIGINT, function () {
                $this->stopReason = 'User interrupted (Ctrl+C)';
                $this->printSummary();
                exit;
            });
        }

        $this->dailyApiUsed = ApiUsage::where('provider','mouser')
            ->whereDate('called_at', now()->toDateString())
            ->count();

        $categoryMap = DB::table('source_category_map')
            ->pluck('category_id','source_category_name');

        $this->info("Starting Mouser Harvest (Continuous Mode)...");
        $this->info("Today's API usage: ".$this->dailyApiUsed." / ".$this->dailyLimit);

        DB::table('harvest_control')->update([
            'is_running' => 1,
            'stop_requested' => 0,
            'api_used' => $this->dailyApiUsed,
        ]);

        // 🔁 OUTER LOOP (runs until API exhausted)
        while (true) {

            if ($this->dailyApiUsed >= $this->dailyLimit) {
                $this->stopReason = 'Daily API limit reached';
                break;
            }

            if ($this->apiCalls >= $this->maxApiCalls) {
                $this->stopReason = 'Session API limit reached';
                break;
            }

            $keywords = DB::table("mouser_fetch_progress")
                ->where(function ($q) {
                    $q->whereNull("next_check_at")
                      ->orWhere("next_check_at", "<=", now());
                })
                ->orderBy("id", "desc")
                ->get();

            if ($keywords->isEmpty()) {
                $this->warn("No keywords found. Retrying...");
                sleep(5);
                continue;
            }

            foreach ($keywords as $row) {

                if ($this->dailyApiUsed >= $this->dailyLimit) {
                    $this->stopReason = 'Daily API limit reached';
                    break 2;
                }

                if ($this->apiCalls >= $this->maxApiCalls) {
                    $this->stopReason = 'Session API limit reached';
                    break 2;
                }

                // Completed keywords are naturally excluded/included via next_check_at
                // in the query above. Offset and is_completed are never modified here.

                $keyword = $row->keyword;
                $offset = $row->offset;
                $lowEfficiencyCount = 0;

                DB::table('mouser_fetch_progress')
                    ->where('id', $row->id)
                    ->update([
                        'status' => 'running',
                        'last_run_at' => now()
                    ]);

                DB::table('harvest_control')->update([
                    'is_running' => 1,
                    'current_keyword' => $keyword,
                    'current_offset' => $offset,
                    'api_used' => $this->dailyApiUsed,
                ]);

                $this->line("--------------------------------------------------");
                $this->info("Keyword: {$keyword} | Offset: {$offset}");

                while (true) {

                    if ($this->dailyApiUsed >= $this->dailyLimit) {
                        $this->stopReason = 'Daily API limit reached';
                        break 3;
                    }

                    if ($this->apiCalls >= $this->maxApiCalls) {
                        $this->stopReason = 'Session API limit reached';
                        break 3;
                    }

                    $control = DB::table('harvest_control')->first();

                    if ($control && $control->stop_requested) {
                        $this->stopReason = 'Stopped from dashboard';
                        break 3;
                    }

                    $this->line("Fetching... offset {$offset}");

                    try {

                        $this->throttle();

                        $response = Http::withHeaders([
                            'Content-Type' => 'application/json'
                        ])
                        ->timeout(30)
                        ->retry(3, 2000)
                        ->post(
                            'https://api.mouser.com/api/v1/search/keyword?apiKey=' . $this->apiKey,
                            [
                                'SearchByKeywordRequest' => [
                                    'keyword' => $keyword,
                                    'records' => $this->perPage,
                                    'startingRecord' => $offset,
                                ]
                            ]
                        );

                    } catch (\Exception $e) {

                        DB::table('mouser_fetch_progress')
                            ->where('id', $row->id)
                            ->update([
                                'offset' => $offset,
                                'status' => 'pending',
                                'updated_at' => now()
                            ]);

                        sleep(5);
                        continue;
                    }

                    $this->apiCalls++;
                    $this->dailyApiUsed++;

                    ApiUsage::create([
                        'provider'   => 'mouser',
                        'called_at'  => now(),
                        'endpoint'   => 'keyword-search',
                        'query'      => $keyword,
                        'controller' => 'mouser:harvest',
                        'ip_address' => 'CLI',
                        'user_agent' => 'CLI',
                    ]);

                    DB::table('harvest_control')->update([
                        'api_used' => $this->dailyApiUsed,
                    ]);

                    if (!$response->successful()) continue;

                    $data = $response->json();
                    if (!$data) continue;

                    $parts = $data['SearchResults']['Parts'] ?? [];

                    if (!$parts) {
                        DB::table("mouser_fetch_progress")
                            ->where("id", $row->id)
                            ->update([
                                "is_completed" => 1,
                                "status" => "completed",
                                "next_check_at" => now()->addHours(24),
                                "updated_at" => now()
                            ]);
                        break;
                    }

                    $fetched = count($parts);
                    $inserted = 0;

                    foreach ($parts as $part) {

                        $productKey =
                            $part['ManufacturerPartNumber']
                            ?? $part['MouserPartNumber']
                            ?? null;

                        if (!$productKey) continue;

                        $productKey = strtoupper(trim($productKey));

                        $exists = CachedProduct::where('product_key', $productKey)->exists();

                        $categoryName = $part['Category'] ?? null;

                        if (is_array($categoryName)) {
                            $categoryName = $categoryName['Name'] ?? null;
                        }

                        $categoryId = $categoryMap[$categoryName] ?? null;
                        if (!$categoryId) continue;

                        $desc = $part['Description'] ?? null;
                        if ($desc) {
                            $desc = preg_replace('/Mouser[^.]*\.?/i', '', $desc);
                        }

                        $imageUrl = $part['ImagePath'] ?? null;
                        $datasheetUrl = $part['DataSheetUrl'] ?? null;

                        CachedProduct::updateOrCreate(
                            ['product_key'=>$productKey],
                            [
                                'name'=>$productKey,
                                'description'=>$desc,
                                'image'=>$imageUrl,
                                'category'=>$categoryName,
                                'category_id'=>$categoryId,
                                'manufacturer'=>$part['Manufacturer'] ?? null,
                                'unit_price'=>isset($part['PriceBreaks'][0]['Price'])
                                    ? str_replace(['$',','],'',$part['PriceBreaks'][0]['Price'])
                                    : null,
                                'quantity'=>rand(3000,15000),
                                'datasheet'=>$datasheetUrl,
                                'raw_data'=>json_encode($part),
                                'is_synced'=>1,
                                'last_synced_at'=>Carbon::now(),
                                'updated_at'=>now()
                            ]
                        );

                        if (!$exists) $inserted++;
                    }

                    $this->totalFetched += $fetched;
                    $this->totalInserted += $inserted;

                    DB::table('harvest_control')->update([
                        'total_fetched' => $this->totalFetched,
                        'total_inserted' => $this->totalInserted,
                    ]);

                    DB::table("mouser_fetch_progress")
                        ->where("id", $row->id)
                        ->increment("total_fetched", $fetched);

                    DB::table("mouser_fetch_progress")
                        ->where("id", $row->id)
                        ->increment("total_inserted", $inserted);

                    $efficiency = $fetched > 0 ? ($inserted / $fetched) * 100 : 0;
                    $remaining = $this->dailyLimit - $this->dailyApiUsed;

                    $this->line("Fetched: {$fetched} | Inserted: {$inserted} | Efficiency: " . round($efficiency,2) . "%");
                    $this->info("API Remaining: {$remaining} / {$this->dailyLimit}");

                    if ($efficiency < 10) {
                        $lowEfficiencyCount++;
                    } else {
                        $lowEfficiencyCount = 0;
                    }

                    if ($lowEfficiencyCount >= 3) {
                        DB::table('mouser_fetch_progress')
                            ->where('id', $row->id)
                            ->update([
                                'offset' => $offset,
                                'status' => 'pending',
                                'updated_at' => now()
                            ]);
                        break;
                    }

                    $offset += $this->perPage;

                    DB::table('mouser_fetch_progress')
                        ->where('id', $row->id)
                        ->update([
                            'offset' => $offset,
                            'status' => 'running',
                            'updated_at' => now()
                        ]);

                    DB::table('harvest_control')->update([
                        'current_offset' => $offset,
                    ]);

                    if (function_exists('pcntl_signal_dispatch')) {
                        pcntl_signal_dispatch();
                    }
                }
            }
        }

        DB::table('harvest_control')->update([
            'is_running' => 0,
        ]);

        $this->printSummary();
    }

    private function throttle()
    {
        usleep(2500000);
    }

    private function printSummary()
    {
        DB::table('harvest_control')->update([
            'is_running' => 0,
            'stop_reason' => $this->stopReason,
        ]);

        $efficiency = $this->totalFetched > 0
            ? ($this->totalInserted / $this->totalFetched) * 100
            : 0;

        $this->line("==================================================");
        $this->info("FINAL SUMMARY");
        $this->info("Stop Reason: " . $this->stopReason);
        $this->info("Total Fetched: " . $this->totalFetched);
        $this->info("Total Inserted: " . $this->totalInserted);
        $this->info("Overall Efficiency: " . round($efficiency, 2) . "%");
        $this->line("==================================================");
    }
}
