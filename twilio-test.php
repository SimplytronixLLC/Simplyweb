<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$accountSid = env('TWILIO_ACCOUNT_SID');
$apiKey     = env('TWILIO_API_KEY');
$apiSecret  = env('TWILIO_API_SECRET');

$from = '+13026002514';
$to   = '+13026002554';

if (!$accountSid || !$apiKey || !$apiSecret) {
    die("Missing TWILIO_ACCOUNT_SID, TWILIO_API_KEY or TWILIO_API_SECRET in .env\n");
}

$url = "https://api.twilio.com/2010-04-01/Accounts/{$accountSid}/Calls.json";

$postData = http_build_query([
    'To'     => $to,
    'From'   => $from,
    'Url'    => 'https://simplytronix.com/voice/twiml',
    'Method' => 'POST',
]);

$ch = curl_init($url);

curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $postData,
    CURLOPT_RETURNTRANSFER => true,

    // Twilio API Key authentication
    CURLOPT_USERPWD        => $apiKey . ':' . $apiSecret,
    CURLOPT_HTTPAUTH       => CURLAUTH_BASIC,

    CURLOPT_TIMEOUT        => 30,
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error    = curl_error($ch);

curl_close($ch);

echo "HTTP: {$httpCode}\n";

if ($error) {
    echo "cURL ERROR: {$error}\n";
    exit(1);
}

$data = json_decode($response, true);

if ($httpCode >= 200 && $httpCode < 300) {

    echo "CALL CREATED SUCCESSFULLY\n";
    echo "Call SID: " . ($data['sid'] ?? 'unknown') . "\n";
    echo "Status:   " . ($data['status'] ?? 'unknown') . "\n";
    echo "From:     " . ($data['from'] ?? 'unknown') . "\n";
    echo "To:       " . ($data['to'] ?? 'unknown') . "\n";

} else {

    echo "TWILIO CALL FAILED\n";
    echo "Code:     " . ($data['code'] ?? 'unknown') . "\n";
    echo "Message:  " . ($data['message'] ?? 'unknown') . "\n";
}
