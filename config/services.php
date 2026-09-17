<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'google' => [
        'places_api_key' => env('GOOGLE_PLACES_API_KEY'),
    ],

    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
    ],

    'huggingface' => [
        'token' => env('HF_TOKEN'),
        'model' => env('HF_MODEL', 'Qwen/Qwen2.5-7B-Instruct:fastest'),
        'base_url' => env('HF_BASE_URL', 'https://router.huggingface.co/v1'),
    ],

    'campay' => [
        'use_demo' => filter_var(env('CAMPAY_USE_DEMO', true), FILTER_VALIDATE_BOOLEAN),
        'base_url' => env(
            'CAMPAY_BASE_URL',
            filter_var(env('CAMPAY_USE_DEMO', true), FILTER_VALIDATE_BOOLEAN)
                ? 'https://demo.campay.net'
                : 'https://www.campay.net'
        ),
        'app_id' => env('CAMPAY_APP_ID'),
        'username' => env('CAMPAY_USERNAME'),
        'password' => env('CAMPAY_PASSWORD'),
        'token' => env('CAMPAY_TOKEN'),
        'webhook_key' => env('CAMPAY_WEBHOOK_KEY'),
        // Local fake payments only. Keep false when using real CamPay demo/live APIs.
        'simulation' => filter_var(env('CAMPAY_SIMULATION', false), FILTER_VALIDATE_BOOLEAN),
    ],
];
