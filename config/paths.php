<?php

if(!defined('DS')){
    define('DS', DIRECTORY_SEPARATOR);
}

if (!defined('ROOT')) {
    define('ROOT', dirname(__DIR__));
}

if (!defined('HOST_URL')) {
    define('BASE_DIR', '/leave-manager');
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

if (!defined('TEMPLATE_PATH')) {
    define('TEMPLATE_PATH', BASE_DIR . '/template' . DS);
}

// Assets files
if (!defined('VIEWS')) {
    define('VIEWS', BASE_DIR . '/' . APP_DIR . '/View/');
}

if (!defined('ASSETS')) {
    define('ASSETS', BASE_DIR . '/assets' . DS);
}

if (!defined('IMAGES')) {
    define('IMAGES', ASSETS . 'images' . DS);
}
