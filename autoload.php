<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'paths.php';

/**
 * Classes autoloader
 */
class AutoLoader
{
    public function __autoload($className)
    {
        if (strpos($className, '\\') !== false) {
            $fqnParts = explode('\\', $className);

            if ($fqnParts[0] === 'App') {
                switch ($fqnParts[1]) {
                    case 'Entity':
                        $this->__autoLoadModel($className);
                        break; 
                    case 'Controller':
                        $this->__autoLoadController($className);
                        break; 
                    case 'Service':
                        $this->__autoLoadService($className);
                        break; 
                    case 'View':
                        $this->__autoLoadView($className);
                        break; 
                    default:
                        $this->__autoloadClass($className);
                        break;
                }
            } elseif ($fqnParts[0] === 'Core') {
                $this->__autoLoadCoreClass($className);
            } else {
                $this->__autoloadClass($className);
            }
        } else {
            $this->__autoloadClass($className);
        }
    }

    public function __autoloadClass($className)
    {
        $filePath = $className . '.php';

        if (strpos($className, '\\') !== false) {
            $fqnParts = explode('\\', $className);
            $filePath = implode(DS, $fqnParts) . '.php';
        }
        
        if (file_exists($filePath)) {
            require_once $filePath;
        } else {
            throw new \Exception(sprintf('Could not load class "%s" . Class file not found', $className));
        }
    }

    public function __autoLoadCoreClass($className)
    {
        $fqnParts = explode('\\', $className);

        unset($fqnParts[0]);
        
        $filePath = CORE_PATH . implode(DS, $fqnParts) . '.php';

        if (file_exists($filePath)) {
            require_once $filePath;
        } else {
            throw new \Exception(sprintf('Could not load Core class "%s".', $className));
        }
    }    

    public function __autoLoadModel($className)
    {
        $fqnParts = explode('\\', $className);

        unset($fqnParts[0]);
        unset($fqnParts[1]);

        $filePath = MODEL_PATH . implode(DS, $fqnParts) . '.php';

        if (file_exists($filePath)) {
            require_once $filePath;
        } else {
            throw new \Exception(sprintf('Could not load Model "%s" . Model class file not found', $className));
        }
    }

    public function __autoLoadController($className)
    {
        $fqnParts = explode('\\', $className);

        unset($fqnParts[0]);
        unset($fqnParts[1]);

        $filePath = CONTROLLER_PATH . implode(DS, $fqnParts) . '.php';

        if (file_exists($filePath)) {
            require_once $filePath;
        } else {
            throw new \Exception(sprintf('Could not load Controller "%s" . Controller class file not found', $className));
        }
    }

    public function __autoLoadService($className)
    {
        $fqnParts = explode('\\', $className);

        unset($fqnParts[0]);
        unset($fqnParts[1]);

        $filePath = SERVICE_PATH . implode(DS, $fqnParts) . '.php';

        if (file_exists($filePath)) {
            require_once $filePath;
        } else {
            throw new \Exception(sprintf('Could not load Service "%s" . Service class file not found', $className));
        }
    }

    public function __autoLoadView($className)
    {
        $fqnParts = explode('\\', $className);

        unset($fqnParts[0]);
        unset($fqnParts[1]);

        $filePath = VIEW_PATH . implode(DS, $fqnParts) . '.php';

        if (file_exists($filePath)) {
            require_once $filePath;
        } else {
            throw new \Exception(sprintf('Could not load View class "%s".', $className));
        }
    }

    /**
     * Register all autoloaders
     *
     * @return void
     */
    public function register()
    {
        try {
            spl_autoload_register(array($this, "__autoload"), true);
        } catch (\Exception $e) {
            $e->getMessage();
        }
    }
}

(new AutoLoader())->register();

use Core\Configure;

// Start session
if (session_status() === PHP_SESSION_NONE) {
    $configure = new Configure();

    $default_session_config = [
        'timeout' => 60*60*24*2 // 2 days
    ];

    $session_config = $configure->read('Session', $default_session_config);
    
    ini_set('session.gc_maxlifetime', $session_config['timeout']);
    session_set_cookie_params($session_config['timeout']); 

    session_start();
    
    $_SESSION['__configure__'] = serialize($configure);
}
