<?php
declare(strict_types=1);

namespace App\Service;

require_once dirname(dirname(__DIR__)) . DS . DS . 'autoload.php';

use Core\Database\ConnectionManager;
use App\Entity\ContractType;
use Core\FlashMessages\Flash;
use Core\Utils\Session;

/**
 * Contract Types Services
 */
class ContractTypesServices
{
    private $connectionManager;

    function __construct()
    {
        $this->connectionManager = new ConnectionManager();
    }
    
	/**
     * Count all contract types
     * 
     * @return int Number of contract types
     */
    public function countAll(): int
    {
        $count = 0;
        $join = '';

        $sql = "SELECT COUNT(*) AS count FROM contract_types ct WHERE ct.etat = ?";

        try {
            $query = $this->connectionManager->getConnection()->prepare($sql);

            $query->execute([1]);

            $result = $query->fetch(\PDO::FETCH_ASSOC);

            $count = (int)$result['count'];
        } catch (\PDOException $e) {
            throw new \Exception("SQL Exception: " . $e->getMessage(), 1);
        }
        
        return $count;
    }

    /**
     * Get all contract types
     * 
     * @return array<ContractType> List of contract types of empty array
     */
    public function getAll()
    {
        $result = [];
        $contract_types = [];

        $sql = "SELECT * FROM contract_types WHERE etat = ?";

        try {
            $query = $this->connectionManager->getConnection()->prepare($sql);

            $query->execute([1]);

            $result = $query->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new \Exception("SQL Exception: " . $e->getMessage(), $e->getCode());
        }

        if (empty($result)) {
			return [];
		}

		foreach ($result as $row) {
            $contractType = new ContractType();
            $contractType->setId($row['id']);
            $contractType->setName($row['name']);
            $contractType->setDescription($row['description']);
            $contractType->setCreated($row['created']);
            $contractType->setEtat($row['etat']);

            $contract_types[] = $contractType;
        }

        return $contract_types;
    }

    /**
     * Retrieve specific contract type
     * 
     * @param int $id Contract type id
     * @return ContractType|null Return the contract type or null if not found
     */
    public function get(int $id): ?ContractType
    {
        $sql = "SELECT * FROM contract_types WHERE etat = :etat AND id = :id";

        try {
            $query = $this->connectionManager->getConnection()->prepare($sql);
            $query->bindParam(':id', $id, \PDO::PARAM_INT);
            $query->bindValue(':etat', 1, \PDO::PARAM_BOOL);

            $query->execute();

            $result = $query->fetch(\PDO::FETCH_ASSOC);

            if (empty($result)) {
                return null;
            }

            $contractType = new ContractType();
            $contractType->setId($result['id']);
            $contractType->setName($result['name']);
            $contractType->setDescription($result['description']);
            $contractType->setCreated($result['created']);
            $contractType->setEtat($result['etat']);

            return $contractType;
        } catch (\PDOException $e) {
            throw new \Exception("SQL Exception: " . $e->getMessage(), $e->getCode());
        }
    }

    /**
     * Add a contract type
     * 
     * @param array $data Contract type data
     * 
     */
    public function add(array|ContractType $contractType): ContractType|bool
    {
        if (is_array($contractType)) {
            $name = htmlentities($contractType['name']);
            $description = htmlentities($contractType['description']);
            if (empty($description)) {
                $description = null;
            }

            $contractType = new ContractType();
            $contractType->setName($name);
            $contractType->setDescription($description);
        } elseif (!$contractType instanceof ContractType) {
            throw new \Exception("Invalid parameter. Type of parameter passed must be array or ContractType");
        }

        // Check if the contract type already exists
        if ($this->checkContractType($contractType)) {
            Flash::error("Le nom du type de contrat est déjà utilisé.");

            Session::write('__formdata__', json_encode($_POST));

            return false;
        }

        $contractType->setCreated(date('Y-m-d H:i:s'));
        $contractType->setEtat(true);

        $errors = $contractType->validation();

        if (!empty($errors)) {
            foreach ($errors as $error) {
                Flash::error($error);
            }

            Session::write('__formdata__', json_encode($_POST));

            return false;
        }

        $sql = "INSERT INTO contract_types(name, description, created, etat) VALUES(?,?,?,?)";

        try {
            $this->connectionManager->getConnection()->beginTransaction();

            $query = $this->connectionManager->getConnection()->prepare($sql);
            $query->bindParam(1, $contractType->getName(), \PDO::PARAM_STR);
            $query->bindParam(2, $contractType->getDescription(), \PDO::PARAM_STR);
            $query->bindParam(3, $contractType->getCreated(), \PDO::PARAM_STR);
            $query->bindParam(4, $contractType->getEtat(), \PDO::PARAM_BOOL);
            $query->execute();

            $id = (int)$this->connectionManager->getConnection()->lastInsertId();
            $contractType->setId($id);
            $this->connectionManager->getConnection()->commit();

            return $contractType;
        } catch(\PDOException $e) {
            $this->connectionManager->getConnection()->rollBack();

            throw new \Exception("SQL Exception: " . $e->getMessage(), $e->getCode());
        }
    }

    /**
     * Update a contract type
     * 
     * @param array|ContractType $data Contract type data
     * @return bool Returns true if contract type was updated successfully, false otherwise.
     */
    public function update(array|ContractType $contract_type): bool
    {
        if (is_array($contract_type)) {
            $name = htmlentities($contract_type['name']);
            $id = (int)$contract_type['id'];
            $description = htmlentities($contract_type['description']);
            if (empty($description)) {
                $description = null;
            }

            $contract_type = new ContractType();
            $contract_type->setID($id);
            $contract_type->setName($name);
            $contract_type->setDescription($description);
        } elseif (!$contract_type instanceof ContractType) {
            throw new \Exception("Invalid parameter. Type of parameter passed must be array or ContractType");
        }

        $contractType = $this->get($contract_type->getId());

        if (empty($contractType)) {
            throw new \Exception("Record non trouvé dans les types de contrat.", 1);
        }

        $contractType->setName($contract_type->getName());
        $contractType->setDescription($contract_type->getDescription());

        // Check if the contract type already exists
        if ($this->checkContractType($contractType)) {
            Flash::error("Le nom du type de contrat est déjà utilisé.");

            Session::write('__formdata__', json_encode($_POST));

            return false;
        }

        $errors = $contractType->validation();

        if (!empty($errors)) {
            foreach ($errors as $error) {
                Flash::error($error);
            }
            
            Session::write('__formdata__', json_encode($_POST));

            return false;
        }

        $sql = "UPDATE contract_types SET name = :name, description = :description WHERE id = :id";

        try {
            $this->connectionManager->getConnection()->beginTransaction();

            $query = $this->connectionManager->getConnection()->prepare($sql);
            $query->bindValue(':name', $contractType->getName(), \PDO::PARAM_STR);
            $query->bindValue(':description', $contractType->getDescription(), \PDO::PARAM_STR);
            $query->bindValue(':id', $contractType->getId(), \PDO::PARAM_INT);
            $updated = $query->execute();

            $this->connectionManager->getConnection()->commit();

            return $updated;
        } catch(\PDOException $e) {
            $this->connectionManager->getConnection()->rollBack();

            throw new \Exception("SQL Exception: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    /**
     * Delete contract type
     *
     * @param int $id
     * @return bool Returns true if contract type has been deleted successfully, false otherwise.
     */
    public function delete(int $id): bool
    {
        // First check if the contract type exists
        $contractType = $this->get($id);
        if (empty($contractType)) {
            throw new \Exception("Record non trouvé dans les types de contrat.", 1);
        }

        $sql = "UPDATE contract_types SET etat = :etat WHERE id = :id";
        
        try{
            $query = $this->connectionManager->getConnection()->prepare($sql);
            $query->bindValue(':etat',0, \PDO::PARAM_BOOL);
            $query->bindValue(':id',$id, \PDO::PARAM_INT);

            $deleted = $query->execute();

            return $deleted;
        } catch (\PDOException $e) {
            throw new \Exception("SQL Exception: " . $e->getMessage(), $e->getCode());
        }
    }

    /**
     * Check if contract type already exists
     *
     * @param ContractType $contractType Contract type to check
     * @return bool Returns true if contract type already exists, false otherwise.
     */
    public function checkContractType(ContractType $contractType): bool 
    {
        if (is_null($contractType->getId())) {
            $sql = "SELECT * FROM contract_types WHERE etat = :etat AND name = :name limit 0,1";
        } else {
            $sql = "SELECT * FROM contract_types WHERE etat = :etat AND name = :name AND id != :id limit 0,1";
        }

        try {
            $query = $this->connectionManager->getConnection()->prepare($sql);
            $query->bindValue(':etat', 1, \PDO::PARAM_BOOL);
            $query->bindValue(':name', $contractType->getName(), \PDO::PARAM_STR);
            if (!is_null($contractType->getId())) {
                $query->bindValue(':id', $contractType->getId(), \PDO::PARAM_INT);
            }
            $query->execute();

            $result = $query->fetchAll(\PDO::FETCH_ASSOC);
            if(empty($result)) {
                return false;
            }

            return true;
        } catch (\PDOException $e) {
            throw new \Exception("SQL Exception: " . $e->getMessage(), $e->getCode());
        }
    }
}