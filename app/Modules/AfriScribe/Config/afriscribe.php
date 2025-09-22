<?php

return [
    /*
    |--------------------------------------------------------------------------
    | AfriScribe Module Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration options for the AfriScribe module.
    |
    */

    'admin_email' => env('AFRISCRIBE_ADMIN_EMAIL', 'researchfripub@gmail.com'),

    'upload' => [
        'max_size' => env('AFRISCRIBE_MAX_UPLOAD_SIZE', 10240), // 10MB in KB
        'allowed_extensions' => ['pdf', 'doc', 'docx', 'txt'],
        'disk' => env('AFRISCRIBE_UPLOAD_DISK', 'public'),
    ],

    'pricing' => [
        'UK' => [
            'proofreading' => [
                'rate' => 0.02, // £0.02 per word
                'rush_multiplier' => 1.5,
                'plagiarism_check' => 50.00
            ],
            'copy_editing' => [
                'rate' => 0.03,
                'rush_multiplier' => 1.5,
                'plagiarism_check' => 50.00
            ],
            'substantive_editing' => [
                'rate' => 0.05,
                'rush_multiplier' => 1.5,
                'plagiarism_check' => 50.00
            ]
        ],
        'Nigeria' => [
            'proofreading' => [
                'rate' => 8.00, // ₦8 per word
                'rush_multiplier' => 1.5,
                'plagiarism_check' => 20000.00
            ],
            'copy_editing' => [
                'rate' => 12.00,
                'rush_multiplier' => 1.5,
                'plagiarism_check' => 20000.00
            ],
            'substantive_editing' => [
                'rate' => 20.00,
                'rush_multiplier' => 1.5,
                'plagiarism_check' => 20000.00
            ]
        ]
    ],

    'services' => [
        'proofreading' => 'Proofreading',
        'editing' => 'Editing',
        'formatting' => 'Formatting',
    ],

    'locations' => [
        'UK' => 'United Kingdom',
        'Nigeria' => 'Nigeria',
    ],

    'features' => [
        'file_upload' => true,
        'dynamic_pricing' => true,
        'email_notifications' => true,
        'admin_panel' => true,
    ],

    'notifications' => [
        'admin' => [
            'enabled' => true,
            'email' => env('AFRISCRIBE_ADMIN_EMAIL', 'researchfripub@gmail.com'),
        ],
        'client' => [
            'enabled' => true,
            'acknowledgment' => true,
        ],
    ],
];
