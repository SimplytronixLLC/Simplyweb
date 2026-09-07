<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class VisitorTracker
{
    protected array $skipPaths = [
        'admin', 'api', 'sitemap', 'datasheet',
        'track-part-search', 'get-level2', 'manufacturers-api',
        'submit-track-order', 'get-a-quote-submit', 'bom-process',
        '_debugbar', 'favicon',
    ];

    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if (
            $request->ajax() ||
            $request->method() !== 'GET' ||
            $this->shouldSkip($request) ||
            $this->isBot($request->userAgent() ?? '')
        ) {
            return $response;
        }

        $visitorId = $request->cookie('visitor_id');
        if (!$visitorId) {
            $visitorId = (string) Str::uuid();
            \Cookie::queue(
                cookie('visitor_id', $visitorId, 60 * 24 * 180)
            );
        }

        try {
            $this->trackVisitor($request, $visitorId);
        } catch (\Exception $e) {
            \Log::error('Visitor tracking error: ' . $e->getMessage());
        }

        return $response;
    }

    private function trackVisitor($request, string $visitorId): void
    {
        $ip = $request->ip();
        $ua = $request->userAgent() ?? '';

        // Check if profile exists
        $profile = DB::table('visitor_profiles')
            ->where('visitor_id', $visitorId)
            ->first();

        if (!$profile) {
    $geoData = $this->getGeolocation($ip);
    $referrer    = $request->headers->get('referer');
    $utmSource   = $request->query('utm_source');
    $utmMedium   = $request->query('utm_medium');
    $utmCampaign = $request->query('utm_campaign');

    DB::table('visitor_profiles')->insert([
        'visitor_id'          => $visitorId,
        'ip_address'          => $ip,
        'ip_country'          => $geoData['country'] ?? null,
        'ip_country_code'     => $geoData['country_code'] ?? null,
        'ip_region'           => $geoData['region'] ?? null,
        'ip_city'             => $geoData['city'] ?? null,
        'ip_timezone'         => $geoData['timezone'] ?? null,
        'ip_latitude'         => $geoData['lat'] ?? null,
        'ip_longitude'        => $geoData['lon'] ?? null,
        'ip_isp'              => $geoData['isp'] ?? null,
        'ip_org'              => $geoData['org'] ?? null,
        'ip_asn'              => $geoData['asn'] ?? null,
        'ip_asname'           => $geoData['asname'] ?? null,
        'device_type'         => $this->getDeviceType($ua),
        'device_brand'        => $this->getDeviceBrand($ua),
        'os_name'             => $this->getOSName($ua),
        'os_version'          => $this->getOSVersion($ua),
        'browser_name'        => $this->getBrowserName($ua),
        'browser_version'     => $this->getBrowserVersion($ua),
        'is_mobile'           => $this->isMobile($ua),
        'is_tablet'           => $this->isTablet($ua),
        'is_bot'              => false,
        'language'            => substr($request->getPreferredLanguage() ?? 'en', 0, 5),
        'first_referrer'      => $referrer ? substr($referrer, 0, 500) : null,
        'first_landing_page'  => substr($request->url(), 0, 500),
        'first_utm_source'    => $utmSource   ? substr($utmSource, 0, 100) : null,
        'first_utm_medium'    => $utmMedium   ? substr($utmMedium, 0, 100) : null,
        'first_utm_campaign'  => $utmCampaign ? substr($utmCampaign, 0, 100) : null,
        'total_session_seconds' => 0,
        'first_visit_at'      => now(),
        'last_visit_at'       => now(),
        'created_at'          => now(),
        'updated_at'          => now(),
    ]);

    DB::table('visitor_behaviors')->insert([
        'visitor_id'   => $visitorId,
        'total_visits' => 1,
        'created_at'   => now(),
        'updated_at'   => now(),
    ]);
} else {
    DB::table('visitor_profiles')
        ->where('visitor_id', $visitorId)
        ->update([
            'last_visit_at' => now(),
            'updated_at'    => now(),
        ]);

    DB::table('visitor_behaviors')
        ->where('visitor_id', $visitorId)
        ->increment('total_visits');
}

        // Track pageview
        $this->trackPageview($request, $visitorId);
    }

    private function trackPageview($request, string $visitorId): void
    {
        $url      = $request->url();
        $segments = $request->segments();
        $pageType = $this->classifyPage($segments);

        $productKey = null;
        $manufacturer = null;
        $category = null;

        if ($pageType === 'product' && count($segments) >= 3) {
            $manufacturer = $segments[1] ?? null;
            $rawPart = $segments[2] ?? null;
            if ($rawPart) {
                $productKey = strtoupper(str_replace(
                    ['__', '--'], ['/', '#'],
                    rawurldecode($rawPart)
                ));
            }
        } elseif ($pageType === 'category' && count($segments) >= 2) {
            $category = $segments[1] ?? null;
        }

        // Track pageview
        DB::table('visitor_pageviews')->insert([
            'visitor_id'   => $visitorId,
            'url'          => substr($url, 0, 500),
            'page_type'    => $pageType,
            'product_key'  => $productKey ? substr($productKey, 0, 100) : null,
            'manufacturer' => $manufacturer ? substr($manufacturer, 0, 100) : null,
            'referrer'     => $request->headers->get('referer') ? substr($request->headers->get('referer'), 0, 500) : null,
            'utm_source'   => $request->query('utm_source') ? substr($request->query('utm_source'), 0, 100) : null,
            'utm_medium'   => $request->query('utm_medium') ? substr($request->query('utm_medium'), 0, 100) : null,
            'utm_campaign' => $request->query('utm_campaign') ? substr($request->query('utm_campaign'), 0, 100) : null,
            'device'       => $this->getDeviceType($request->userAgent() ?? ''),
            'browser'      => $this->getBrowserName($request->userAgent() ?? ''),
            'ip'           => $request->ip(),
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        // Update behaviors
        DB::table('visitor_behaviors')
            ->where('visitor_id', $visitorId)
            ->increment('total_pageviews');

        if ($pageType === 'product') {
            DB::table('visitor_behaviors')
                ->where('visitor_id', $visitorId)
                ->increment('product_views');

            if ($productKey) {
                // Track specific product interest
                $existing = DB::table('visitor_interests')
                    ->where('visitor_id', $visitorId)
                    ->where('product_key', $productKey)
                    ->first();

                if ($existing) {
                    DB::table('visitor_interests')
                        ->where('visitor_id', $visitorId)
                        ->where('product_key', $productKey)
                        ->update([
                            'view_count' => $existing->view_count + 1,
                            'last_viewed_at' => now(),
                        ]);
                } else {
                    DB::table('visitor_interests')->insert([
                        'visitor_id'    => $visitorId,
                        'product_key'   => $productKey,
                        'manufacturer'  => $manufacturer,
                        'view_count'    => 1,
                        'last_viewed_at' => now(),
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ]);

                    DB::table('visitor_behaviors')
                        ->where('visitor_id', $visitorId)
                        ->increment('unique_products_viewed');
                }
            }
        }

        // Recalculate engagement score
        $this->updateEngagementScore($visitorId);
    }

    private function updateEngagementScore(string $visitorId): void
    {
        $behavior = DB::table('visitor_behaviors')
            ->where('visitor_id', $visitorId)
            ->first();

        if (!$behavior) return;

        // Scoring: pageviews (1pt each), searches (5pts), products viewed (3pts), quote (20pts)
        $score = 0;
        $score += $behavior->total_pageviews * 1;
        $score += $behavior->total_searches * 5;
        $score += $behavior->product_views * 3;
        $score += $behavior->quote_requests * 20;

        // Time-based bonus
        $profile = DB::table('visitor_profiles')
            ->where('visitor_id', $visitorId)
            ->first();

        if ($profile && $profile->first_visit_at) {
            $daysSinceFirstVisit = now()->diffInDays(\Carbon\Carbon::parse($profile->first_visit_at));
            if ($daysSinceFirstVisit > 0 && $daysSinceFirstVisit <= 30) {
                $score += 10; // Hot lead - recent activity
            }
        }

        DB::table('visitor_profiles')
            ->where('visitor_id', $visitorId)
            ->update([
                'engagement_score' => $score,
                'updated_at'       => now(),
            ]);
    }

    private function getGeolocation(string $ip): array
{
    if (empty($ip) || $ip === '127.0.0.1' || str_starts_with($ip, '192.168.')) {
        return [];
    }

    $cacheKey = 'geo_ip_' . md5($ip);
    $cached = \Cache::get($cacheKey);
    if ($cached) return $cached;

    $result = $this->curlGeo("https://ipwho.is/{$ip}");
    if ($result) {
        $data = json_decode($result, true);
        if (!empty($data['success'])) {
            $geo = [
                'country'      => $data['country'] ?? null,
                'country_code' => $data['country_code'] ?? null,
                'region'       => $data['region'] ?? null,
                'city'         => $data['city'] ?? null,
                'timezone'     => $data['timezone']['id'] ?? null,
                'lat'          => $data['latitude'] ?? null,
                'lon'          => $data['longitude'] ?? null,
                'isp'          => $data['connection']['isp'] ?? null,
                'org'          => $data['connection']['org'] ?? null,
                'asn'          => isset($data['connection']['asn'])
                                    ? 'AS' . $data['connection']['asn'] : null,
                'asname'       => $data['connection']['org'] ?? null,
            ];
            \Cache::put($cacheKey, $geo, now()->addDays(30));
            return $geo;
        }
    }

    // Fallback: ipinfo.io
    $result = $this->curlGeo("https://ipinfo.io/{$ip}/json");
    if ($result) {
        $data = json_decode($result, true);
        if (!empty($data['ip'])) {
            [$lat, $lon] = explode(',', $data['loc'] ?? ',');
            $orgRaw = $data['org'] ?? null;
            $asn = $org = null;
            if ($orgRaw && preg_match('/^(AS\d+)\s+(.+)$/', $orgRaw, $m)) {
                $asn = $m[1];
                $org = $m[2];
            }
            $geo = [
                'country'      => $data['country'] ?? null,
                'country_code' => $data['country'] ?? null,
                'region'       => $data['region'] ?? null,
                'city'         => $data['city'] ?? null,
                'timezone'     => $data['timezone'] ?? null,
                'lat'          => trim($lat) ?: null,
                'lon'          => trim($lon) ?: null,
                'isp'          => $org,
                'org'          => $org,
                'asn'          => $asn,
                'asname'       => $org,
            ];
            \Cache::put($cacheKey, $geo, now()->addDays(30));
            return $geo;
        }
    }

    return [];
}

private function curlGeo(string $url): ?string
{
    if (!function_exists('curl_init')) return null;

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 3,
        CURLOPT_CONNECTTIMEOUT => 2,
        CURLOPT_USERAGENT      => 'Mozilla/5.0 (compatible; simplytronix/1.0)',
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_FOLLOWLOCATION => true,
    ]);
    $response = curl_exec($ch);
    $error    = curl_error($ch);
    curl_close($ch);

    if ($error) {
        \Log::warning("curlGeo failed [{$url}]: {$error}");
        return null;
    }

    return $response ?: null;
}

    private function classifyPage(array $segments): string
    {
        $first = $segments[0] ?? '';
        return match(true) {
            $first === ''                  => 'home',
            $first === 'product'           => 'product',
            $first === 'category'          => 'category',
            $first === 'blog'              => 'blog',
            $first === 'get-a-quote'       => 'quote',
            $first === 'available-stock'   => 'stock',
            $first === 'manufacturers'     => 'manufacturers',
            $first === 'news-alerts'       => 'news',
            $first === 'about-us'          => 'about',
            $first === 'contact-us'        => 'contact',
            default                        => 'other',
        };
    }

    private function shouldSkip($request): bool
    {
        $path = $request->path();
        foreach ($this->skipPaths as $skip) {
            if (str_starts_with($path, $skip)) return true;
        }
        return false;
    }

    private function isBot(string $ua): bool
    {
        if (empty($ua)) return true;
        $bots = ['bot', 'crawl', 'spider', 'slurp', 'curl', 'wget',
                 'python', 'scrapy', 'facebook', 'twitter', 'google',
                 'bing', 'yahoo', 'baidu', 'yandex', 'duckduck'];
        $ua = strtolower($ua);
        foreach ($bots as $bot) {
            if (str_contains($ua, $bot)) return true;
        }
        return false;
    }

    private function getDeviceType(string $ua): string
    {
        $ua = strtolower($ua);
        if (str_contains($ua, 'mobile') || str_contains($ua, 'android')) return 'mobile';
        if (str_contains($ua, 'tablet') || str_contains($ua, 'ipad')) return 'tablet';
        return 'desktop';
    }

    private function isMobile(string $ua): bool
    {
        return $this->getDeviceType($ua) === 'mobile';
    }

    private function isTablet(string $ua): bool
    {
        return $this->getDeviceType($ua) === 'tablet';
    }

    private function getDeviceBrand(string $ua): ?string
    {
        return match(true) {
            str_contains($ua, 'iPhone') => 'Apple',
            str_contains($ua, 'iPad') => 'Apple',
            str_contains($ua, 'Samsung') => 'Samsung',
            str_contains($ua, 'Pixel') => 'Google',
            str_contains($ua, 'OnePlus') => 'OnePlus',
            str_contains($ua, 'Xiaomi') => 'Xiaomi',
            default => null,
        };
    }

    private function getOSName(string $ua): ?string
    {
        return match(true) {
            str_contains($ua, 'Windows') => 'Windows',
            str_contains($ua, 'Macintosh') => 'macOS',
            str_contains($ua, 'iPhone') => 'iOS',
            str_contains($ua, 'iPad') => 'iPadOS',
            str_contains($ua, 'Android') => 'Android',
            str_contains($ua, 'Linux') => 'Linux',
            default => null,
        };
    }

    private function getOSVersion(string $ua): ?string
    {
        if (preg_match('/Windows NT ([\d.]+)/', $ua, $m)) return $m[1];
        if (preg_match('/OS X ([\d_]+)/', $ua, $m)) return str_replace('_', '.', $m[1]);
        if (preg_match('/iPhone OS ([\d_]+)/', $ua, $m)) return str_replace('_', '.', $m[1]);
        if (preg_match('/Android ([\d.]+)/', $ua, $m)) return $m[1];
        return null;
    }

    private function getBrowserName(string $ua): ?string
    {
        return match(true) {
            str_contains($ua, 'Chrome') && !str_contains($ua, 'Edg') => 'Chrome',
            str_contains($ua, 'Firefox') => 'Firefox',
            str_contains($ua, 'Safari') && !str_contains($ua, 'Chrome') => 'Safari',
            str_contains($ua, 'Edg') => 'Edge',
            str_contains($ua, 'Opera') => 'Opera',
            default => null,
        };
    }

    private function getBrowserVersion(string $ua): ?string
    {
        if (preg_match('/Chrome\/([\d.]+)/', $ua, $m)) return $m[1];
        if (preg_match('/Firefox\/([\d.]+)/', $ua, $m)) return $m[1];
        if (preg_match('/Version\/([\d.]+).*Safari/', $ua, $m)) return $m[1];
        if (preg_match('/Edg[e\/]+([\d.]+)/', $ua, $m)) return $m[1];
        return null;
    }
}