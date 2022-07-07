<?php
declare(strict_types=1);

namespace Core\Database;
use \PDO;

/**
 * This class is used to create and manage connection with DBMS
 */
class ConnectionManager {
    private string $host;
    private string $username;
    private string $password;
    private string $database;

    private $_connection_error;

    private $_connection;

    public function __construct()
    {
        $config = require dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'app.php';
        
        $this->setConfig($config['DataSource']);

        $dsn = "mysql:host=$this->host;dbname=$this->database;charset=UTF8";

        try {
            $this->_connection = new PDO($dsn, $this->username, $this->password);
            $this->_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            $this->_connection_error = $e->getMessage();
        }
    }

    /**
     * Get the connection object
     */
    public function getConnection(){
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
        if(isset($config['host'])){
            $this->host = $config['host'];
        }

        if(isset($config['username'])){
            $this->username = $config['username'];
        }

        if(isset($config['password'])){
            $this->password = $config['password'];
        }

        if(isset($config['database'])){
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

}