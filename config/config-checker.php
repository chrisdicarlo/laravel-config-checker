<?php

declare(strict_types=1);

return [
    'blade' => [
        'include_paths' => [
            'resources/views',
        ],

        'exclude_paths' => [
            'vendor',
        ],
    ],

    'config' => [
        'include_paths' => [
            'config',
        ],

        'exclude_paths' => [
            'vendor',
        ],
    ],

    'php' => [
        'include_paths' => [
            'app',
            'database',
            'routes',
            'bootstrap',
        ],

        'exclude_paths' => [
            'config',
            'vendor',
        ],
    ],
];
