<?php

return [
    'default' => env('CACHE_DRIVER', 'redis'),
    'stores' => [
        'file' => [
            'driver' => 'file',
            'path' => storage_path('framework/cache/data'),
        ],
        'redis' => [
            'driver' => 'redis',
            'connection' => 'default',
        ],
    ],
    'prefix' => env('CACHE_PREFIX', 'gym_system_cache'),
];
