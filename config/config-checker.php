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

        'names' => [
            '*.blade.php',
        ],
    ],

    'config' => [
        'include_paths' => [
            'config',
        ],

        'exclude_paths' => [
            'vendor',
        ],

        'names' => [
            '*.php',
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

        'names' => [
            '*.php',
        ],
    ],
];
