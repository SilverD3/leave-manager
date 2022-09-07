<?php
declare(strict_types=1);

namespace App\Service;

require_once dirname(dirname(__DIR__)) . DS . DS . 'autoload.php';

use Core\Database\ConnectionManager;
use App\Entity\ContractModel;
use App\Entity\ContractType;
use Core\FlashMessages\Flash;
use Core\Utils\Session;

/**
 * Contract Models Services
 */
class ContractModelsServices
{
    private $connectionManager;

    function __construct()
    {
        $this->connectionManager = new ConnectionManager();
    }
    
	/**
     * Count all contract models
     * 
     * @return int Number of contract models
     */
    public function countAll(): int
    {
        $count = 0;
        $join = '';

        $sql = "SELECT COUNT(*) AS count FROM contract_models cm WHERE cm.etat = ?";

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
     * Get all contract models
     * 
     * @param bool $joinTypes whether to join contract types or not
     * @return array<ContractModel> List of contract models of empty array
     */
    public function getAll(bool $joinTypes = false)
    {
        $result = [];
        $contract_models = [];

        $select = "SELECT cm.id AS ContractModel_id, cm.name AS ContractModel_name, cm.contract_type_id AS ContractModel_contract_type_id, cm.content AS ContractModel_content, "
                ."cm.is_current AS ContractModel_is_current, cm.status AS ContractModel_status, cm.created AS ContractModel_created, cm.modified AS ContractModel_modified, cm.etat AS ContractModel_etat ";
        
        if ($joinTypes) {
            $select .= ", ct.id AS ContractType_id, ct.name AS ContractType_name, ct.description AS ContractType_description, ct.created AS ContractType_created, "
                    ."ct.etat AS ContractType_etat ";
        }
        
        $from = "FROM contract_models cm ";
        
        if ($joinTypes) {
            $join = "INNER JOIN contract_types ct ON ct.id = cm.contract_type_id ";
        } else {
            $join = '';
        }

        $sql = $select . $from . $join . "WHERE cm.etat = ?";

        try {
            $query = $this->connectionManager->getConnection()->prepare($sql);

            $query->execute([1]);

            $result = $query->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new \Exception("SQL Exception: " . $e->getMessage(), (int)$e->getCode());
        }

        if (empty($result)) {
			return [];
		}

        foreach ($result as $row) {
            $model = new ContractModel();
            $model->setId($row['ContractModel_id']);
            $model->setName($row['ContractModel_name']);
            $model->setContent($row['ContractModel_content']);
            $model->setIsCurrent($row['ContractModel_is_current']);
            $model->setContractTypeId($row['ContractModel_contract_type_id']);
            $model->setStatus($row['ContractModel_status']);
            $model->setCreated($row['ContractModel_created']);
            $model->setModified($row['ContractModel_modified']);
            $model->setEtat($row['ContractModel_etat']);

            if ($joinTypes) {
                $contractType = new ContractType();
                $contractType->setId($row['ContractType_id']);
                $contractType->setName($row['ContractType_name']);
                $contractType->setDescription($row['ContractType_description']);
                $contractType->setCreated($row['ContractType_created']);
                $contractType->setEtat($row['ContractType_etat']);

                $model->setContractType($contractType);
            }

            $contract_models[] = $model;
        }

        return $contract_models;
    }

    /**
     * Retrieve specific contract model
     * 
     * @param int $id Contract model id
     * @return ContractModel|null Return the contract model or null if not found
     */
    public function get(int $id): ?ContractModel
    {
        $sql = "SELECT cm.id AS ContractModel_id, cm.name AS ContractModel_name, cm.contract_type_id AS ContractModel_contract_type_id, cm.content AS ContractModel_content, "
                ."cm.is_current AS ContractModel_is_current, cm.status AS ContractModel_status, cm.created AS ContractModel_created, cm.modified AS ContractModel_modified, cm.etat AS ContractModel_etat, "
                ."ct.id AS ContractType_id, ct.name AS ContractType_name, ct.description AS ContractType_description, ct.created AS ContractType_created, "
                ."ct.etat AS ContractType_etat "
                ."FROM contract_models cm "
                ."INNER JOIN contract_types ct ON ct.id = cm.contract_type_id "
                ."WHERE cm.etat = :etat AND cm.id = :id ";

        try {
            $query = $this->connectionManager->getConnection()->prepare($sql);
            $query->bindParam(':id', $id, \PDO::PARAM_INT);
            $query->bindValue(':etat', 1, \PDO::PARAM_BOOL);

            $query->execute();

            $result = $query->fetch(\PDO::FETCH_ASSOC);

            if (empty($result)) {
                return null;
            }

            $model = new ContractModel();
            $model->setId($result['ContractModel_id']);
            $model->setName($result['ContractModel_name']);
            $model->setContent($result['ContractModel_content']);
            $model->setIsCurrent($result['ContractModel_is_current']);
            $model->setContractTypeId($result['ContractModel_contract_type_id']);
            $model->setStatus($result['ContractModel_status']);
            $model->setCreated($result['ContractModel_created']);
            $model->setModified($result['ContractModel_modified']);
            $model->setEtat($result['ContractModel_etat']);

            $contractType = new ContractType();
            $contractType->setId($result['ContractType_id']);
            $contractType->setName($result['ContractType_name']);
            $contractType->setDescription($result['ContractType_description']);
            $contractType->setCreated($result['ContractType_created']);
            $contractType->setEtat($result['ContractType_etat']);

            $model->setContractType($contractType);

            return $model;
        } catch (\PDOException $e) {
            throw new \Exception("SQL Exception: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    /**
     * Retrieve specific contract model
     * 
     * @param int $type_id Contract type id
     * @return ContractModel|null Return the contract model or null if not found
     */
    public function getCurrent(int $type_id): ?ContractModel
    {
        $sql = "SELECT cm.id AS ContractModel_id, cm.name AS ContractModel_name, cm.contract_type_id AS ContractModel_contract_type_id, cm.content AS ContractModel_content, "
                ."cm.is_current AS ContractModel_is_current, cm.status AS ContractModel_status, cm.created AS ContractModel_created, cm.modified AS ContractModel_modified, cm.etat AS ContractModel_etat, "
                ."ct.id AS ContractType_id, ct.name AS ContractType_name, ct.description AS ContractType_description, ct.created AS ContractType_created, "
                ."ct.etat AS ContractType_etat "
                ."FROM contract_models cm "
                ."INNER JOIN contract_types ct ON ct.id = cm.contract_type_id "
                ."WHERE cm.etat = :etat AND cm.is_current = :current AND cm.contract_type_id = :contract_type_id ";

        try {
            $query = $this->connectionManager->getConnection()->prepare($sql);
            $query->bindValue(':etat', 1, \PDO::PARAM_BOOL);
            $query->bindValue(':current', 1, \PDO::PARAM_BOOL);
            $query->bindParam(':contract_type_id', $type_id, \PDO::PARAM_INT);

            $query->execute();

            $result = $query->fetch(\PDO::FETCH_ASSOC);

            if (empty($result)) {
                $sql = "SELECT cm.id AS ContractModel_id, cm.name AS ContractModel_name, cm.contract_type_id AS ContractModel_contract_type_id, cm.content AS ContractModel_content, "
                        ."cm.is_current AS ContractModel_is_current, cm.status AS ContractModel_status, cm.created AS ContractModel_created, cm.modified AS ContractModel_modified, cm.etat AS ContractModel_etat, "
                        ."ct.id AS ContractType_id, ct.name AS ContractType_name, ct.description AS ContractType_description, ct.created AS ContractType_created, "
                        ."ct.etat AS ContractType_etat "
                        ."FROM contract_models cm "
                        ."INNER JOIN contract_types ct ON ct.id = cm.contract_type_id "
                        ."WHERE cm.etat = :etat AND cm.contract_type_id = :contract_type_id ";
                
                $query = $this->connectionManager->getConnection()->prepare($sql);
                $query->bindValue(':etat', 1, \PDO::PARAM_BOOL);
                $query->bindParam(':contract_type_id', $type_id, \PDO::PARAM_INT);
    
                $query->execute();
    
                $result = $query->fetch(\PDO::FETCH_ASSOC);

                if (empty($result)) {
                    return null;
                }
            }

            $model = new ContractModel();
            $model->setId($result['ContractModel_id']);
            $model->setName($result['ContractModel_name']);
            $model->setContent($result['ContractModel_content']);
            $model->setIsCurrent($result['ContractModel_is_current']);
            $model->setContractTypeId($result['ContractModel_contract_type_id']);
            $model->setStatus($result['ContractModel_status']);
            $model->setCreated($result['ContractModel_created']);
            $model->setModified($result['ContractModel_modified']);
            $model->setEtat($result['ContractModel_etat']);

            $contractType = new ContractType();
            $contractType->setId($result['ContractType_id']);
            $contractType->setName($result['ContractType_name']);
            $contractType->setDescription($result['ContractType_description']);
            $contractType->setCreated($result['ContractType_created']);
            $contractType->setEtat($result['ContractType_etat']);

            $model->setContractType($contractType);

            return $model;
        } catch (\PDOException $e) {
            throw new \Exception("SQL Exception: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    /**
     * Add contract model
     *
     * @param array|ContractModel $contractModel Model to add
     * @return boolean|integer Return the saved model id if success, false otherwise.
     */
    public function add(array|ContractModel $contractModel): bool|int
    {
        if (is_array($contractModel)) {
            $contractModel = $this->toEntity($contractModel);
        }

        $contractModel->setStatus('active');
        $contractModel->setCreated(date('Y-m-d H:i:s'));
        $contractModel->setModified(null);
        $contractModel->setEtat(true);

        if ($this->checkContractModel($contractModel)) {
            Flash::error("Un modèle de contrat avec le même nom existe déjà.");

            Session::write('__formdata__', json_encode($_POST));

            return false;
        }

        $errors = $contractModel->validation();
		if (!empty($errors)) {
			foreach ($errors as $error) {
				Flash::error($error);
			}

			Session::write('__formdata__', json_encode($_POST));

			return false;
		}

        $sql = "INSERT INTO contract_models(contract_type_id, name, content, status, created, modified, etat) VALUES(?,?,?,?,?,?,?)";

        try {

			$this->connectionManager->getConnection()->beginTransaction();

			$query = $this->connectionManager->getConnection()->prepare($sql);

            $query->bindValue(1, $contractModel->getContractTypeId(), \PDO::PARAM_INT);
            $query->bindValue(2, $contractModel->getName(), \PDO::PARAM_STR);
            $query->bindValue(3, $contractModel->getContent(), \PDO::PARAM_STR);
            $query->bindValue(4, $contractModel->getStatus(), \PDO::PARAM_STR);
            $query->bindValue(5, $contractModel->getCreated(), \PDO::PARAM_STR);
            $query->bindValue(6, $contractModel->getModified(), \PDO::PARAM_STR);
            $query->bindValue(7, $contractModel->getEtat(), \PDO::PARAM_BOOL);

            $query->execute();

            $model_id = (int)$this->connectionManager->getConnection()->lastInsertId();

            if ($contractModel->getIsCurrent()) {
                $this->setCurrentModel($model_id, (int)$contractModel->getContractTypeId());
            }

            $this->connectionManager->getConnection()->commit();

            return $model_id;
        } catch (\PDOException $e) {
            $this->connectionManager->getConnection()->rollBack();

			throw new \Exception("SQL Exception: " . $e->getMessage(), 1);
		}
    }

    /**
     * Update existed contract model
     *
     * @param array|ContractModel $contractModel Contract Model to update
     * @return bool Returns true if the model has been updated, false otherwise.
     */
    public function update(array|ContractModel $contractModel): bool
    {
        if (is_array($contractModel)) {
            $contractModel = $this->toEntity($contractModel);
        }

        
        $existedModel = $this->get($contractModel->getId());
        
		if (empty($existedModel)) {
            Flash::error("Aucun modèle de contrat trouvé avec l'id " . $contractModel->getId());
            
			return false;
		}
        
        $contractModel->setModified(date('Y-m-d H:i:s'));
        $contractModel->setContractTypeId($existedModel->getContractTypeId());
        
        if ($this->checkContractModel($contractModel)) {
            Flash::error("Un modèle de contrat avec le même nom existe déjà.");

            Session::write('__formdata__', json_encode($_POST));

            return false;
        }

        $errors = $contractModel->validation();
		if (!empty($errors)) {
			foreach ($errors as $error) {
				Flash::error($error);
			}

			Session::write('__formdata__', json_encode($_POST));

			return false;
		}

        $sql = "UPDATE contract_models SET name = :name, content = :content, modified = :modified WHERE id = :id";

        try {

			$this->connectionManager->getConnection()->beginTransaction();

			$query = $this->connectionManager->getConnection()->prepare($sql);

            $query->bindValue(':name', $contractModel->getName(), \PDO::PARAM_STR);
            $query->bindValue(':content', $contractModel->getContent(), \PDO::PARAM_STR);
            $query->bindValue(':modified', $contractModel->getModified(), \PDO::PARAM_STR);
            $query->bindValue(':id', $contractModel->getId(), \PDO::PARAM_INT);

            $updated = $query->execute();

            if ($contractModel->getIsCurrent() && ($contractModel->getIsCurrent() != $existedModel->getIsCurrent())) {
                $this->setCurrentModel((int)$contractModel->getId(), (int)$contractModel->getContractTypeId());
            }

            $this->connectionManager->getConnection()->commit();

            return $updated;
        } catch (\PDOException $e) {
            $this->connectionManager->getConnection()->rollBack();

			throw new \Exception("SQL Exception: " . $e->getMessage(), 1);
		}
    }

    /**
     * Set contract model as default
     *
     * @param integer $model_id Contract Model id
     * @param integer $contract_type_id Contract type id
     * @return bool Returns true if contract model has been set as default, false otherwise.
     */
    public function setCurrentModel(int $model_id, int $contract_type_id): bool
    {
        $sql1 = "UPDATE contract_models SET is_current = ? WHERE contract_type_id = ? AND is_current = ?";
        $sql2 = "UPDATE contract_models SET is_current = ? WHERE contract_type_id = ? AND id = ?";

        try {
            $query1 = $this->connectionManager->getConnection()->prepare($sql1);
            $query2 = $this->connectionManager->getConnection()->prepare($sql2);

            $query1->bindValue(1, false, \PDO::PARAM_BOOL);
            $query1->bindValue(2, $contract_type_id, \PDO::PARAM_INT);
            $query1->bindValue(3, true, \PDO::PARAM_BOOL);
            
            $query2->bindValue(1, true, \PDO::PARAM_BOOL);
            $query2->bindValue(2, $contract_type_id, \PDO::PARAM_INT);
            $query2->bindValue(3, $model_id, \PDO::PARAM_INT);

            $updated1 = $query1->execute();
            $updated2 = $query2->execute();

            return $updated1 && $updated2;
        } catch (\PDOException $e) {
			throw new \Exception("SQL Exception: " . $e->getMessage(), 1);
		}
    }

    /**
     * Delete contract model
     *
     * @param int $id
     * @return bool Returns true if contract model has been deleted successfully, false otherwise.
     */
    public function delete(int $id): bool
    {
        // First check if the contract model exists
        $contractModel = $this->get($id);
        if (empty($contractModel)) {
            throw new \Exception("Record non trouvé dans les modèles de contrat.", 1);
        }

        $sql = "UPDATE contract_models SET etat = :etat, status = :status WHERE id = :id";
        
        try{
            $query = $this->connectionManager->getConnection()->prepare($sql);
            $query->bindValue(':etat',0, \PDO::PARAM_BOOL);
            $query->bindValue(':status','deleted', \PDO::PARAM_STR);
            $query->bindValue(':id',$id, \PDO::PARAM_INT);

            $deleted = $query->execute();

            return $deleted;
        } catch (\PDOException $e) {
            throw new \Exception("SQL Exception: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    /**
     * Check if contract model already exists
     *
     * @param ContractModel $contractModel Contract model to check
     * @return bool Returns true if contract model already exists, false otherwise.
     */
    public function checkContractModel(ContractModel $contractModel): bool 
    {
        if (is_null($contractModel->getId())) {
            $sql = "SELECT * FROM contract_models WHERE etat = :etat AND contract_type_id = :contract_type_id AND name = :name limit 0,1";
        } else {
            $sql = "SELECT * FROM contract_models WHERE etat = :etat AND contract_type_id = :contract_type_id AND name = :name AND id != :id limit 0,1";
        }

        try {
            $query = $this->connectionManager->getConnection()->prepare($sql);
            $query->bindValue(':etat', 1, \PDO::PARAM_BOOL);
            $query->bindValue(':contract_type_id', $contractModel->getContractTypeId(), \PDO::PARAM_INT);
            $query->bindValue(':name', $contractModel->getName(), \PDO::PARAM_STR);
            if (!is_null($contractModel->getId())) {
                $query->bindValue(':id', $contractModel->getId(), \PDO::PARAM_INT);
            }
            $query->execute();

            $result = $query->fetchAll(\PDO::FETCH_ASSOC);
            
            return !empty($result);
        } catch (\PDOException $e) {
            throw new \Exception("SQL Exception: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    /**
     * Parse array to ContractModel object
     *
     * @param array $data Data to parse
     * @return ContractModel|null Returns parsed object or null
     */
    public function toEntity(array $data): ?ContractModel
    {
        $id = !empty($data['id']) ? (int)$data['id'] : null;
        $contract_type_id = !empty($data['contract_type_id']) ? $data['contract_type_id'] : null;
        $name = !empty($data['name']) ? $data['name'] : null;
        $content = !empty($data['content']) ? $data['content'] : null;
        $is_current = !empty($data['is_current']) ? $data['is_current'] : null;
        $status = !empty($data['status']) ? $data['status'] : null;
        $created = !empty($data['created']) ? $data['created'] : null;
        $modified = !empty($data['modified']) ? $data['modified'] : null;
        $etat = !empty($data['etat']) ? $data['etat'] : null;

        $model = new ContractModel();
        $model->setId($id);
        $model->setContractTypeId($contract_type_id);
        $model->setName($name);
        $model->setIsCurrent($is_current);
        $model->setContent($content);
        $model->setStatus($status);
        $model->setCreated($created);
        $model->setModified($modified);
        $model->setEtat($etat);

        return $model;
    }
}