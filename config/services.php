<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    
    'mouser' => [
        'key' => env('MOUSER_API_KEY'),
    ],
    
    'digikey' => [
        'client_id'     => env('DIGIKEY_CLIENT_ID'),
        'client_secret' => env('DIGIKEY_CLIENT_SECRET'),
        'keys' => array_values(array_filter([
            [
                'client_id'     => env('DIGIKEY_CLIENT_ID'),
                'client_secret' => env('DIGIKEY_CLIENT_SECRET'),
            ],
            env('DIGIKEY_CLIENT_ID_1') ? [
                'client_id'     => env('DIGIKEY_CLIENT_ID_1'),
                'client_secret' => env('DIGIKEY_CLIENT_SECRET_1'),
            ] : null,
            env('DIGIKEY_CLIENT_ID_2') ? [
                'client_id'     => env('DIGIKEY_CLIENT_ID_2'),
                'client_secret' => env('DIGIKEY_CLIENT_SECRET_2'),
            ] : null,
             env('DIGIKEY_CLIENT_ID_3') ? [
                'client_id'     => env('DIGIKEY_CLIENT_ID_3'),
                'client_secret' => env('DIGIKEY_CLIENT_SECRET_3'),
            ] : null,
            
             env('DIGIKEY_CLIENT_ID_4') ? [
                'client_id'     => env('DIGIKEY_CLIENT_ID_4'),
                'client_secret' => env('DIGIKEY_CLIENT_SECRET_4'),
            ] : null,
            
             env('DIGIKEY_CLIENT_ID_5') ? [
                'client_id'     => env('DIGIKEY_CLIENT_ID_5'),
                'client_secret' => env('DIGIKEY_CLIENT_SECRET_5'),
            ] : null,
            
            env('DIGIKEY_CLIENT_ID_6') ? [
                'client_id'     => env('DIGIKEY_CLIENT_ID_6'),
                'client_secret' => env('DIGIKEY_CLIENT_SECRET_6'),
            ] : null,
        ])),
    ],
    
    'nexar' => [
    'client_id' => env('NEXAR_CLIENT_ID'),
    'client_secret' => env('NEXAR_CLIENT_SECRET'),
],

];
