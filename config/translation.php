<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Translation Source
    |--------------------------------------------------------------------------
    |
    | Choose where to load translations from: 'database' or 'files'
    | When set to 'database', translations will be loaded from the DB first,
    | with automatic fallback to PHP files if a key is not found.
    |
    */
    'source' => env('TRANSLATION_SOURCE', 'database'),

    /*
    |--------------------------------------------------------------------------
    | Fallback to Files
    |--------------------------------------------------------------------------
    |
    | When enabled, the system will automatically fallback to PHP language
    | files if a translation is not found in the database.
    |
    */
    'fallback_to_files' => env('TRANSLATION_FALLBACK_FILES', true),

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Configure how translations are cached for performance.
    |
    */
    'cache' => [
        'enabled' => env('TRANSLATION_CACHE_ENABLED', true),
        'ttl' => env('TRANSLATION_CACHE_TTL', 3600), // 1 hour
        'prefix' => 'trans',
        'tags' => ['translations'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Import Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for importing translation files.
    |
    */
    'import' => [
        'max_file_size' => 10240, // KB (10MB)
        'allowed_formats' => ['csv', 'json', 'xlsx'],
        'batch_size' => 500, // Process in batches for large files
        'timeout' => 300, // 5 minutes for large imports
    ],

    /*
    |--------------------------------------------------------------------------
    | Export Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for exporting translation files.
    |
    */
    'export' => [
        'formats' => ['csv', 'json', 'xlsx'],
        'include_empty' => false, // Include keys with empty values
        'include_timestamps' => false, // Add created_at/updated_at to exports
    ],

    /*
    |--------------------------------------------------------------------------
    | Sync Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for syncing translations from PHP files to database.
    |
    */
    'sync' => [
        'scan_directories' => [
            'lang',
        ],
        'exclude_groups' => [
            'validation', // Laravel's default, don't sync
        ],
    ],
];
