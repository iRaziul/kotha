<?php

// config for Larament/Kotha
return [
    /*
    |--------------------------------------------------------------------------
    | Default Driver
    |--------------------------------------------------------------------------
    | This is the provider Kotha will use unless specified otherwise.
    */
    'default' => env('KOTHA_DRIVER', 'log'),

    /*
    |--------------------------------------------------------------------------
    | SMS Drivers Configuration
    |--------------------------------------------------------------------------
    | Add your credentials for the providers here.
    */
    'drivers' => [
        'esms' => [
            'api_token' => env('KOTHA_ESMS_TOKEN'),
            'sender_id' => env('KOTHA_ESMS_SENDER_ID'),
        ],
        'mimsms' => [
            'username' => env('KOTHA_MIMSMS_USERNAME'),
            'api_key' => env('KOTHA_MIMSMS_API_KEY'),
            'sender_id' => env('KOTHA_MIMSMS_SENDER_ID'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | HTTP Request Configuration
    |--------------------------------------------------------------------------
    | Set the timeout, retry, and retry delay for the HTTP client.
    */
    'request' => [
        'timeout' => env('KOTHA_REQUEST_TIMEOUT', 10),
        'retry' => env('KOTHA_REQUEST_RETRY', 3),
        'retry_delay' => env('KOTHA_REQUEST_RETRY_DELAY', 300),
    ],
];
