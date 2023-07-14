<?php

/**
 * Leave manager : Simple app for contract and leave management.
 *
 * @copyright Copyright (c) Silevester D. (https://github.com/SilverD3)
 * @link      https://github.com/SilverD3/leave-manager Leave Manager Project
 * @since     1.0 (2022)
 */

if (version_compare(PHP_VERSION, '8.1.0', '<=')) {
    echo 'PHP version not supported. You must use version 8.1.0 or latest. Your current version is ' . PHP_VERSION . "\n";
    die();
}

require_once __DIR__ . DIRECTORY_SEPARATOR . 'autoload.php';

require_once APP_DIR . DS . 'View' . DS . 'Employees' . DS . 'dashboard.php';
