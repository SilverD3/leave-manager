<?php
declare(strict_types=1);

namespace App\Service;

require_once dirname(dirname(__DIR__)) . DS . DS . 'autoload.php';

use Core\Database\ConnectionManager;
use App\Entity\PermissionRequest;
use App\Entity\Employee;
use App\Entity\Permission;
use App\View\Helpers\DateHelper;
use Core\FlashMessages\Flash;
use Core\Utils\Session;

/**
 * Permissions Services
 */
class PermissionRequestsServices
{
    private $connectionManager;

    function __construct()
    {
        $this->connectionManager = new ConnectionManager();
    }

    /**
     * Get All Permission requests
     * @param  bool|null $joinEmployee Determines if employees should be joined
     * @param  int|string|null $year Year of permission
     * @return array                  Array of Permission requests or empty array
     * @throw \Exception When error occurs
     */
    public function getAll(bool $joinEmployee = false, $employee_id = null, $year = null)
    {
        $result = [];
        $permission_requests = [];
        $join = '';

        $select = "SELECT pr.id AS PermissionRequest_id, pr.employee_id AS PermissionRequest_employee_id, pr.reason AS PermissionRequest_reason, "
                    ."pr.description AS PermissionRequest_description, pr.start_date AS PermissionRequest_start_date, pr.end_date AS PermissionRequest_end_date, "
                    ."pr.status AS PermissionRequest_status, pr.reduce AS PermissionRequest_reduce, pr.created AS PermissionRequest_created, pr.etat AS PermissionRequest_etat ";

        if ($joinEmployee) {
            $select .= " , e.id AS Employee_id, e.first_name AS Employee_first_name, e.last_name AS Employee_last_name, e.email AS Employee_email ";
            $join = " JOIN employees e ON e.id = pr.employee_id ";
        }

        $sql = $select . "FROM permission_requests pr " . $join . " WHERE pr.etat = :etat";

        if (!is_null($employee_id)) {
            $sql .= " AND pr.employee_id = :employee_id ";
        }

        $year_clause = " AND YEAR(pr.end_date) = :year ";
        if (is_null($year)) {
            $year_val= date('Y');
        } elseif ($year == 'all') {
            $year_clause = '';
            $year_val = '';
        } else {
            $year_val = $year;
        }

        $sql .= $year_clause;

        try {
            $query = $this->connectionManager->getConnection()->prepare($sql);
            
            $query->bindValue(':etat', true, \PDO::PARAM_BOOL);

            if (!empty($year_val)) {
                $query->bindValue(':year', $year_val, \PDO::PARAM_INT);
            }

            if (!is_null($employee_id)) {
                $query->bindValue(':employee_id', $employee_id, \PDO::PARAM_INT);            
            }

            $query->execute();

            $result = $query->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new \Exception("SQL Exception: " . $e->getMessage(), 1);
        }
        
        if (empty($result)) {
            return [];
        }

        foreach ($result as $row) {

            $permissionRequest = new PermissionRequest();

            $permissionRequest->setId($row['PermissionRequest_id']);
            $permissionRequest->setEmployeeId($row['PermissionRequest_employee_id']);
            $permissionRequest->setReason($row['PermissionRequest_reason']);
            $permissionRequest->setDescription($row['PermissionRequest_description']);
            $permissionRequest->setStartDate($row['PermissionRequest_start_date']);
            $permissionRequest->setEndDate($row['PermissionRequest_end_date']);
            $permissionRequest->setStatus($row['PermissionRequest_status']);
            $permissionRequest->setReduce($row['PermissionRequest_reduce']);
            $permissionRequest->setCreated($row['PermissionRequest_created']);
            $permissionRequest->setEtat($row['PermissionRequest_etat']);

            if ($joinEmployee) {
                $employee = new Employee();
                $employee->setId($row['Employee_id']);
                $employee->setFirstName($row['Employee_first_name']);
                $employee->setLastName($row['Employee_last_name']);
                $employee->setEmail($row['Employee_email']);   

                $permissionRequest->setEmployee($employee);        
            }

            $permission_requests[] = $permissionRequest;
        }

        return $permission_requests;
    }

	/**
     * Count all permission requests
     * 
     * @param string $status Status to consider
     * @param int|null $employee_id Employee id
     * @param int|string|null $year Year of permission
     * @return int Number of permission requests
     */
    public function countAll(string $status = 'all', $employee_id = null, $year = null): int
    {
        $count = 0;

        $sql = "SELECT COUNT(*) AS count FROM permission_requests pr WHERE pr.etat = :etat";
        if ($status != 'all') {
            $sql .= " AND pr.status = :status";
        }

        if (!is_null($employee_id)) {
            $sql .= " AND pr.employee_id = :employee_id";
        }

        $year_clause = " AND YEAR(pr.end_date) = :year ";
        if (is_null($year)) {
            $year_val= date('Y');
        } elseif ($year == 'all') {
            $year_clause = '';
            $year_val = '';
        } else {
            $year_val = $year;
        }

        $sql .= $year_clause;

        try {
            $query = $this->connectionManager->getConnection()->prepare($sql);

            $query->bindValue(':etat', true, \PDO::PARAM_BOOL);

            if ($status != 'all') {
                $query->bindValue(':status', $status, \PDO::PARAM_STR);
            }

            if (!is_null($employee_id)) {
                $query->bindValue(':employee_id', $employee_id, \PDO::PARAM_INT);
            }

            if (!empty($year_val)) {
                $query->bindValue(':year', $year_val, \PDO::PARAM_INT);
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
     * Get Latests Permission Requests
     * @param  int    $record  Number of row to return
     * @param int $employee_id ID of the employee
     * @param int|string|null $year Year of permission
     * @return array<PermissionRequest>  List of latest permission request
     */
    public function getLatest(int $record, $employee_id = null, $year = null)
    {
        $result = [];
        $permission_requests = [];

        if (!is_null($employee_id)) {
            $where = "WHERE pr.etat = :etat AND pr.employee_id = :employee_id";
        } else {
            $where = "WHERE pr.etat = :etat";
        }

        $year_clause = " AND YEAR(pr.end_date) = :year ";
        if (is_null($year)) {
            $year_val= date('Y');
        } elseif ($year == 'all') {
            $year_clause = '';
            $year_val = '';
        } else {
            $year_val = $year;
        }

        $where .= $year_clause;

        $sql = "SELECT pr.id AS PermissionRequest_id, pr.employee_id AS PermissionRequest_employee_id, pr.reason AS PermissionRequest_reason, pr.description AS PermissionRequest_description, "
                ."pr.start_date AS PermissionRequest_start_date, pr.end_date AS PermissionRequest_end_date, pr.status AS PermissionRequest_status, pr.created AS PermissionRequest_created, "
                ."pr.etat AS PermissionRequest_etat, pr.reduce AS PermissionRequest_reduce, e.id AS Employee_id, e.first_name AS Employee_first_name, e.last_name AS Employee_last_name, e.email AS Employee_email "
                ."FROM permission_requests pr JOIN employees e ON e.id = pr.employee_id ". $where ." ORDER BY pr.created DESC LIMIT 0, :size";
        
        try {
            $query = $this->connectionManager->getConnection()->prepare($sql);

            $query->bindParam(':size', $record, \PDO::PARAM_INT);
            $query->bindValue(':etat', 1, \PDO::PARAM_INT);

            if (!is_null($employee_id)) {
                $query->bindValue(':employee_id', $employee_id, \PDO::PARAM_INT);
            }

            if (!empty($year_val)) {
                $query->bindValue(':year', $year_val, \PDO::PARAM_INT);
            }

            $query->execute();

            $result = $query->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new \Exception("SQL Exception: " . $e->getMessage(), 1);
        }
        
        if (empty($result)) {
            return [];
        }

        foreach ($result as $row) {

            $permissionRequest = new PermissionRequest();

            $permissionRequest->setId($row['PermissionRequest_id']);
            $permissionRequest->setEmployeeId($row['PermissionRequest_employee_id']);
            $permissionRequest->setReason($row['PermissionRequest_reason']);
            $permissionRequest->setDescription($row['PermissionRequest_description']);
            $permissionRequest->setStartDate($row['PermissionRequest_start_date']);
            $permissionRequest->setEndDate($row['PermissionRequest_end_date']);
            $permissionRequest->setStatus($row['PermissionRequest_status']);
            $permissionRequest->setReduce($row['PermissionRequest_reduce']);
            $permissionRequest->setCreated($row['PermissionRequest_created']);
            $permissionRequest->setEtat($row['PermissionRequest_etat']);
            
            $employee = new Employee();
            $employee->setId($row['Employee_id']);
            $employee->setFirstName($row['Employee_first_name']);
            $employee->setLastName($row['Employee_last_name']);
            $employee->setEmail($row['Employee_email']);   

            $permissionRequest->setEmployee($employee);        
         
            $permission_requests[] = $permissionRequest;
        }

        return $permission_requests;
    }

    /**
     * Get permission request by id
     *
     * @param int $id Permission request id
     * @param bool $joinEmployee Wheter to join employee or not
     * @return PermissionRequest|null Return permission request if found, null otherwise.
     */
    public function get($id, $joinEmployee = true): ?PermissionRequest
    {
        $select = "SELECT pr.id AS PermissionRequest_id, pr.employee_id AS PermissionRequest_employee_id, pr.reason AS PermissionRequest_reason, pr.description AS PermissionRequest_description, "
                ."pr.start_date AS PermissionRequest_start_date, pr.end_date AS PermissionRequest_end_date, pr.status AS PermissionRequest_status, pr.created AS PermissionRequest_created, "
                ."pr.etat AS PermissionRequest_etat, pr.reduce AS PermissionRequest_reduce ";
            
        $join = "";
        $from = "FROM permission_requests pr ";
        $where = "WHERE pr.id = :id AND pr.etat = :etat";

        if ($joinEmployee) {
            $select .= ", e.id AS Employee_id, e.first_name AS Employee_first_name, e.last_name AS Employee_last_name, e.email AS Employee_email ";
            $join = "JOIN employees e ON e.id = pr.employee_id ";
        }

        $sql = $select . $from . $join . $where;

        try {
            $query = $this->connectionManager->getConnection()->prepare($sql);

            $query->bindValue(':id', $id, \PDO::PARAM_INT);
            $query->bindValue(':etat', true, \PDO::PARAM_BOOL);
            $query->execute();

            $result = $query->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new \Exception("SQL Exception: " . $e->getMessage(), 1);
        }
        
        if (empty($result)) {
            return null;
        }

        $permissionRequest = new PermissionRequest();

        $permissionRequest->setId($result['PermissionRequest_id']);
        $permissionRequest->setEmployeeId($result['PermissionRequest_employee_id']);
        $permissionRequest->setReason($result['PermissionRequest_reason']);
        $permissionRequest->setDescription($result['PermissionRequest_description']);
        $permissionRequest->setStartDate($result['PermissionRequest_start_date']);
        $permissionRequest->setEndDate($result['PermissionRequest_end_date']);
        $permissionRequest->setStatus($result['PermissionRequest_status']);
        $permissionRequest->setReduce($result['PermissionRequest_reduce']);
        $permissionRequest->setCreated($result['PermissionRequest_created']);
        $permissionRequest->setEtat($result['PermissionRequest_etat']);
        
        if ($joinEmployee) {
            $employee = new Employee();
            $employee->setId($result['Employee_id']);
            $employee->setFirstName($result['Employee_first_name']);
            $employee->setLastName($result['Employee_last_name']);
            $employee->setEmail($result['Employee_email']);   
    
            $permissionRequest->setEmployee($employee);  
        }

        return $permissionRequest;
    }

    /**
     * Get the last permission of the employee
     *
     * @param int $employee_id Employee id
     * @param int|null $permission_id Permission id
     * @return PermissionRequest|null Return the last permission if found, null otherwise
     */
    public function getLastPermission($employee_id, $permission_id = null): ?PermissionRequest
    {
        $sql = "SELECT pr.id AS PermissionRequest_id, pr.employee_id AS PermissionRequest_employee_id, pr.reason AS PermissionRequest_reason, pr.description AS PermissionRequest_description, "
                ."pr.start_date AS PermissionRequest_start_date, pr.end_date AS PermissionRequest_end_date, pr.status AS PermissionRequest_status, pr.created AS PermissionRequest_created, "
                ."pr.etat AS PermissionRequest_etat, pr.reduce AS PermissionRequest_reduce FROM permission_requests pr WHERE pr.employee_id = :employee_id AND pr.etat = :etat AND pr.status = :status";

        if (!is_null($permission_id)) {
            $sql .= ' AND pr.id != :id';
        }

        $sql .= ' ORDER BY pr.id DESC LIMIT 0,1';

        try {
            $query = $this->connectionManager->getConnection()->prepare($sql);

            $query->bindValue(':employee_id', $employee_id, \PDO::PARAM_INT);
            if (!is_null($permission_id)) {
                $query->bindValue(':id', $permission_id, \PDO::PARAM_INT);
            }
            $query->bindValue(':etat', true, \PDO::PARAM_BOOL);
            $query->bindValue(':status', 'approved', \PDO::PARAM_STR);
            $query->execute();

            $result = $query->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new \Exception("SQL Exception: " . $e->getMessage(), 1);
        }
        
        if (empty($result)) {
            return null;
        }

        $permissionRequest = new PermissionRequest();

        $permissionRequest->setId($result['PermissionRequest_id']);
        $permissionRequest->setEmployeeId($result['PermissionRequest_employee_id']);
        $permissionRequest->setReason($result['PermissionRequest_reason']);
        $permissionRequest->setDescription($result['PermissionRequest_description']);
        $permissionRequest->setStartDate($result['PermissionRequest_start_date']);
        $permissionRequest->setEndDate($result['PermissionRequest_end_date']);
        $permissionRequest->setStatus($result['PermissionRequest_status']);
        $permissionRequest->setReduce($result['PermissionRequest_reduce']);
        $permissionRequest->setCreated($result['PermissionRequest_created']);
        $permissionRequest->setEtat($result['PermissionRequest_etat']);

        return $permissionRequest;
    }

    /**
     * Add permission request
     * @param array|PermissionRequest $permissionRequest Permission request to add
     * @return bool|int Returns the id of the permission request if success, false otherwise.
     */
    public function add(array|PermissionRequest $permissionRequest): bool|int
    {
        if (is_array($permissionRequest)) {
            $permissionRequest = $this->toEntity($permissionRequest);
        }

        $permissionRequest->setStatus('pending');
        $permissionRequest->setCreated(date('Y-m-d H:i:s'));
        $permissionRequest->setModified(null);
        $permissionRequest->setEtat(true);

        $errors = $permissionRequest->validation();
		if (!empty($errors)) {
			foreach ($errors as $error) {
				Flash::error($error);
			}

			Session::write('__formdata__', json_encode($_POST));

			return false;
		}

        $sql = "INSERT INTO permission_requests(employee_id, reason, description, start_date, end_date, status, reduce, created, modified, etat) "
                ." VALUES(?,?,?,?,?,?,?,?,?,?)";

        try {

			$this->connectionManager->getConnection()->beginTransaction();

			$query = $this->connectionManager->getConnection()->prepare($sql);

            $query->bindValue(1, $permissionRequest->getEmployeeId(), \PDO::PARAM_INT);
            $query->bindValue(2, $permissionRequest->getReason(), \PDO::PARAM_STR);
            $query->bindValue(3, $permissionRequest->getDescription(), \PDO::PARAM_STR);
            $query->bindValue(4, $permissionRequest->getStartDate(), \PDO::PARAM_STR);
            $query->bindValue(5, $permissionRequest->getEndDate(), \PDO::PARAM_STR);
            $query->bindValue(6, $permissionRequest->getStatus(), \PDO::PARAM_STR);
            $query->bindValue(7, $permissionRequest->getReduce(), \PDO::PARAM_BOOL);
            $query->bindValue(8, $permissionRequest->getCreated(), \PDO::PARAM_STR);
            $query->bindValue(9, $permissionRequest->getModified(), \PDO::PARAM_STR);
            $query->bindValue(10, $permissionRequest->getEtat(), \PDO::PARAM_BOOL);

            $query->execute();

            $request_id = (int)$this->connectionManager->getConnection()->lastInsertId();

            $this->connectionManager->getConnection()->commit();

            return $request_id;
        } catch (\PDOException $e) {
            $this->connectionManager->getConnection()->rollBack();

			throw new \Exception("SQL Exception: " . $e->getMessage(), 1);
		}
    }

    /**
     * Update permission request
     *
     * @param array|PermissionRequest $permissionRequest Permission request to update
     * @return bool Returns true if the permission request has been updated, false otherwise.
     */
    public function update(array|PermissionRequest $permissionRequest): bool
    {
        if (is_array($permissionRequest)) {
            $permissionRequest = $this->toEntity($permissionRequest);
        }

        $existedRequest = $this->get($permissionRequest->getId(), false);
        if (empty($existedRequest)) {
            Flash::error("Aucune demande de permission trouvée avec l'id " . $permissionRequest->getId());
            
			return false;
        }

        $existedRequest->setReason($permissionRequest->getReason());
        $existedRequest->setDescription($permissionRequest->getDescription());
        $existedRequest->setStartDate($permissionRequest->getStartDate());
        $existedRequest->setEndDate($permissionRequest->getEndDate());
        $existedRequest->setModified(date('Y-m-d H:i:s'));

        $errors = $existedRequest->validation();
		if (!empty($errors)) {
			foreach ($errors as $error) {
				Flash::error($error);
			}

			Session::write('__formdata__', json_encode($_POST));

			return false;
		}

        $sql = "UPDATE permission_requests SET reason = :reason, description = :description, start_date = :start_date, end_date = :end_date, modified = :modified WHERE id = :id";
    
        try {
			$query = $this->connectionManager->getConnection()->prepare($sql);

            $query->bindValue(':reason', $existedRequest->getReason(), \PDO::PARAM_STR);
            $query->bindValue(':description', $existedRequest->getDescription(), \PDO::PARAM_STR);
            $query->bindValue(':start_date', $existedRequest->getStartDate(), \PDO::PARAM_STR);
            $query->bindValue(':end_date', $existedRequest->getEndDate(), \PDO::PARAM_STR);
            $query->bindValue(':modified', $existedRequest->getModified(), \PDO::PARAM_STR);
            $query->bindValue(':id', $existedRequest->getId(), \PDO::PARAM_INT);

            $updated = $query->execute();

            return $updated;
        } catch (\PDOException $e) {
			throw new \Exception("SQL Exception: " . $e->getMessage(), 1);
		}
    
    }

    /**
     * Approve permission request
     *
     * @param int $id Permission request id
     * @param bool $reduce Whether to reduce leave duration or not
     * @return bool Returns true if permission request has been approved, false otherwise.
     */
    public function approve($id, $reduce = false): bool
    {
        $permissionRequest = $this->get($id, false);
        if (empty($permissionRequest)) {
            Flash::error("Aucune demande de permission trouvée avec l'id " . $id);
            
			return false;
        }

        $sql = "UPDATE permission_requests SET status = :status, reduce = :reduce WHERE id = :id";

        try {
			$query = $this->connectionManager->getConnection()->prepare($sql);
            
            $query->bindValue(':status', 'approved', \PDO::PARAM_STR);
            $query->bindValue(':reduce', $reduce, \PDO::PARAM_BOOL);
            $query->bindValue(':id', $id, \PDO::PARAM_INT);

            $approved = $query->execute();
            return $approved;
        } catch (\PDOException $e) {
            throw new \Exception("SQL Exception: " . $e->getMessage(), 1);
        }
    }

    /**
     * Disapprove permission request
     *
     * @param int $id Permission request id
     * @return bool Returns true if permission request has been disapproved, false otherwise.
     */
    public function disapprove($id): bool
    {
        $permissionRequest = $this->get($id, false);
        if (empty($permissionRequest)) {
            Flash::error("Aucune demande de permission trouvée avec l'id " . $id);
            
			return false;
        }

        $sql = "UPDATE permission_requests SET status = :status WHERE id = :id";

        try {
			$query = $this->connectionManager->getConnection()->prepare($sql);
            
            $query->bindValue(':status', 'disapproved', \PDO::PARAM_STR);
            $query->bindValue(':id', $id, \PDO::PARAM_INT);

            $disapproved = $query->execute();
            return $disapproved;
        } catch (\PDOException $e) {
            throw new \Exception("SQL Exception: " . $e->getMessage(), 1);
        }
    }

    /**
     * Delete permission request
     *
     * @param int $id Permission request id
     * @return bool Returns true if permission request has been deleted, false otherwise.
     */
    public function delete($id): bool
    {
        $permissionRequest = $this->get($id, false);
        if (empty($permissionRequest)) {
            Flash::error("Aucune demande de permission trouvée avec l'id " . $id);
            
			return false;
        }

        $sql = "UPDATE permission_requests SET status = :status, etat = :etat WHERE id = :id";

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

    public function toEntity(array $data): ?PermissionRequest
    {
        $id = !empty($data['id']) ? (int)$data['id'] : null;
        $employee_id = !empty($data['employee_id']) ? $data['employee_id'] : null;
        $reason = !empty($data['reason']) ? $data['reason'] : null;
        $description = !empty($data['description']) ? $data['description'] : null;
        $start_date = !empty($data['start_date']) ? $data['start_date'] : null;
        $end_date = !empty($data['end_date']) ? $data['end_date'] : null;
        $status = !empty($data['status']) ? $data['status'] : false;
        $reduce = !empty($data['reduce']) ? $data['reduce'] : null;
        $created = !empty($data['created']) ? $data['created'] : null;
        $modified = !empty($data['modified']) ? $data['modified'] : null;
        $etat = !empty($data['etat']) ? $data['etat'] : null;

        $permissionRequest = new PermissionRequest();
        $permissionRequest->setId($id);
        $permissionRequest->setEmployeeId($employee_id);
        $permissionRequest->setReason($reason);
        $permissionRequest->setDescription($description);
        $permissionRequest->setStartDate($start_date);
        $permissionRequest->setEndDate($end_date);
        $permissionRequest->setStatus($status);
        $permissionRequest->setReduce((bool)$reduce);
        $permissionRequest->setCreated($created);
        $permissionRequest->setModified($modified);
        $permissionRequest->setEtat($etat);

        return $permissionRequest;
    }

    /**
     * Get All Permissions
     * @param  bool|boolean $joinEmployee Determines if employees should be joined
     * @param int|null $employeeId Id of the employee
     * @param int|string|null $year Year of permission
     * @return array                      Array of Permission or empty array
     * @throw \Exception When error occurs
     */
    public function getAllPermissions(bool $joinEmployee = false, $employee_id = null, $year = null)
    {
        $result = [];
        $permissions = [];
        $join = '';

        $select = "SELECT pr.id AS PermissionRequest_id, pr.employee_id AS PermissionRequest_employee_id, pr.reason AS PermissionRequest_reason, "
                    ." pr.description AS PermissionRequest_description, pr.start_date AS PermissionRequest_start_date, pr.end_date AS PermissionRequest_end_date, "
                    ." pr.reduce AS PermissionRequest_reduce, pr.created AS PermissionRequest_created, pr.etat AS PermissionRequest_etat ";

        if ($joinEmployee) {
            $select .= " , e.id AS Employee_id, e.first_name AS Employee_first_name, e.last_name AS Employee_last_name, e.email AS Employee_email ";
            $join = " JOIN employees e ON e.id = pr.employee_id ";
        }

        $sql = $select . "FROM permission_requests pr " . $join . " WHERE pr.etat = :etat AND pr.status = :status ";

        if (!is_null($employee_id)) {
            $sql .= " AND pr.employee_id = :employee_id ";
        }

        $year_clause = " AND YEAR(pr.end_date) = :year ";
        if (is_null($year)) {
            $year_val= date('Y');
        } elseif ($year == 'all') {
            $year_clause = '';
            $year_val = '';
        } else {
            $year_val = $year;
        }

        $sql .= $year_clause;

        try {
            $query = $this->connectionManager->getConnection()->prepare($sql);
            
            $query->bindValue(':etat', true, \PDO::PARAM_BOOL);
            $query->bindValue(':status', 'approved', \PDO::PARAM_STR);
            if (!is_null($employee_id)) {
                $query->bindValue(':employee_id', $employee_id, \PDO::PARAM_INT);            
            }

            if (!empty($year_val)) {
                $query->bindValue(':year', $year_val, \PDO::PARAM_INT);
            }

            $query->execute();

            $result = $query->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new \Exception("SQL Exception: " . $e->getMessage(), 1);
        }
        
        if (empty($result)) {
            return [];
        }

        foreach ($result as $row) {

            $permission = new Permission();

            $permission->setId($row['PermissionRequest_id']);
            $permission->setEmployeeId($row['PermissionRequest_employee_id']);
            $permission->setReason($row['PermissionRequest_reason']);
            $permission->setDescription($row['PermissionRequest_description']);
            $permission->setStartDate($row['PermissionRequest_start_date']);
            $permission->setEndDate($row['PermissionRequest_end_date']);
            $permission->setReduce((bool)$row['PermissionRequest_reduce']);
            $permission->setCreated($row['PermissionRequest_created']);
            $permission->setEtat($row['PermissionRequest_etat']);

            if ($joinEmployee) {
                $employee = new Employee();
                $employee->setId($row['Employee_id']);
                $employee->setFirstName($row['Employee_first_name']);
                $employee->setLastName($row['Employee_last_name']);
                $employee->setEmail($row['Employee_email']);   

                $permission->setEmployee($employee);        
            }

            $permissions[] = $permission;
        }

        return $permissions;
    }

    /**
     * Compute total days spent in permissions by employee and by year
     *
     * @param int|null $employee_id Employee to consider. If null, all employees will be considered
     * @param int|string|null $year Year of permission
     * @return int Number of spent days
     */
    public function getSentDays($employee_id = null, $year = null): int
    {
        $configsServices = new ConfigsServices();
        $hourlyRateConfig = $configsServices->getByCode('LM_HOURLY_RATE');
        $hourlyRate = (int)$hourlyRateConfig->getValue();

        $workingDaysConfigs = $configsServices->getByCode('LM_WORKING_DAYS');
        $nbWorkingDays = count(explode(',', $workingDaysConfigs->getValue()));

        if ($year === null) {
            $year = (string)date('Y');
        }

        $nb_spent_days = 0;
        $nb_spent_minutes = 0;
        $permissions = $this->getAllPermissions(false, $employee_id, $year);

        foreach ($permissions as $permission) {
            if ($permission->getReduce()) {
                $nb_spent_minutes += $this->getWorkingMinutes($permission->getStartDate(), $permission->getEndDate(), $year);
            }
        }

        $nb_hours = $nb_spent_minutes / 60;
        
        $nb_spent_days = round($nb_hours / ($hourlyRate / $nbWorkingDays), 0, PHP_ROUND_HALF_DOWN);

        return (int)$nb_spent_days;
    }

    /**
     * Compute the number of hours between two dates
     *
     * @param mixed $dateFrom First date
     * @param mixed $dateTo Second date
     * @param string $year Year to consider
     * @return int Number of hours between the dates
     */
    public function getWorkingMinutes($dateFrom, $dateTo, $year)
    {
        $configsServices = new ConfigsServices();
        $periodWorkingMinutes = 0;

        $year = (string)$year;

        // Get holidays
        $holidays = [];
        $holidaysConfig = $configsServices->getByCode('LM_HOLIDAYS');
        $holidays = explode(',', str_replace('*', $year, $holidaysConfig->getValue()));

        // Get daily breaks
        $dailyBreaks = [];
        $dailyBreaksConfig = $configsServices->getByCode('LM_DAILY_BREAKS');
        $dailyBreaks = explode(',', $dailyBreaksConfig->getValue());

        // Get work start and end time
        $workBeginAtConfig = $configsServices->getByCode('LM_WORK_BEGIN_AT');
        $workEndAtConfig = $configsServices->getByCode('LM_WORK_END_AT');
        $workBeginAt = $workBeginAtConfig->getValue();
        $workEndAt = $workEndAtConfig->getValue();

        // Get working days
        $workingDays = [];
        $workingDaysConfigs = $configsServices->getByCode('LM_WORKING_DAYS');
        $workingDays = DateHelper::daysNumbers(explode(',', $workingDaysConfigs->getValue()));

        $periodWorkingMinutes = DateHelper::getWorkingMinutes($dateFrom, $dateTo, $workBeginAt, $workEndAt, $holidays, $workingDays, $dailyBreaks);

        return $periodWorkingMinutes;
    }

    /**
     * Get all years for permissions
     *
     * @return array Array of years
     */
    public function getYears()
    {
        $sql = "SELECT DISTINCT(YEAR(end_date)) as year FROM permission_requests";

        try {
            $query = $this->connectionManager->getConnection()->prepare($sql);
            $query->execute();

            $result = $query->fetchAll(\PDO::FETCH_ASSOC);

            return $result;
        } catch (\PDOException $e) {
            throw new \Exception("SQL Exception: " . $e->getMessage(), 1);
        }   
    }
}