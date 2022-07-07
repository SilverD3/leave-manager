<?php

if(!defined('DS')){
    define('DS', DIRECTORY_SEPARATOR);
}

define('ROOT', dirname(__DIR__));

define('APP_DIR', 'src');

define('APP', ROOT . DS . APP_DIR . DS);

define('CONFIG', ROOT . DS . 'config' . DS);

define('CORE_PATH', ROOT . DS . 'Core' . DS);

define('MODEL_PATH', APP . 'Model' . DS);

define('CONTROLLER_PATH', APP . 'Controller' . DS);

define('VIEW_PATH', APP . 'View' . DS);

define('SERVICE_PATH', APP . 'Service' . DS);
