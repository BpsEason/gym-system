<?php

return [
    'type' => 'laravel', // or 'dingo'
    'docs_url' => '/docs',
    'postman_url' => 'docs/collection.json',
    'bindings' => [],
    'routes' => [
        [
            'match' => [
                'domains' => ['*'],
                'prefixes' => ['api/*'],
            ],
            'include' => [],
            'exclude' => [
                'api/metrics', // Exclude Prometheus endpoint
                'api/docs',    // Exclude Scribe endpoints
            ],
            'apply' => [],
        ],
    ],
    'strategies' => [
        'metadata' => [
            \Scribe\Extracting\Strategies\Metadata\Get::class
        ],
        'urlParameters' => [
            \Scribe\Extracting\Strategies\UrlParameters\Get::class
        ],
        'queryParameters' => [
            \Scribe\Extracting\Strategies\QueryParameters\Get::class
        ],
        'headers' => [
            \Scribe\Extracting\Strategies\Headers\Get::class
        ],
        'bodyParameters' => [
            \Scribe\Extracting\Strategies\BodyParameters\GetFromFormRequest::class,
            \Scribe\Extracting\Strategies\BodyParameters\GetFromRequest::class
        ],
        'responseFields' => [
            \Scribe\Extracting\Strategies\ResponseFields\GetFromResponse::class
        ],
        'responses' => [
            \Scribe\Extracting\Strategies\Responses\Use','\Scribe\Extracting\Strategies\Responses\ResponseCalls
        ],
        'group' => [
            \Scribe\Extracting\Strategies\Tag::class
        ],
        'responseCalls' => [
            \Scribe\Extracting\Strategies\ResponseCalls\CallLaravel::class
        ],
    ],
];
