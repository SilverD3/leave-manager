<?php

declare(strict_types=1);


/**
 * Leave manager : Simple app for contract and leave management.
 *
 * @copyright Copyright (c) Silevester D. (https://github.com/SilverD3)
 * @link      https://github.com/SilverD3/leave-manager Leave Manager Project
 * @since     1.0 (2022)
 */

namespace Core\Database;

require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'autoload.php';

use \PDO;
use Core\Configure;

/**
 * This class is used to create and manage connection with DBMS
 */
class ConnectionManager
{
    private string $host;
    private string $username;
    private string $password;
    private string $database;

    private $_connection_error;

    private $_connection;

    public function __construct()
    {
        $config = (new Configure())->read('DataSource');

        if (empty($config)) {
            $this->_connection_error = "Aucune source de données n'est configurée";
        } else {
            $this->setConfig($config);

            $dsn = "mysql:host=$this->host;dbname=$this->database;charset=UTF8";

            try {
                $this->_connection = new PDO($dsn, $this->username, $this->password);
                $this->_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (\PDOException $e) {
                $this->_connection_error = $e->getMessage();
            }
        }
    }

    /**
     * Get the connection object
     */
    public function getConnection()
    {
        return $this->_connection;
    }

    /**
     * Set connection properties
     *
     * @param array $config 
     * @return void
     */
    public function setConfig(array $config)
    {
        if (isset($config['host'])) {
            $this->host = $config['host'];
        }

        if (isset($config['username'])) {
            $this->username = $config['username'];
        }

        if (isset($config['password'])) {
            $this->password = $config['password'];
        }

        if (isset($config['database'])) {
            $this->database = $config['database'];
        }
    }

    public function getDatabase()
    {
        return $this->database;
    }

    public function getError()
    {
        return $this->_connection_error;
    }

    public function closeConnection(): void
    {
        $this->_connection = null;
    }
}
