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

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'Maps' => [
        'api_key' => env('MAPS_API_KEY'),
    ],

    'docusign' => [
        'client_id' => env('DOCUSIGN_CLIENT_ID'),
        'user_id' => env('DOCUSIGN_USER_ID'),
        'key_path' => env('DOCUSIGN_KEY_PATH'),
        'account_id' => env('DOCUSIGN_ACCOUNT_ID'),
        'oauth_base_path' => env('DOCUSIGN_OAUTH_BASE_PATH', 'account-d.docusign.com'),
        'scope' => 'signature impersonation',
        'signature_artiste_anchor' => '/signature-artiste/',
        'signature_contractant_anchor' => '/signature-contractant/',
        'date_artiste_anchor' => '/date-artiste/',
        'date_contractant_anchor' => '/date-contractant/'
    ],

];
