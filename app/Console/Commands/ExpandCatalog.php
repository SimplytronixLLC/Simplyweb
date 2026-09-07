<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CachedProduct;

class ExpandCatalog extends Command
{
    protected $signature = 'catalog:expand';
    protected $description = 'Expand catalog using Mouser API (900 max calls)';

    public function handle()
    {
        $maxApiCalls = 900;
        $apiCallsUsed = 0;

        $keywords = [
            'SN7','STM','ATC','ATM','ATT','PIC','IRF','IRS','IRL',
            'BZX','BAT','BAV','BSS','BC5','BD1','BF1','BS1','BUK','BYV','BZV',
            'CD4','CMX','CPH','CS5','CXA','CY7','DAC','DB1','DCM','DDZ','DFF',
            'DG4','DH1','DI5','DJA','DK1','DLW','DMC','DMP','DNF','DO2','DP8',
            'DQ1','DR1','DS1','DT1','DU1','DV1','DW1','DX1','DY1','DZ1',
            'EPC','EPF','EPH','EPM','EPN','EPS','EQC','ERJ','ERS','ERT',
            'FAN','FDD','FDP','FDS','FDV','FGA','FGH','FGL','FQP','FQT',
            'G4P','GBJ','GCM','GDT','GFS','GHN','GJM','GLF','GMZ','GQM',
            'H11','HC4','HD7','HEF','HF3','HG2','HIH','HLK','HM6','HMC',
            'INA','INL','INS','IPB','IPD','IR2','IR3','IR4','IRL','IRS',
            'ISL','ISO','ITS','IVN','IXA','IXF','IXT','IXY',
            'J11','J12','J13','J17','J18','J19','J21','J22','J23','J24',
            'KIA','KIC','KID','KIA','KSD','KSH','KSM','KSP','KST','KTK',
            'L78','L79','LA6','LB1','LC7','LD1','LD2','LD3','LD5','LD7',
            'LE3','LF3','LG3','LH0','LI2','LJ1','LL4','LM1','LM2','LM3',
            'LM7','LMC','LME','LNK','LP2','LP3','LP5','LP8','LQH','LR8',
            'LS7','LT1','LT3','LT6','LTC','LTM','LTS','LTV','LVC','LVR',
            'MAX','MC1','MC3','MC7','MCP','MCR','MDD','MDS','MEC','MEF',
            'MIC','MJD','MJE','MJL','MMD','MMZ','MPM','MPQ','MPS','MRF',
            'MSP','MUR','MX2','MX6','MY4','MZ2',
            'NCP','NCS','NE5','NE6','NJM','NJU','NKH','NLD','NLU','NMC',
            'NPN','NPS','NRF','NSV','NTC','NTS','NVT','NX3','NX5','NX7',
            'OPA','OPB','OPI','OPL','OPN','OPS','OPT','OPX','OPZ',
            'PAM','PBH','PC8','PCA','PCH','PCM','PCS','PDC','PDF','PDN',
            'PDS','PE4','PE6','PEM','PEX','PF0','PF1','PF2','PF3','PF4',
            'PG1','PG2','PG3','PG4','PG5','PHB','PHD','PHM','PHN','PHX',
            'PIC','PID','PIE','PIF','PIM','PIN','PIO','PIP','PIQ','PIR',
            'PIS','PIT','PIU','PIV','PIX','PIY','PIZ',
            'QPA','QPF','QPI','QPL','QPM','QPN','QPP','QPR','QPS','QPT',
            'QPU','QPV','QPX','QPY','QPZ',
            'RBR','RCL','RCP','RDC','RDF','RDH','RDL','RDM','RDP','RDR',
            'RDS','RDT','RDX','RDY','RDZ',
            'SN1','SN2','SN3','SN4','SN5','SN6','SN7','SN8','SN9',
            'SP2','SP3','SP4','SP5','SP6','SP7','SP8','SP9',
            'STM','STP','STR','STS','STV','STW','STX',
            'TPS','TLC','TLE','TLV','TLW','TLX','TMS','TMP','TNY','TPA',
            'UCC','ULN','UMA','UMC','UMD','UME','UMF','UMG','UMH','UMK',
            'VND','VNQ','VNS','VNT','VNX','VNY','VNZ',
            'XTR','XTP','XTL','XTN','XTR','XTV',
            'ZTX','ZVN','ZVP','ZXM','ZXN','ZXP','ZXQ','ZXR'
        ];

        foreach ($keywords as $keyword) {

            if ($apiCallsUsed >= $maxApiCalls) {
                break;
            }

            $this->info("Expanding keyword: {$keyword}");

            $startingRecord = 0;

            while ($apiCallsUsed < $maxApiCalls) {

                $apiKey = config('services.mouser.key');
                $url = "https://api.mouser.com/api/v1/search/keyword?apiKey={$apiKey}";

                $postData = json_encode([
                    "SearchByKeywordRequest" => [
                        "keyword" => $keyword,
                        "records" => 50,
                        "startingRecord" => $startingRecord,
                        "searchOptions" => "None",
                        "searchWithYourSignUpLanguage" => false
                    ]
                ]);

                $ch = curl_init($url);
                curl_setopt_array($ch, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POST => true,
                    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                    CURLOPT_POSTFIELDS => $postData,
                    CURLOPT_TIMEOUT => 60,
                ]);

                $response = curl_exec($ch);

                if (curl_errno($ch)) {
                    $this->error("Curl error: " . curl_error($ch));
                    curl_close($ch);
                    break;
                }

                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($httpCode !== 200) {
                    $this->error("HTTP error {$httpCode}");
                    break;
                }

                $data = json_decode($response, true);

                if (!isset($data['SearchResults'])) {
                    $this->error("Invalid API response");
                    break;
                }

                $parts = $data['SearchResults']['Parts'] ?? [];

                if (empty($parts)) {
                    break;
                }

                foreach ($parts as $part) {

                    $productKey =
                        $part['ManufacturerPartNumber']
                        ?? $part['MouserPartNumber']
                        ?? null;

                    if (!$productKey) continue;

                    $productKey = strtoupper(trim($productKey));

                    CachedProduct::updateOrCreate(
                        ['product_key' => $productKey],
                        [
                            'name'         => $productKey,
                            'description'  => $part['Description'] ?? null,
                            'image'        => $part['ImagePath'] ?? null,
                            'category'     => $part['Category'] ?? null,
                            'manufacturer' => $part['Manufacturer'] ?? null,
                            'unit_price'   => isset($part['PriceBreaks'][0]['Price'])
                                ? str_replace(['$', ','], '', $part['PriceBreaks'][0]['Price'])
                                : null,
                            'quantity'     => rand(3000, 15000),
                            'raw_data'     => json_encode($part),
                            'is_synced'    => 0,
                            'updated_at'   => now()
                        ]
                    );
                }

                $startingRecord += 50;
                $apiCallsUsed++;

                $this->info("API Call {$apiCallsUsed} | {$keyword} offset {$startingRecord}");

                if (count($parts) < 50) {
                    break;
                }

                sleep(2);
            }
        }

        $this->info("Expansion completed. API calls used: {$apiCallsUsed}");
    }
}