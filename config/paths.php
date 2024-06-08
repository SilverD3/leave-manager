<?php

/**
 * Leave manager : Simple app for contract and leave management.
 *
 * @copyright Copyright (c) Silevester D. (https://github.com/SilverD3)
 * @link      https://github.com/SilverD3/leave-manager Leave Manager Project
 * @since     1.0 (2022)
 */

require_once __DIR__ . DIRECTORY_SEPARATOR . "functions.php";

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

if (!defined('ROOT')) {
    define('ROOT', dirname(__DIR__));
}

if (!defined('BASE_URL')) {
    define('BASE_URL', getFullDomainUrl());
}

if (!defined('APP_DIR')) {
    define('APP_DIR', 'src');
}

if (!defined('APP')) {
    define('APP', ROOT . DS . APP_DIR . DS);
}

if (!defined('CONFIG')) {
    define('CONFIG', ROOT . DS . 'config' . DS);
}

if (!defined('CORE_PATH')) {
    define('CORE_PATH', ROOT . DS . 'Core' . DS);
}

if (!defined('MODEL_PATH')) {
    define('MODEL_PATH', APP . 'Entity' . DS);
}

if (!defined('CONTROLLER_PATH')) {
    define('CONTROLLER_PATH', APP . 'Controller' . DS);
}

if (!defined('VIEW_PATH')) {
    define('VIEW_PATH', APP . 'View' . DS);
}

if (!defined('SERVICE_PATH')) {
    define('SERVICE_PATH', APP . 'Service' . DS);
}

if (!defined('VENDOR_PATH')) {
    define('VENDOR_PATH', ROOT . DS . 'vendor' . DS);
}

if (!defined('ASSETS_PATH')) {
    define('ASSETS_PATH', ROOT . DS . 'assets' . DS);
}

if (!defined('TEMPLATE_PATH')) {
    define('TEMPLATE_PATH', BASE_URL . 'template' . DS);
}

// Assets files
if (!defined('VIEWS')) {
    define('VIEWS', BASE_URL . APP_DIR . '/View/');
}

if (!defined('ASSETS')) {
    define('ASSETS', BASE_URL . 'assets/');
}

if (!defined('UPLOADS')) {
    define('UPLOADS', BASE_URL . 'public/uploads/');
}

if (!defined('INTERNSHIP_REPORTS_DIR_NAME')) {
    define('INTERNSHIP_REPORTS_DIR_NAME', 'internship-reports');
}

if (!defined('UPLOADS_PATH')) {
    define('UPLOADS_PATH', ROOT . DS . 'public' . DS . 'uploads' . DS);
}

if (!defined('IMAGES')) {
    define('IMAGES', ASSETS . 'images' . DS);
}

if (!defined('VENDOR')) {
    define('VENDOR', BASE_URL . 'vendor' . DS);
}

if (!defined('PAGINATOR_KEY')) {
    define('PAGINATOR_KEY', "__paginator__");
}
