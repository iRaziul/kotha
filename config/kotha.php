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
        'ssl' => [
            'api_token' => env('KOTHA_SSL_TOKEN'),
            'sender_id' => env('KOTHA_SSL_SENDER_ID'),
            'csms_id' => env('KOTHA_SSL_CSMS_ID'),
        ],
        'grameenphone' => [
            'username' => env('KOTHA_GP_USERNAME'),
            'password' => env('KOTHA_GP_PASSWORD'),
            'cli' => env('KOTHA_GP_CLI', '2222'),
            'message_type' => env('KOTHA_GP_MESSAGE_TYPE', 1),
        ],
        'banglalink' => [
            'user_id' => env('KOTHA_BL_USER_ID'),
            'password' => env('KOTHA_BL_PASSWORD'),
            'sender_id' => env('KOTHA_BL_SENDER_ID'),
        ],
        'robi' => [
            'username' => env('KOTHA_ROBI_USERNAME'),
            'password' => env('KOTHA_ROBI_PASSWORD'),
        ],
        'infobip' => [
            'base_url' => env('KOTHA_INFOBIP_BASE_URL'),
            'username' => env('KOTHA_INFOBIP_USERNAME'),
            'password' => env('KOTHA_INFOBIP_PASSWORD'),
            'sender_id' => env('KOTHA_INFOBIP_SENDER_ID'),
        ],
        'adnsms' => [
            'api_key' => env('KOTHA_ADNSMS_API_KEY'),
            'api_secret' => env('KOTHA_ADNSMS_API_SECRET'),
            'sender_id' => env('KOTHA_ADNSMS_SENDER_ID'),
            'request_type' => env('KOTHA_ADNSMS_REQUEST_TYPE', 'SINGLE_SMS'),
            'message_type' => env('KOTHA_ADNSMS_MESSAGE_TYPE', 'TEXT'),
        ],
        'alphasms' => [
            'api_key' => env('KOTHA_ALPHASMS_API_KEY'),
            'sender_id' => env('KOTHA_ALPHASMS_SENDER_ID'),
        ],
        'greenweb' => [
            'token' => env('KOTHA_GREENWEB_TOKEN'),
        ],
        'bulksms' => [
            'api_key' => env('KOTHA_BULKSMS_API_KEY'),
            'sender_id' => env('KOTHA_BULKSMS_SENDER_ID'),
        ],
        'elitbuzz' => [
            'url' => env('KOTHA_ELITBUZZ_URL'),
            'api_key' => env('KOTHA_ELITBUZZ_API_KEY'),
            'sender_id' => env('KOTHA_ELITBUZZ_SENDER_ID'),
            'type' => env('KOTHA_ELITBUZZ_TYPE', 'text'),
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
