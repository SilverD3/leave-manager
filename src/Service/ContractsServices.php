<?php
declare(strict_types=1);

namespace App\Service;

require_once dirname(dirname(__DIR__)) . DS . DS . 'autoload.php';

use Core\Database\ConnectionManager;
use App\Entity\Contract;
use App\Entity\ContractType;
use App\Entity\Employee;
use Core\FlashMessages\Flash;
use Core\Utils\Session;

/**
 * Contract Services
 */
class ContractsServices
{
	private $connectionManager;

    function __construct()
    {
        $this->connectionManager = new ConnectionManager();
    }

    /**
     * Get All contracts
     *
     * @param string $status
     * @param bool $joinType Wheter to join contract type or not
     * @param bool $joinEmployee Wheter to join employees or not
     * @param int|null $employee_id Employee id
     * @return Contract[] Contracts list
     */
    public function getAll(string $status = 'all', bool $joinType = false, bool $joinEmployee = false, $employee_id = null)
    {
        $results = [];
        $contracts = [];
        
        $select = "SELECT c.id AS Contract_id, c.title AS Contract_title, c.employee_id AS Contract_employee_id, c.start_date AS Contract_start_date, "
                    ."c.end_date AS Contract_end_date, c.job_object AS Contract_job_object, c.job_description AS Contract_job_description, c.job_salary AS Contract_job_salary, "
                    ."c.hourly_rate AS Contract_hourly_rate, c.pdf AS Contract_pdf, c.created AS Contract_created, c.contract_type_id AS Contract_contract_type_id, "
                    ."c.modified AS Contract_modified, c.status AS Contract_status, c.etat AS Contract_etat ";
                    
        $from = " FROM contracts c";
        $join = "";
        $where = " WHERE c.etat = :etat ";
        
        if ($joinType) {
            $join = " INNER JOIN contract_types ct ON ct.id = c.contract_type_id ";
            $select .= ", ct.id AS ContractType_id, ct.name AS ContractType_name, ct.description AS ContractType_description, ct.created AS ContractType_created, "
                    ."ct.etat AS ContractType_etat ";
        }
        
        if ($joinEmployee) {
            $select .= " , e.id AS Employee_id, e.first_name AS Employee_first_name, e.last_name AS Employee_last_name, e.email AS Employee_email ";
            $join .= " INNER JOIN employees e ON e.id = c.employee_id ";
        }

        if ($status != 'all') {
            $where .= " AND c.status = :status ";
        }

        if (!is_null($employee_id)) {
            $where .= " AND c.employee_id = :employee_id ";
        }

        $sql = $select . $from . $join . $where;

        try {
            $query = $this->connectionManager->getConnection()->prepare($sql);

            $query->bindValue(':etat', 1, \PDO::PARAM_BOOL);

            if ($status != 'all') {
                $query->bindValue(':status', $status, \PDO::PARAM_STR);
            }

            if (!is_null($employee_id)) {
                $query->bindValue(':employee_id', $employee_id, \PDO::PARAM_INT);
            }
            
            $query->execute();

            $results = $query->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new \Exception("SQL Exception: " . $e->getMessage(), (int)$e->getCode());
        }

        if (empty($results)) {
			return [];
		}

        foreach ($results as $row) {
            $contract = new Contract();
            $contract->setId($row['Contract_id']);
            $contract->setEmployeeId($row['Contract_employee_id']);
            $contract->setContractTypeId($row['Contract_contract_type_id']);
            $contract->setTitle($row['Contract_title']);
            $contract->setStartDate($row['Contract_start_date']);
            $contract->setEndDate($row['Contract_end_date']);
            $contract->setJobObject($row['Contract_job_object']);
            $contract->setJobDescription($row['Contract_job_description']);
            $contract->setJobSalary($row['Contract_job_salary']);
            $contract->setHourlyRate($row['Contract_hourly_rate']);
            $contract->setPdf($row['Contract_pdf']);
            $contract->setCreated($row['Contract_created']);
            $contract->setModified($row['Contract_modified']);
            $contract->setStatus($row['Contract_status']);
            $contract->setEtat((bool)$row['Contract_etat']);

            if ($joinType) {
                $contractType = new ContractType();
                $contractType->setId($row['ContractType_id']);
                $contractType->setName($row['ContractType_name']);
                $contractType->setDescription($row['ContractType_description']);
                $contractType->setCreated($row['ContractType_created']);
                $contractType->setEtat($row['ContractType_etat']);

                $contract->setContractType($contractType);
            }

            if ($joinEmployee) {
                $employee = new Employee();
                $employee->setId($row['Employee_id']);
                $employee->setFirstName($row['Employee_first_name']);
                $employee->setLastName($row['Employee_last_name']);
                $employee->setEmail($row['Employee_email']);   

                $contract->setEmployee($employee);        
            }

            $contracts[] = $contract;
        }

        return $contracts;
    }

    /**
     * Get All expired contracts
     *
     * @param bool $joinType Wheter to join contract type or not
     * @param bool $joinEmployee Wheter to join employees or not
     * @return Contract[] Contracts list
     */
    public function getExpired(bool $joinType = true, bool $joinEmployee = true)
    {
        $select = "SELECT c.id AS Contract_id, c.title AS Contract_title, c.employee_id AS Contract_employee_id, c.start_date AS Contract_start_date, "
                    ."c.end_date AS Contract_end_date, c.job_object AS Contract_job_object, c.job_description AS Contract_job_description, c.job_salary AS Contract_job_salary, "
                    ."c.hourly_rate AS Contract_hourly_rate, c.pdf AS Contract_pdf, c.created AS Contract_created, c.contract_type_id AS Contract_contract_type_id, "
                    ."c.modified AS Contract_modified, c.status AS Contract_status, c.etat AS Contract_etat ";
                    
        $from = " FROM contracts c";
        $join = "";
        $where = " WHERE c.etat = :etat AND c.end_date < :end_date and c.status = :status";
        
        if ($joinType) {
            $join = " INNER JOIN contract_types ct ON ct.id = c.contract_type_id ";
            $select .= ", ct.id AS ContractType_id, ct.name AS ContractType_name, ct.description AS ContractType_description, ct.created AS ContractType_created, "
                    ."ct.etat AS ContractType_etat ";
        }
        
        if ($joinEmployee) {
            $select .= " , e.id AS Employee_id, e.first_name AS Employee_first_name, e.last_name AS Employee_last_name, e.email AS Employee_email ";
            $join .= " INNER JOIN employees e ON e.id = c.employee_id ";
        }

        $sql = $select . $from . $join . $where;

        try {
            $query = $this->connectionManager->getConnection()->prepare($sql);

            $query->bindValue(':etat', 1, \PDO::PARAM_BOOL);
            $query->bindValue(':status', 'active', \PDO::PARAM_STR);
            $query->bindValue(':end_date', date('Y-m-d'), \PDO::PARAM_STR);
            
            $query->execute();

            $results = $query->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new \Exception("SQL Exception: " . $e->getMessage(), (int)$e->getCode());
        }

        if (empty($results)) {
			return [];
		}

        foreach ($results as $row) {
            $contract = new Contract();
            $contract->setId($row['Contract_id']);
            $contract->setEmployeeId($row['Contract_employee_id']);
            $contract->setContractTypeId($row['Contract_contract_type_id']);
            $contract->setTitle($row['Contract_title']);
            $contract->setStartDate($row['Contract_start_date']);
            $contract->setEndDate($row['Contract_end_date']);
            $contract->setJobObject($row['Contract_job_object']);
            $contract->setJobDescription($row['Contract_job_description']);
            $contract->setJobSalary($row['Contract_job_salary']);
            $contract->setHourlyRate($row['Contract_hourly_rate']);
            $contract->setPdf($row['Contract_pdf']);
            $contract->setCreated($row['Contract_created']);
            $contract->setModified($row['Contract_modified']);
            $contract->setStatus($row['Contract_status']);
            $contract->setEtat((bool)$row['Contract_etat']);

            if ($joinType) {
                $contractType = new ContractType();
                $contractType->setId($row['ContractType_id']);
                $contractType->setName($row['ContractType_name']);
                $contractType->setDescription($row['ContractType_description']);
                $contractType->setCreated($row['ContractType_created']);
                $contractType->setEtat($row['ContractType_etat']);

                $contract->setContractType($contractType);
            }

            if ($joinEmployee) {
                $employee = new Employee();
                $employee->setId($row['Employee_id']);
                $employee->setFirstName($row['Employee_first_name']);
                $employee->setLastName($row['Employee_last_name']);
                $employee->setEmail($row['Employee_email']);   

                $contract->setEmployee($employee);        
            }

            $contracts[] = $contract;
        }

        return $contracts;
    }

    /**
     * Count all expired contract
     *
     * @return int Returns the number of expired contract
     */
    public function countExpired(): ?int
    {
        $count = 0;

        $sql = "SELECT COUNT(*) AS count FROM contracts c WHERE c.etat = :etat AND c.status = :status AND c.end_date < :end_date";

        try {
            $query = $this->connectionManager->getConnection()->prepare($sql);

            $query->bindValue(':etat', 1, \PDO::PARAM_BOOL);
            $query->bindValue(':status', 'active', \PDO::PARAM_STR);
            $query->bindValue(':end_date', date('Y-m-d'), \PDO::PARAM_STR);

            $query->execute();

            $result = $query->fetch(\PDO::FETCH_ASSOC);

            $count = (int)$result['count'];
        } catch (\PDOException $e) {
            throw new \Exception("SQL Exception: " . $e->getMessage(), 1);
        }
        
        return $count;
    }

    /**
     * Count all contracts
     *
     * @param string $status Status to consider
     * @param int|null $employee_id Employee id
     * @return int Number of contracts
     */
    public function countAll(string $status = 'all', $employee_id = null): int
    {
        $count = 0;

        $sql = "SELECT COUNT(*) AS count FROM contracts c WHERE c.etat = :etat";
        if ($status != 'all') {
            $sql .= " AND c.status = :status";
        }

        if ($employee_id != null) {
            $sql .= " AND c.employee_id = :employee_id";
        }

        try {
            $query = $this->connectionManager->getConnection()->prepare($sql);

            $query->bindValue(':etat', 1, \PDO::PARAM_BOOL);
            if ($status != 'all') {
                $query->bindValue(':status', $status, \PDO::PARAM_STR);
            }
            if ($employee_id != null) {
                $query->bindValue(':employee_id', $employee_id, \PDO::PARAM_INT);
            }

            $query->execute();

            $result = $query->fetch(\PDO::FETCH_ASSOC);

            $count = (int)$result['count'];
        } catch (\PDOException $e) {
            throw new \Exception("SQL Exception: " . $e->getMessage(), 1);
        }
        
        return $count;
    }

    /**
     * Get contract by id
     *
     * @param int $id Contract id
     * @param boolean $joinType Wheter to join contract type or not
     * @param boolean $joinEmployee Wheter to join employee or not
     * @return Contract|null Returns contract if found, null if not found.
     */
    public function get($id, bool $joinType = false, bool $joinEmployee = false): ?Contract
    {
        $select = "SELECT c.id AS Contract_id, c.title AS Contract_title, c.employee_id AS Contract_employee_id, c.start_date AS Contract_start_date, "
                    ."c.end_date AS Contract_end_date, c.job_object AS Contract_job_object, c.job_description AS Contract_job_description, c.job_salary AS Contract_job_salary, "
                    ."c.hourly_rate AS Contract_hourly_rate, c.pdf AS Contract_pdf, c.created AS Contract_created, c.contract_type_id AS Contract_contract_type_id, "
                    ."c.modified AS Contract_modified, c.status AS Contract_status, c.etat AS Contract_etat ";
                    
        $from = " FROM contracts c";
        $join = "";
        $where = " WHERE c.etat = :etat AND c.id = :id ";
        
        if ($joinType) {
            $join = " INNER JOIN contract_types ct ON ct.id = c.contract_type_id ";
            $select .= ", ct.id AS ContractType_id, ct.name AS ContractType_name, ct.description AS ContractType_description, ct.created AS ContractType_created, "
                    ."ct.etat AS ContractType_etat ";
        }
        
        if ($joinEmployee) {
            $select .= " , e.id AS Employee_id, e.first_name AS Employee_first_name, e.last_name AS Employee_last_name, e.email AS Employee_email ";
            $join .= " INNER JOIN employees e ON e.id = c.employee_id ";
        }

        $sql = $select . $from . $join . $where;

        try {
            $query = $this->connectionManager->getConnection()->prepare($sql);

            $query->bindValue(':etat', 1, \PDO::PARAM_BOOL);

            $query->bindValue(':id', $id);
            
            $query->execute();

            $results = $query->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new \Exception("SQL Exception: " . $e->getMessage(), (int)$e->getCode());
        }

        if (empty($results)) {
			return null;
		}

        $contract = new Contract();
        $contract->setId($results['Contract_id']);
        $contract->setEmployeeId($results['Contract_employee_id']);
        $contract->setContractTypeId($results['Contract_contract_type_id']);
        $contract->setTitle($results['Contract_title']);
        $contract->setStartDate($results['Contract_start_date']);
        $contract->setEndDate($results['Contract_end_date']);
        $contract->setJobObject($results['Contract_job_object']);
        $contract->setJobDescription($results['Contract_job_description']);
        $contract->setJobSalary($results['Contract_job_salary']);
        $contract->setHourlyRate($results['Contract_hourly_rate']);
        $contract->setPdf($results['Contract_pdf']);
        $contract->setCreated($results['Contract_created']);
        $contract->setModified($results['Contract_modified']);
        $contract->setStatus($results['Contract_status']);
        $contract->setEtat((bool)$results['Contract_etat']);

        if ($joinType) {
            $contractType = new ContractType();
            $contractType->setId($results['ContractType_id']);
            $contractType->setName($results['ContractType_name']);
            $contractType->setDescription($results['ContractType_description']);
            $contractType->setCreated($results['ContractType_created']);
            $contractType->setEtat($results['ContractType_etat']);

            $contract->setContractType($contractType);
        }

        if ($joinEmployee) {
            $employee = new Employee();
            $employee->setId($results['Employee_id']);
            $employee->setFirstName($results['Employee_first_name']);
            $employee->setLastName($results['Employee_last_name']);
            $employee->setEmail($results['Employee_email']);   

            $contract->setEmployee($employee);        
        }

        return $contract;
    }

    /**
     * Add contract
     *
     * @param array|Contract $contract Contract to save
     * @return int|bool Returns the id of the contract if successful, false otherwise.
     */
    public function add(array|Contract $contract): int|bool
    {
        if (is_array($contract)) {
            $contract = $this->toEntity($contract);
        }

        if ($this->checkContract($contract)) {
            Flash::error("Un contrat de même type, pour le même employé et pour la même période existe déjà.");

            Session::write('__formdata__', json_encode($_POST));

            return false;
        }

        $errors = $contract->validation();
		if (!empty($errors)) {
			foreach ($errors as $error) {
				Flash::error($error);
			}

			Session::write('__formdata__', json_encode($_POST));

			return false;
		}

        if (strtotime($contract->getStartDate()) < time()) {
            $contract->setStatus('active');
        } else {
            $contract->setStatus('pending');
        }

        $contract->setCreated(date('Y-m-d H:i:s'));
        $contract->setModified(null);
        $contract->setEtat(true);

        $sql = "INSERT INTO contracts(employee_id, title, contract_type_id, start_date, end_date, job_object, job_description, job_salary, "
                ."hourly_rate, created, modified, status, etat) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)";

        try {
            $this->connectionManager->getConnection()->beginTransaction();

            $query = $this->connectionManager->getConnection()->prepare($sql);

            $query->bindValue(1, $contract->getEmployeeId(), \PDO::PARAM_INT);
            $query->bindValue(2, $contract->getTitle(), \PDO::PARAM_STR);
            $query->bindValue(3, $contract->getContractTypeId(), \PDO::PARAM_INT);
            $query->bindValue(4, $contract->getStartDate(), \PDO::PARAM_STR);
            $query->bindValue(5, $contract->getEndDate(), \PDO::PARAM_STR);
            $query->bindValue(6, $contract->getJobObject(), \PDO::PARAM_STR);
            $query->bindValue(7, $contract->getJobDescription(), \PDO::PARAM_STR);
            $query->bindValue(8, $contract->getJobSalary(), \PDO::PARAM_STR);
            $query->bindValue(9, $contract->getHourlyRate(), \PDO::PARAM_STR);
            $query->bindValue(10, $contract->getCreated(), \PDO::PARAM_STR);
            $query->bindValue(11, $contract->getModified(), \PDO::PARAM_STR);
            $query->bindValue(12, $contract->getStatus(), \PDO::PARAM_STR);
            $query->bindValue(13, $contract->getEtat(), \PDO::PARAM_BOOL);

            $query->execute();

            $model_id = (int)$this->connectionManager->getConnection()->lastInsertId();

            $this->connectionManager->getConnection()->commit();

            return $model_id;
        } catch (\PDOException $e) {
            $this->connectionManager->getConnection()->rollBack();

			throw new \Exception("SQL Exception: " . $e->getMessage(), 1);
		}
    }

    /**
     * Extend contract
     *
     * @param int $id Contract id
     * @param string $end_date Contract end date
     * @return bool Returns true if the contract has been extended, false otherwise.
     */
    public function extend($id, $end_date): bool
    {
        // Check if the contract exists
        $existedContract = $this->get($id);
        if (empty($existedContract)) {
            Flash::error("Aucun contrat trouvé avec l'id " . $id);
            
			return false;
		}

        $sql = "UPDATE contracts SET end_date = :end_date WHERE id = :id";

        try {
            $query = $this->connectionManager->getConnection()->prepare($sql);
            
            $query->bindValue(':end_date', $end_date, \PDO::PARAM_STR);
            $query->bindValue(':id', $id, \PDO::PARAM_INT);

            $updated = $query->execute();

            return $updated;
        } catch (\PDOException $e) {
            throw new \Exception("SQL Exception: " . $e->getMessage(), 1);
        }
    }

    /**
     * Update contract
     *
     * @param array|Contract $contract Contract to update
     * @return int|bool Returns true if the contract has been updated, false otherwise.
     */
    public function update(array|Contract $contract): bool
    {
        if (is_array($contract)) {
            $contract = $this->toEntity($contract);
        }

        // Check if the contract exists
        $existedContract = $this->get($contract->getId());
        if (empty($existedContract)) {
            Flash::error("Aucun contrat trouvé avec l'id " . $contract->getId());
            
			return false;
		}

        // Check if another contract with same data already exists
        if ($this->checkContract($contract)) {
            Flash::error("Un contrat de même type, pour le même employé et pour la même période existe déjà.");

            Session::write('__formdata__', json_encode($_POST));

            return false;
        }

        $existedContract->setTitle($contract->getTitle());
        $existedContract->setStartDate($contract->getStartDate());
        $existedContract->setEndDate($contract->getEndDate());
        $existedContract->setJobObject($contract->getJobObject());
        $existedContract->setJobDescription($contract->getJobDescription());
        $existedContract->setJobSalary($contract->getJobSalary());
        $existedContract->setHourlyRate($contract->getHourlyRate());
        $existedContract->setModified(date('Y-m-d H:i:s'));
        if (strtotime($existedContract->getStartDate()) < time()) {
            $existedContract->setStatus('active');
        } else {
            $existedContract->setStatus('pending');
        }

        if ($contract->getPdf() != null) {
            $existedContract->setPdf($contract->getPdf());
        }

        // Validation
        $errors = $existedContract->validation();
		if (!empty($errors)) {
			foreach ($errors as $error) {
				Flash::error($error);
			}

			Session::write('__formdata__', json_encode($_POST));

			return false;
		}

        $sql = "UPDATE contracts SET title = :title, start_date = :start_date, end_date = :end_date, job_object = :job_object, job_description = :job_description, "
                ."job_salary = :job_salary, hourly_rate = :hourly_rate, pdf = :pdf, modified = :modified, status = :status WHERE id = :id";

        try {
            $query = $this->connectionManager->getConnection()->prepare($sql);

            $query->bindValue(':title', $existedContract->getTitle(), \PDO::PARAM_STR);
            $query->bindValue(':status', $existedContract->getStatus(), \PDO::PARAM_STR);
            $query->bindValue(':start_date', $existedContract->getStartDate(), \PDO::PARAM_STR);
            $query->bindValue(':end_date', $existedContract->getEndDate(), \PDO::PARAM_STR);
            $query->bindValue(':job_object', $existedContract->getJobObject(), \PDO::PARAM_STR);
            $query->bindValue(':job_description', $existedContract->getJobDescription(), \PDO::PARAM_STR);
            $query->bindValue(':job_salary', $existedContract->getJobSalary(), \PDO::PARAM_STR);
            $query->bindValue(':hourly_rate', $existedContract->getHourlyRate(), \PDO::PARAM_STR);
            $query->bindValue(':pdf', $existedContract->getPdf(), \PDO::PARAM_STR);
            $query->bindValue(':modified', $existedContract->getModified(), \PDO::PARAM_STR);
            $query->bindValue(':id', $existedContract->getId(), \PDO::PARAM_INT);

            $updated = $query->execute();

            return $updated;
        } catch (\PDOException $e) {
            throw new \Exception("SQL Exception: " . $e->getMessage(), 1);
        }
    }

    /**
     * Delete contract
     *
     * @param int $id Contract id
     * @return bool Returns true if contract was deleted, false otherwise.
     */
    public function delete($id): bool
    {
        // Check if the contract exists
        $existedContract = $this->get($id);
        if (empty($existedContract)) {
            Flash::error("Aucun contrat trouvé avec l'id " . $id);
            
			return false;
		}

        $sql = "UPDATE contracts SET status = :status, etat = :etat WHERE id = :id";

        try {
            $query = $this->connectionManager->getConnection()->prepare($sql);

            $query->bindValue(':status', 'deleted', \PDO::PARAM_STR);
            $query->bindValue(':etat', false, \PDO::PARAM_BOOL);
            $query->bindValue(':id', $id, \PDO::PARAM_INT);

            $deleted = $query->execute();

            return $deleted;
        } catch (\PDOException $e) {
            throw new \Exception("SQL Exception: " . $e->getMessage(), 1);
        }
    }

    /**
     * Terminate contract
     *
     * @param int $id Contract id
     * @return bool Returns true if contract was terminated, false otherwise.
     */
    public function terminate($id): bool
    {
        // Check if the contract exists
        $existedContract = $this->get($id);
        if (empty($existedContract)) {
            Flash::error("Aucun contrat trouvé avec l'id " . $id);
            
			return false;
		}

        $sql = "UPDATE contracts SET status = :status WHERE id = :id";

        try {
            $query = $this->connectionManager->getConnection()->prepare($sql);

            $query->bindValue(':status', 'terminated', \PDO::PARAM_STR);
            $query->bindValue(':id', $id, \PDO::PARAM_INT);

            $deleted = $query->execute();

            return $deleted;
        } catch (\PDOException $e) {
            throw new \Exception("SQL Exception: " . $e->getMessage(), 1);
        }
    }

    /**
     * Check if the contract already exists
     *
     * @param Contract $contract Contract to check
     * @return boolean Returns true if contract already exists, false otherwise.
     */
    public function checkContract(Contract $contract): bool
    {
        $existed = false;

        $sql = "SELECT * FROM contracts c WHERE c.etat = :etat AND c.employee_id = :employee_id AND c.contract_type_id = :contract_type_id "
                ." AND c.start_date = :start_date AND c.end_date = :end_date";
            
        if (!empty($contract->getId())) {
            $sql .= " AND c.id != :id";
        }

        try {
            $query = $this->connectionManager->getConnection()->prepare($sql);

            $query->bindValue(':etat', 1, \PDO::PARAM_BOOL);
            $query->bindValue(':employee_id', $contract->getEmployeeId(), \PDO::PARAM_INT);
            $query->bindValue(':contract_type_id', $contract->getContractTypeId(), \PDO::PARAM_INT);
            $query->bindValue(':start_date', $contract->getStartDate(), \PDO::PARAM_STR);
            $query->bindValue(':end_date', $contract->getEndDate(), \PDO::PARAM_STR);
            if (!empty($contract->getId())) {
                $query->bindValue(':id',$contract->getId(), \PDO::PARAM_INT);
            }

            $query->execute();
            $results = $query->fetchAll(\PDO::FETCH_ASSOC);

            return !empty($results);
        } catch (\PDOException $e) {
            throw new \Exception("SQL Exception: " . $e->getMessage(), (int)$e->getCode());
        }

        return $existed;
    }

    /**
     * Parse array to Contract object
     *
     * @param array $data Array to parse
     * @return Contract|null Return the Contract object or null
     */
    public function toEntity(array $data): ?Contract
    {
        $id = !empty($data['id']) ? $data['id'] : null;
        $employee_id = !empty($data['employee_id']) ? $data['employee_id'] : null;
        $title = !empty($data['title']) ? $data['title'] : null;
        $contract_type_id = !empty($data['contract_type_id']) ? $data['contract_type_id'] : null;
        $start_date = !empty($data['start_date']) ? $data['start_date'] : null;
        $end_date = !empty($data['end_date']) ? $data['end_date'] : null;
        $job_object = !empty($data['job_object']) ? $data['job_object'] : null;
        $job_description = !empty($data['job_description']) ? $data['job_description'] : null;
        $job_salary = !empty($data['job_salary']) ? $data['job_salary'] : null;
        $hourly_rate = !empty($data['hourly_rate']) ? $data['hourly_rate'] : null;
        $pdf = !empty($data['pdf']) ? $data['pdf'] : null;
        $created = !empty($data['created']) ? $data['created'] : null;
        $modified = !empty($data['modified']) ? $data['modified'] : null;
        $status = !empty($data['status']) ? $data['status'] : null;
        $etat = !empty($data['etat']) ? $data['etat'] : null;
        $employee = !empty($data['employee']) ? $data['employee'] : null;
        $contract_type = !empty($data['contract$contract_type']) ? $data['contract$contract_type'] : null;

        $contract = new Contract();
        $contract->setId($id);
        $contract->setEmployeeId($employee_id);
        $contract->setContractTypeId($contract_type_id);
        $contract->setTitle($title);
        $contract->setStartDate($start_date);
        $contract->setEndDate($end_date);
        $contract->setJobObject($job_object);
        $contract->setJobDescription($job_description);
        $contract->setJobSalary($job_salary);
        $contract->setHourlyRate($hourly_rate);
        $contract->setPdf($pdf);
        $contract->setCreated($created);
        $contract->setModified($modified);
        $contract->setStatus($status);
        
        if ($etat != null) {
            $contract->setEtat($etat);
        }

        if ($employee != null) {
            $contract->setEmployee($employee);
        }

        if ($contract_type != null) {
            $contract->setContractType($contract_type);
        }

        return $contract;
    }
}