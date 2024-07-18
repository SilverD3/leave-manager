<?php

/**
 * Leave manager : Simple app for contract and leave management.
 *
 * @copyright Copyright (c) Silevester D. (https://github.com/SilverD3)
 * @link      https://github.com/SilverD3/leave-manager Leave Manager Project
 * @since     1.0 (2022)
 */

return [
    'DataSource' => [
        'host' => env('DATABASE_HOST', 'localhost'),
        'username' => env('DATABASE_USER', 'root'),
        'password' => env('DATABASE_PASSWORD', ''),
        'database' => env('DATABASE_NAME', 'leave_manager'),
    ],

    'Session' => [
        'timeout' => 60 * 60 * 24 * 2 // 2 days
    ],

    // Debug config
    "Debug" => [
        'enable' => env('DEBUG', true)
    ],

    // Mail
    'Mail'=> [
        'host'=> env('MAILER_HOST', 'ssl://smtp.gmail.com'),
        'port' => env('MAILER_PORT', 465),
        'username'=> env('MAILER_USER', ''),
        'password'=> env('MAILER_PASSWORD', ''),
        'tls'=> env('MAILER_USE_TLS', true),
    ],
];
