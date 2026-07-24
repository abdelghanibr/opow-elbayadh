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
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    'remote' => [
        'secret' => env('REMOTE_API_SECRET'),
    ],
    'guiddini' => [
    'base_url'   => env('GUIDDINI_BASE_URL'),
    'app_key'    => env('GUIDDINI_APP_KEY'),
    'secret_key' => env('GUIDDINI_SECRET_KEY'),
    'return_url' => env('GUIDDINI_RETURN_URL'),
    'callback'   => env('GUIDDINI_CALLBACK_URL'),
    ],
    'satim' => [
    'register_url' => env('SATIM_REGISTER_URL'),
    'ack_url'      => env('SATIM_ACK_URL'),
    'refund_url'   => env('SATIM_REFUND_URL'),
    'username'     => env('SATIM_USERNAME'),
    'password'     => env('SATIM_PASSWORD'),
    'terminal_id'  => env('SATIM_TERMINAL_ID'),
    ],


];
