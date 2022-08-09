<?php
declare(strict_types=1);

namespace App\Service;

require_once dirname(dirname(__DIR__)) . DS . DS . 'autoload.php';

use Core\Database\ConnectionManager;
use App\Entity\PermissionRequest;
use App\Entity\Employee;

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
     * @param  bool|boolean $joinEmployee Determines if employees should be joined
     * @return array                  Array of Permission requests or empty array
     * @throw \Exception When error occurs
     */
    public function getAll(bool $joinEmployee = false)
    {
        $result = [];
        $permission_requests = [];
        $join = '';

        $select = "SELECT pr.id AS PermissionRequest_id, pr.employee_id AS PermissionRequest_employee_id, pr.reason AS PermissionRequest_reason, pr.description AS PermissionRequest_description, pr.start_date AS PermissionRequest_start_date, pr.end_date AS PermissionRequest_end_date, pr.status AS PermissionRequest_status, pr.created AS PermissionRequest_created, pr.etat AS PermissionRequest_etat ";

        if ($joinEmployee) {
            $select .= " , e.id AS Employee_id, e.first_name AS Employee_first_name, e.last_name AS Employee_last_name, e.email AS Employee_email, ";
            $join = " JOIN employees e ON e.id = pr.role_id  ";
        }

        $sql = $select . " FROM permission_requests pr " . $join . " WHERE pr.etat = ?";

        try {
            $query = $this->connectionManager->getConnection()->prepare($sql);

            $query->execute([1]);

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
     * @return int Number of permission requests
     */
    public function countAll(string $status = 'all'): int
    {
        $count = 0;

        $sql = "SELECT COUNT(*) AS count FROM permission_requests pr WHERE pr.etat = ?";
        if ($status != 'all') {
            $sql .= " AND pr.status = ?";
        }

        try {
            $query = $this->connectionManager->getConnection()->prepare($sql);

            if ($status == 'all') {
                $query->execute([1]);
            } else {
                $query->execute([1, $status]);
            }

            $result = $query->fetch(\PDO::FETCH_ASSOC);

            $count = (int)$result['count'];
        } catch (\PDOException $e) {
            throw new \Exception("SQL Exception: " . $e->getMessage(), 1);
        }
        
        return $count;
    }

    /**
     * Get Latests Permission Requests
     * @param  int    $record            Number of row to return
     * @return array<PermissionRequest>  List of latest permission request
     */
    public function getLatest(int $record)
    {
        $result = [];
        $permission_requests = [];

        $sql = "SELECT pr.id AS PermissionRequest_id, pr.employee_id AS PermissionRequest_employee_id, pr.reason AS PermissionRequest_reason, pr.description AS PermissionRequest_description, pr.start_date AS PermissionRequest_start_date, pr.end_date AS PermissionRequest_end_date, pr.status AS PermissionRequest_status, pr.created AS PermissionRequest_created, pr.etat AS PermissionRequest_etat, e.id AS Employee_id, e.first_name AS Employee_first_name, e.last_name AS Employee_last_name, e.email AS Employee_email FROM permission_requests pr JOIN employees e ON e.id = pr.employee_id WHERE pr.etat = :etat ORDER BY pr.created DESC LIMIT 0, :size";

        try {
            $query = $this->connectionManager->getConnection()->prepare($sql);

            $query->bindParam(':size', $record, \PDO::PARAM_INT);
            $query->bindValue(':etat', 1, \PDO::PARAM_INT);
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
}