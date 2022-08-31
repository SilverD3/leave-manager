<?php
declare(strict_types=1);

namespace App\Service;

require_once dirname(dirname(__DIR__)) . DS . DS . 'autoload.php';

use Core\Database\ConnectionManager;
use App\Entity\Config;
use Core\FlashMessages\Flash;
use Core\Utils\Session;

/**
 * Configs Services
 */
class ConfigsServices
{
    private $connectionManager;

    function __construct()
    {
        $this->connectionManager = new ConnectionManager();
    }

    /**
     * Get all configs
     * 
     * @return Config[] List of configs or empty array
     */
    public function getAll()
    {
        $result = [];
        $configs = [];

        $sql = "SELECT * FROM configs";

        try {
            $query = $this->connectionManager->getConnection()->prepare($sql);
            $query->execute();
            $result = $query->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new \Exception("SQL Exception: " . $e->getMessage(), $e->getCode());
        }

        if (empty($result)) {
			return [];
		}

		foreach ($result as $row) {
            $config = new Config();
            $config->setId($row['id']);
            $config->setCode($row['code']);
            $config->setDescription($row['description']);
            $config->setDefaultValue($row['default_value']);
            $config->setValue($row['value']);
            $config->setValueType($row['value_type']);
            $config->setModified($row['modified']);
            $config->setModifiedBy($row['modified_by']);

            $configs[] = $config;
        }

        return $configs;
    }

    /**
     * Retrieve specific config by id
     * 
     * @param int $id Config id
     * @return Config|null Return the config or null if not found
     */
    public function get(int $id): ?Config
    {
        $sql = "SELECT * FROM configs WHERE id = :id";

        try {
            $query = $this->connectionManager->getConnection()->prepare($sql);
            $query->bindParam(':id', $id, \PDO::PARAM_INT);

            $query->execute();

            $result = $query->fetch(\PDO::FETCH_ASSOC);

            if (empty($result)) {
                return null;
            }

            $config = new Config();
            $config->setId($result['id']);
            $config->setCode($result['code']);
            $config->setDescription($result['description']);
            $config->setDefaultValue($result['default_value']);
            $config->setValue($result['value']);
            $config->setValueType($result['value_type']);
            $config->setModified($result['modified']);
            $config->setModifiedBy($result['modified_by']);

            return $config;
        } catch (\PDOException $e) {
            throw new \Exception("SQL Exception: " . $e->getMessage(), $e->getCode());
        }
    }

    /**
     * Retrieve specific config by code
     * 
     * @param string $code Config code
     * @return Config|null Return the config or null if not found
     */
    public function getByCode(string $code): ?Config
    {
        $sql = "SELECT * FROM configs WHERE code = :code";

        try {
            $query = $this->connectionManager->getConnection()->prepare($sql);
            $query->bindParam(':code', $code, \PDO::PARAM_INT);

            $query->execute();

            $result = $query->fetch(\PDO::FETCH_ASSOC);

            if (empty($result)) {
                return null;
            }

            $config = new Config();
            $config->setId($result['id']);
            $config->setCode($result['code']);
            $config->setDescription($result['description']);
            $config->setDefaultValue($result['default_value']);
            $config->setValue($result['value']);
            $config->setValueType($result['value_type']);
            $config->setModified($result['modified']);
            $config->setModifiedBy($result['modified_by']);

            return $config;
        } catch (\PDOException $e) {
            throw new \Exception("SQL Exception: " . $e->getMessage(), $e->getCode());
        }
    }

    /**
     * Update a config
     * 
     * @param array|Config $data Config data
     * @return bool Returns true if config was updated successfully, false otherwise.
     */
    public function update(array|Config $config_data): bool
    {
        if (is_array($config_data)) {
            $config_data = $this->toEntity($config_data);
        }
        
        $config = $this->get($config_data->getId());

        if (empty($config)) {
            throw new \Exception("Record non trouvé dans les paramètres.", 1);
        }

        $config->setValue($config_data->getValue());
        $config->setModified(date('Y-m-d H:i:s'));
        $config->setModifiedBy($config_data->getModifiedBy());

        $errors = $config->validation();
        if (!empty($errors)) {
            foreach ($errors as $error) {
                Flash::error($error);
            }
            
            Session::write('__formdata__', json_encode($_POST));

            return false;
        }

        $sql = "UPDATE configs SET value = :value, modified = :modified, modified_by = :modified_by WHERE id = :id";

        try {
            $query = $this->connectionManager->getConnection()->prepare($sql);
            
            $query->bindValue(':value', $config->getValue(), \PDO::PARAM_STR);
            $query->bindValue(':modified', $config->getModified(), \PDO::PARAM_STR);
            $query->bindValue(':modified_by', $config->getModifiedBy(), \PDO::PARAM_INT);
            $query->bindValue(':id', $config->getId(), \PDO::PARAM_INT);

            $updated = $query->execute();

            return $updated;
        } catch(\PDOException $e) {
            throw new \Exception("SQL Exception: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    /**
     * Parse array to Config object
     *
     * @param array $data Data to parse
     * @return Config|null Returns parsed object or null
     */
    public function toEntity(array $data): ?Config
    {
        $id = !empty($data['id']) ? (int)$data['id'] : null;
        $code = !empty($data['code']) ? $data['code'] : null;
        $description = !empty($data['description']) ? $data['description'] : null;
        $default_value = !empty($data['default_value']) ? $data['default_value'] : null;
        $value = !empty($data['value']) ? $data['value'] : null;
        $value_type = !empty($data['value_type']) ? $data['value_type'] : null;
        $modified_by = !empty($data['modified_by']) ? $data['modified_by'] : null;
        $modified = !empty($data['modified']) ? $data['modified'] : null;

        $config = new Config();
        $config->setId($id);
        $config->setCode($code);
        $config->setDescription($description);
        $config->setDefaultValue($default_value);
        $config->setValue($value);
        $config->setValueType($value_type);
        $config->setModified($modified);
        $config->setModifiedBy($modified_by);

        return $config;
    }
}