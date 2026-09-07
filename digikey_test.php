<?php

$ch = curl_init("https://api.digikey.com");

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo "cURL Error: " . curl_error($ch);
} else {
    echo "Connected to DigiKey API successfully<br>";
    echo "Response length: " . strlen($response);
}

curl_close($ch);