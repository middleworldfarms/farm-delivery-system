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

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'wordpress' => [
        'api_key' => env('MWF_API_KEY'),
        'api_base' => env('MWF_API_BASE_URL'),
        'base_url' => env('WOOCOMMERCE_URL'),
    ],

    'woocommerce' => [
        'consumer_key' => env('WOOCOMMERCE_CONSUMER_KEY'),
        'consumer_secret' => env('WOOCOMMERCE_CONSUMER_SECRET'),
        'base_url' => env('WOOCOMMERCE_URL'),
        'api_url' => env('WOOCOMMERCE_URL'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'wp_api' => [
        'url'    => env('WP_API_URL', 'https://example.com'),
        'key'    => env('WP_API_KEY'),
        'secret' => env('WP_API_SECRET'),
    ],
    'wc_api' => [
        'url'             => env('WC_API_URL', ''),
        'consumer_key'    => env('WC_CONSUMER_KEY', ''),
        'consumer_secret' => env('WC_CONSUMER_SECRET', ''),
        'integration_key' => env('SELF_SERVE_SHOP_INTEGRATION_KEY', ''),
    ],
];
