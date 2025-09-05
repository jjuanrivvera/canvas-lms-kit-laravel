<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Canvas Connection
    |--------------------------------------------------------------------------
    |
    | This option controls the default Canvas connection that will be used
    | when using the Canvas LMS Kit. This connection will be used unless
    | another connection is explicitly specified.
    |
    */

    'default' => env('CANVAS_CONNECTION', 'main'),

    /*
    |--------------------------------------------------------------------------
    | Canvas Connections
    |--------------------------------------------------------------------------
    |
    | Here you may configure one or more Canvas LMS connections. Each
    | connection represents a different Canvas instance or account. This is
    | useful for multi-tenant applications or when working with multiple
    | Canvas environments (production, sandbox, etc.).
    |
    | Each connection requires at minimum an API key and base URL. The
    | account_id defaults to 1 if not specified.
    |
    */

    'connections' => [

        'main' => [
            /*
            |--------------------------------------------------------------
            | Authentication Mode
            |--------------------------------------------------------------
            |
            | Choose between 'api_key' or 'oauth' authentication.
            | This determines which credentials are used for API calls.
            |
            */

            'auth_mode'   => env('CANVAS_AUTH_MODE', 'api_key'), // 'api_key' or 'oauth'

            /*
            |--------------------------------------------------------------
            | API Key Authentication
            |--------------------------------------------------------------
            */

            'api_key'     => env('CANVAS_API_KEY'),
            'base_url'    => env('CANVAS_BASE_URL', 'https://canvas.instructure.com'),
            'account_id'  => env('CANVAS_ACCOUNT_ID', 1),
            'timeout'     => env('CANVAS_TIMEOUT', 30),
            'api_version' => env('CANVAS_API_VERSION', 'v1'),
            'log_channel' => env('CANVAS_LOG_CHANNEL'),

            /*
            |--------------------------------------------------------------
            | OAuth 2.0 Configuration
            |--------------------------------------------------------------
            |
            | Required when auth_mode is set to 'oauth'.
            | These settings enable OAuth 2.0 authentication flow.
            |
            */

            'oauth_client_id'     => env('CANVAS_OAUTH_CLIENT_ID'),
            'oauth_client_secret' => env('CANVAS_OAUTH_CLIENT_SECRET'),
            'oauth_redirect_uri'  => env('CANVAS_OAUTH_REDIRECT_URI'),
            'oauth_token'         => env('CANVAS_OAUTH_TOKEN'),
            'oauth_refresh_token' => env('CANVAS_OAUTH_REFRESH_TOKEN'),

            /*
            |--------------------------------------------------------------
            | Middleware Configuration
            |--------------------------------------------------------------
            |
            | Configure middleware for retry logic, rate limiting, etc.
            | Uncomment and adjust as needed.
            |
            */

            'middleware' => [
                // 'retry' => [
                //     'max_attempts' => 3,
                //     'delay' => 1000,
                //     'multiplier' => 2,
                // ],
                // 'rate_limit' => [
                //     'wait_on_limit' => true,
                //     'max_requests_per_second' => 10,
                // ],
            ],
        ],

        /*
        |--------------------------------------------------------------
        | Additional Connections
        |--------------------------------------------------------------
        |
        | You may define additional Canvas connections here for
        | multi-tenant applications or multiple environments.
        |
        */

        // 'sandbox' => [
        //     'api_key' => env('CANVAS_SANDBOX_API_KEY'),
        //     'base_url' => env('CANVAS_SANDBOX_BASE_URL'),
        //     'account_id' => env('CANVAS_SANDBOX_ACCOUNT_ID', 1),
        //     'timeout' => env('CANVAS_SANDBOX_TIMEOUT', 30),
        //     'log_channel' => env('CANVAS_SANDBOX_LOG_CHANNEL'),
        // ],

        // 'tenant_2' => [
        //     'api_key' => env('CANVAS_TENANT2_API_KEY'),
        //     'base_url' => env('CANVAS_TENANT2_BASE_URL'),
        //     'account_id' => env('CANVAS_TENANT2_ACCOUNT_ID', 1),
        // ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Testing Configuration
    |--------------------------------------------------------------------------
    |
    | These options control the behavior of the Canvas LMS Kit during
    | testing. When 'fake' is enabled, API calls will be mocked.
    |
    */

    'testing' => [
        'fake'           => env('CANVAS_FAKE', false),
        'fake_data_path' => storage_path('canvas/fake-data'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Control caching behavior for Canvas API responses. This can help
    | reduce API calls and improve performance. Note: This feature
    | requires additional implementation in your application.
    |
    */

    'cache' => [
        'enabled' => env('CANVAS_CACHE_ENABLED', false),
        'ttl'     => env('CANVAS_CACHE_TTL', 3600), // seconds
        'store'   => env('CANVAS_CACHE_STORE'),
        'prefix'  => env('CANVAS_CACHE_PREFIX', 'canvas'),
    ],

];
