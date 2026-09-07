<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\ApiUsage;
use App\Models\CachedProduct;

class DigiKeyService
{
    /**
     * All configured API key pairs.
     * Each entry: ['client_id' => '...', 'client_secret' => '...']
     *
     * Loaded from config('services.digikey.keys'), which is an array of
     * ['client_id', 'client_secret'] pairs.  Falls back to the legacy
     * single-key config so existing deployments keep working.
     */
    protected array $keys;

    /** Index of the key currently in use (0-based). */
    public int $activeIndex = 0;

    /**
     * How many consecutive 429 responses a key must receive before we
     * rotate to the next key.
     */
    public const CONSECUTIVE_429_THRESHOLD = 5;

    /**
     * If a 429's Retry-After header exceeds this many seconds, the key is
     * considered "exhausted" for the day — log it distinctly so the
     * dashboard can flag it, and rotate immediately.
     *
     * 3 hours, per business rule.
     */
    public const EXHAUSTION_THRESHOLD_SECONDS = 10800;

    public $debugCallback = null;

    // ── Boot ─────────────────────────────────────────────────────────────────

    public function __construct()
    {
        // Support both the new multi-key array and the legacy single-key config
        $multiKeys = config('services.digikey.keys', []);

        if (!empty($multiKeys)) {
            $this->keys = array_values($multiKeys);
        } else {
            // Legacy fallback — single key pair
            $this->keys = [[
                'client_id'     => config('services.digikey.client_id'),
                'client_secret' => config('services.digikey.client_secret'),
            ]];
        }

        // Restore whichever key was active when the last run ended
        $this->activeIndex = (int) Cache::get('digikey_active_key_index', 0);

        // Clamp in case keys were removed since the last run
        if ($this->activeIndex >= count($this->keys)) {
            $this->activeIndex = 0;
        }
    }

    // ── Key helpers ───────────────────────────────────────────────────────────

    /** Return the client_id for the active key. */
    protected function clientId(): string
    {
        return $this->keys[$this->activeIndex]['client_id'] ?? '';
    }

    /** Return the client_secret for the active key. */
    protected function clientSecret(): string
    {
        return $this->keys[$this->activeIndex]['client_secret'] ?? '';
    }

    /**
     * Return the total number of configured API key pairs.
     * Used by the dashboard to compute the daily limit dynamically.
     */
    public function keyCount(): int
    {
        return count($this->keys);
    }

    /**
     * Cache key for the OAuth token of a given key-pair index.
     * Each key gets its own token so they don't overwrite each other.
     */
    protected function tokenCacheKey(int $index): string
    {
        return "digikey_token_{$index}";
    }

    /**
     * Cache key for the consecutive-429 counter of a given key-pair index.
     */
    protected function consecutive429Key(int $index): string
    {
        return "digikey_consecutive_429_{$index}";
    }

    /**
     * Cache key for the "blocked until" timestamp of a given key-pair index.
     */
    protected function blockedUntilKey(int $index): string
    {
        return "digikey_blocked_until_{$index}";
    }

    // ── Token management ──────────────────────────────────────────────────────

    /**
     * Fetch (or return a cached) OAuth access token for the given key index.
     * Returns null if the token request fails.
     */
    public function getAccessToken(int $index = -1): ?string
    {
        if ($index < 0) {
            $index = $this->activeIndex;
        }

        $cacheKey = $this->tokenCacheKey($index);

        if ($token = Cache::get($cacheKey)) {
            return $token;
        }

        $key = $this->keys[$index] ?? null;
        if (!$key) {
            return null;
        }

        $response = Http::timeout(30)
            ->asForm()
            ->post('https://api.digikey.com/v1/oauth2/token', [
                'client_id'     => $key['client_id'],
                'client_secret' => $key['client_secret'],
                'grant_type'    => 'client_credentials',
            ]);

        if (!$response->ok()) {
            Log::warning("DigiKey token request failed for key index {$index}", [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            return null;
        }

        $data      = $response->json();
        $token     = $data['access_token'] ?? null;
        $expiresIn = $data['expires_in']   ?? 1800;

        if ($token) {
            Cache::put($cacheKey, $token, now()->addSeconds($expiresIn - 60));
        }

        return $token;
    }

    // ── Key rotation ──────────────────────────────────────────────────────────

    /**
     * Handle a 429 response for the active key.
     *
     * - If Retry-After > EXHAUSTION_THRESHOLD_SECONDS (3 hrs): key is
     *   considered exhausted for the day — mark blocked and rotate immediately.
     * - If Retry-After <= threshold: mark key blocked, stay on same key
     *   (short throttle, caller will wait).
     * - If no Retry-After header: increment consecutive counter, rotate at
     *   CONSECUTIVE_429_THRESHOLD.
     *
     * Returns true if a usable key is now active, false if all keys are exhausted.
     */
    public function handle429(string $retryAfter = ''): bool
        {
            $idx = $this->activeIndex;
        
            $this->debug("Key #{$idx} got 429. Retry-After: '{$retryAfter}'");
        
            if (!is_numeric($retryAfter) || (int)$retryAfter <= 0) {
                // No usable Retry-After header — track consecutive 429s and
                // rotate once the threshold is hit, instead of hammering
                // the same key forever.
                $count = (int) Cache::get($this->consecutive429Key($idx), 0) + 1;
                Cache::put($this->consecutive429Key($idx), $count, now()->addHours(2));
                $this->debug("Key #{$idx} got 429 with no Retry-After header — consecutive count: {$count}");

                if ($count >= self::CONSECUTIVE_429_THRESHOLD) {
                    $this->debug("Key #{$idx} hit consecutive 429 threshold ({$count}) — rotating to next key");
                    return $this->rotateToNextAvailableKey();
                }

                return true;
            }
        
            $seconds      = (int)$retryAfter;
            $blockedUntil = now()->addSeconds($seconds);
            Cache::put($this->blockedUntilKey($idx), $blockedUntil->toDateTimeString(), $blockedUntil);
            $this->debug("Key #{$idx} blocked for {$seconds}s until {$blockedUntil}");
        
            // Retry-After > 3 hours means daily quota exhausted — rotate immediately
            if ($seconds > self::EXHAUSTION_THRESHOLD_SECONDS) {
                $this->debug("Retry-After > 3 hours — key #{$idx} EXHAUSTED, rotating to next key");
                return $this->rotateToNextAvailableKey();
            }
        
            // Short block — stay on same key, caller will wait out the Retry-After
            return true;
        }

    /**
     * Walk through all keys starting after the current active index and
     * activate the first one that is not currently blocked.
     *
     * Returns true if a usable key was found and activated, false if every
     * key is blocked.
     */
    protected function rotateToNextAvailableKey(): bool
    {
        $total = count($this->keys);

        for ($offset = 1; $offset <= $total; $offset++) {
            $candidate = ($this->activeIndex + $offset) % $total;

            // Skip if this candidate is still blocked
            $blockedUntil = Cache::get($this->blockedUntilKey($candidate));
            if ($blockedUntil && now()->lt($blockedUntil)) {
                $this->debug("Key #{$candidate} still blocked until {$blockedUntil}, skipping");
                continue;
            }

            $this->activeIndex = $candidate;
            Cache::put('digikey_active_key_index', $candidate, now()->addDays(1));

            // Reset consecutive-429 counter for the newly active key
            Cache::put($this->consecutive429Key($candidate), 0, now()->addHours(2));

            $this->debug("Rotated to key #{$candidate}");
            return true;
        }

        $this->debug("All keys exhausted / blocked");
        return false; // All keys are blocked
    }

    /**
     * Mark the active key as healthy (reset its 429 counter).
     * Call this after any successful API response.
     */
    protected function markKeyHealthy(): void
    {
        Cache::put($this->consecutive429Key($this->activeIndex), 0, now()->addHours(2));
    }

    /**
     * Return a summary of every key's current state.
     * Useful for dashboard / artisan command output.
     *
     * @return array<int, array{index: int, client_id: string, active: bool, blocked_until: string|null, consecutive_429: int}>
     */
    public function keyStatuses(): array
    {
        $statuses = [];

        foreach ($this->keys as $index => $key) {
            $blockedUntil = Cache::get($this->blockedUntilKey($index));
            $statuses[]   = [
                'index'           => $index,
                'client_id'       => substr($key['client_id'], 0, 8) . '...',
                'active'          => $index === $this->activeIndex,
                'blocked_until'   => $blockedUntil,
                'consecutive_429' => (int) Cache::get($this->consecutive429Key($index), 0),
            ];
        }

        return $statuses;
    }

    // ── Core API call with auto-rotate ────────────────────────────────────────

    /**
     * Make a GET request to a DigiKey product endpoint.
     *
     * Handles:
     *   - 401 → refresh token and retry once
     *   - 429 → call handle429(); if a new key is available, retry once
     *
     * Returns the HTTP response object, or null if all keys are exhausted.
     *
     * NOTE: This method tracks $lastKeyIndexUsed, $lastStatusCode, and
     * $lastRetryAfterSeconds as it goes, so the caller (fetchProductSpecs)
     * can log exactly which key handled the call and what happened — even
     * if a retry on a different key ultimately succeeded.
     */
    protected ?int $lastKeyIndexUsed     = null;
    protected ?int $lastStatusCode       = null;
    protected ?int $lastRetryAfterSeconds = null;

    protected function makeProductRequest(string $url): ?\Illuminate\Http\Client\Response
    {
        $token = $this->getAccessToken();
        if (!$token) return null;

        $this->lastKeyIndexUsed      = $this->activeIndex;
        $this->lastStatusCode         = null;
        $this->lastRetryAfterSeconds  = null;

        try {
            $response = $this->httpGet($url, $token);
        } catch (\Illuminate\Http\Client\RequestException $e) {
            $response = $e->response;
        }

        if ($response->status() === 401) {
            Cache::forget($this->tokenCacheKey($this->activeIndex));
            $token = $this->getAccessToken();
            if (!$token) return null;
            try {
                $response = $this->httpGet($url, $token);
            } catch (\Illuminate\Http\Client\RequestException $e) {
                $response = $e->response;
            }
        }

        if ($response->status() === 429) {
            $retryAfter = $response->header('Retry-After') ?? '';

            // Capture the 429 event against the key that actually received it,
            // BEFORE we potentially rotate to a different key.
            $this->lastKeyIndexUsed        = $this->activeIndex;
            $this->lastStatusCode          = 429;
            $this->lastRetryAfterSeconds   = is_numeric($retryAfter) ? (int) $retryAfter : null;

            $this->debug("429 received. Retry-After: '{$retryAfter}'");
            $hasNextKey = $this->handle429($retryAfter);

            if (!$hasNextKey) {
                return $response;
            }

            $token = $this->getAccessToken();
            if (!$token) return null;
            try {
                $response = $this->httpGet($url, $token);
            } catch (\Illuminate\Http\Client\RequestException $e) {
                $response = $e->response;
            }

            // If the retry on the new key succeeds, lastStatusCode should
            // reflect the FINAL outcome, but we keep the 429 + retry-after
            // captured above for logging purposes — see fetchProductSpecs().
        }

        if ($response->ok()) {
            $this->markKeyHealthy();
        }

        return $response;
    }

    /**
     * Raw HTTP GET with the standard DigiKey headers for the active key.
     */
    protected function httpGet(string $url, string $token): \Illuminate\Http\Client\Response
    {
        return Http::timeout(30)
            ->withHeaders([
                'Authorization'             => 'Bearer ' . $token,
                'X-DIGIKEY-Client-Id'       => $this->clientId(),
                'X-DIGIKEY-Locale-Site'     => 'US',
                'X-DIGIKEY-Locale-Language' => 'en',
                'X-DIGIKEY-Locale-Currency' => 'USD',
                'Accept'                    => 'application/json',
            ])
            ->get($url);
    }

    // ── Public product-spec fetch ─────────────────────────────────────────────

    public function fetchProductSpecs(string $mpn, string $calledBy = 'fetchProductSpecs'): ?array
    {
        $mpn      = strtoupper(trim($mpn));
        $cacheKey = "digikey_specs_{$mpn}";
        $cached   = Cache::store('database')->get($cacheKey);

        if (!empty($cached)) {
            if (is_array($cached) && isset($cached[0]['name'])) {
                return ['specs' => $cached, 'datasheet' => null, 'raw' => null];
            }
            return $cached;
        }

        // ── DB cache lookup ───────────────────────────────────────────────────
        $existingProduct = CachedProduct::where('product_key', $mpn)->first();

        // Skip if we already know DigiKey has nothing for this MPN
        if ($existingProduct && $existingProduct->digikey_no_result) {
            return null;
        }

        if (!$existingProduct) {
            $parent = preg_replace('/[A-Z0-9]+$/', '', $mpn);
            if ($parent) {
                $existingProduct = CachedProduct::where('product_key', 'LIKE', $parent . '%')
                    ->whereNotNull('specs')
                    ->first();
            }
        }

        if ($existingProduct && !empty($existingProduct->specs)) {
            $specs = is_array($existingProduct->specs)
                ? $existingProduct->specs
                : json_decode($existingProduct->specs, true);

            if (!empty($specs)) {
                $result = [
                    'specs'     => $specs,
                    'datasheet' => $existingProduct->datasheet ?? null,
                    'raw'       => null,
                ];
                Cache::store('database')->put($cacheKey, $result, now()->addDays(7));
                return $result;
            }
        }

        // ── Live API fetch ────────────────────────────────────────────────────
        $encodedMpn = rawurlencode($mpn);
        $url        = "https://api.digikey.com/products/v4/search/{$encodedMpn}/productdetails";

        $response = $this->makeProductRequest($url);

        // Log with the caller context, key index, status code, and any
        // retry-after captured during the request (even if a retry on a
        // rotated key ultimately succeeded — we still record the 429 that
        // triggered the rotation).
        $this->logApiUsage(
            'productdetails',
            $mpn,
            $calledBy,
            $this->lastKeyIndexUsed,
            $this->lastStatusCode ?? $response?->status(),
            $this->lastRetryAfterSeconds
        );

        if (!$response) {
            return null;
        }

        if ($response->status() === 429) {
            return ['status_code' => 429, 'specs' => [], 'datasheet' => null, 'raw' => null];
        }

        if (!$response->ok()) {
            // Cache the failure so we don't retry for 24 hours
            Cache::store('database')->put($cacheKey, ['specs' => [], 'datasheet' => null, 'raw' => null], now()->addHours(24));

            // Mark in DB so even if cache clears, we don't retry
            CachedProduct::updateOrCreate(
                ['product_key' => $mpn],
                [
                    'digikey_no_result'  => 1,
                    'digikey_checked_at' => now(),
                ]
            );

            return $this->fetchViaKeyword($mpn, $calledBy);
        }

        $data       = $response->json();
        $parameters = $data['Product']['Parameters'] ?? [];
        $datasheet  = $data['Product']['DatasheetUrl'] ?? null;

        if (empty($parameters)) {
            // Cache the failure so we don't retry for 24 hours
            Cache::store('database')->put($cacheKey, ['specs' => [], 'datasheet' => null, 'raw' => null], now()->addHours(24));

            // Mark in DB so even if cache clears, we don't retry
            CachedProduct::updateOrCreate(
                ['product_key' => $mpn],
                [
                    'digikey_no_result'  => 1,
                    'digikey_checked_at' => now(),
                ]
            );

            return $this->fetchViaKeyword($mpn, $calledBy);
        }

        $specs = $this->formatSpecs($parameters);

        if (!empty($specs)) {
            $result = [
                'specs'     => $specs,
                'datasheet' => $datasheet,
                'raw'       => $data,
            ];
            Cache::store('database')->put($cacheKey, $result, now()->addDays(7));
            return $result;
        }

        return $this->fetchViaKeyword($mpn, $calledBy);
    }

    // ── Keyword fallback ──────────────────────────────────────────────────────

    private function fetchViaKeyword(string $mpn, string $calledBy = 'fetchViaKeyword'): ?array
    {
        $token = $this->getAccessToken();
        if (!$token) {
            return null;
        }

        $keyIndexUsed = $this->activeIndex;

        $response = Http::timeout(30)
            ->withHeaders([
                'Authorization'       => 'Bearer ' . $token,
                'X-DIGIKEY-Client-Id' => $this->clientId(),
                'Accept'              => 'application/json',
            ])
            ->post('https://api.digikey.com/products/v4/search/keyword', [
                'Keywords'    => $mpn,
                'RecordCount' => 1,
            ]);

        $retryAfterSeconds = null;
        if ($response->status() === 429) {
            $retryAfterHeader  = $response->header('Retry-After') ?? '';
            $retryAfterSeconds = is_numeric($retryAfterHeader) ? (int) $retryAfterHeader : null;

            // Rotate keys on 429 here too — this fallback path previously
            // never rotated, letting a blocked key get hammered forever.
            $this->handle429($retryAfterHeader);
        }

        // Log the keyword fallback call with its own caller label, key index,
        // and status/retry-after.
        $this->logApiUsage(
            'keyword-search',
            $mpn,
            $calledBy . '→fetchViaKeyword',
            $keyIndexUsed,
            $response->status(),
            $retryAfterSeconds
        );

        if (!$response->ok()) {
            return null;
        }

        $data     = $response->json();
        $products = $data['ExactMatches'] ?? $data['Products'] ?? [];

        foreach ($products as $product) {
            if (!empty($product['Parameters'])) {
                return [
                    'specs'     => $this->formatSpecs($product['Parameters']),
                    'datasheet' => $product['DatasheetUrl'] ?? null,
                    'raw'       => $product,
                ];
            }
        }

        return null;
    }

    // ── Shared helpers ────────────────────────────────────────────────────────

    private function formatSpecs(array $parameters): ?array
    {
        $specs = [];

        foreach ($parameters as $param) {
            if (empty($param['ParameterText']) || empty($param['ValueText'])) {
                continue;
            }
            $specs[] = [
                'name'  => trim($param['ParameterText']),
                'value' => trim($param['ValueText']),
            ];
        }

        return !empty($specs) ? $specs : null;
    }

    /**
     * Log an API call with the exact caller context, which key handled it,
     * the HTTP status code, and (if a 429) the Retry-After duration.
     *
     * @param string   $endpoint            e.g. 'productdetails', 'keyword-search'
     * @param string   $query               The MPN or search term
     * @param string   $calledBy            Human-readable caller label
     * @param int|null $keyIndex            Which configured key (0-based) handled this call
     * @param int|null $statusCode          HTTP status returned
     * @param int|null $retryAfterSeconds   Retry-After value in seconds, only set on 429s
     */
    private function logApiUsage(
        string $endpoint,
        string $query,
        string $calledBy = 'DigiKeyService',
        ?int $keyIndex = null,
        ?int $statusCode = null,
        ?int $retryAfterSeconds = null
    ): void {
        try {
            ApiUsage::create([
                'provider'            => 'digikey',
                'called_at'           => now(),
                'endpoint'            => $endpoint,
                'query'               => $query,
                'controller'          => $calledBy,
                'key_index'           => $keyIndex,
                'status_code'         => $statusCode,
                'retry_after_seconds' => $retryAfterSeconds,
                'ip_address'          => request()?->ip() ?? 'system',
                'user_agent'          => request()?->userAgent() ?? 'artisan',
            ]);
        } catch (\Throwable $e) {
            // Never let logging break the sync
        }
    }

    private function debug(string $msg): void
    {
        if ($this->debugCallback) {
            ($this->debugCallback)($msg);
        }
    }
}