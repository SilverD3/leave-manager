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
        'host' => 'localhost',
        'username' => 'root',
        'password' => '',
        'database' => 'leave_manager',
    ],

    'Session' => [
        'timeout' => 60*60*24*2 // 2 days
    ]
];