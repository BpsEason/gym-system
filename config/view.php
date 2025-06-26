<?php

return [
    'paths' => [
        resource_path('views'), # Even if resources/views is mostly empty, it's a standard path
    ],
    'compiled' => env(
        'VIEW_COMPILED_PATH',
        realpath(storage_path('framework/views'))
    ),
];
