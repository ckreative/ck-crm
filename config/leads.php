<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Leads Package Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the behavior of the CK CRM Leads package.
    |
    */

    'routes' => [
        'enabled' => false, // Disabled to use organization-scoped routes
        'prefix' => 'leads',
        'middleware' => ['web', 'auth', 'admin'],
        'as' => 'leads.',
    ],

    'navigation' => [
        'enabled' => true,
        'icon' => 'heroicon-o-users',
        'position' => 'main', // main or settings
    ],

    'features' => [
        'archive' => true,
        'bulk_actions' => true,
        'export' => false,
        'import' => false,
    ],

    'calcom' => [
        'enabled' => env('LEADS_CALCOM_ENABLED', false),
        'api_key' => env('CALCOM_API_KEY'),
        'sync_enabled' => env('LEADS_CALCOM_SYNC_ENABLED', false),
        'sync_days' => env('LEADS_CALCOM_SYNC_DAYS', 30),
    ],

    'database' => [
        'table_name' => 'leads',
        'use_uuid' => true,
    ],

    'authorization' => [
        'enabled' => true,
        'admin_only' => true,
    ],

    'views' => [
        'layout' => 'x-app-layout',
        'styles' => 'tailwind', // tailwind or bootstrap
    ],
];