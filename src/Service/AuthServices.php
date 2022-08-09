<?php
declare(strict_types=1);

namespace App\Service;

require_once dirname(dirname(__DIR__)) . DS . DS . 'autoload.php';

use Core\Database\ConnectionManager;
use App\Entity\Employee;
use App\Entity\Role;
use Core\Auth\PasswordHasher;

class AuthServices
{
    public function login(string $username, string $password): ?Employee
    {
        $connectionManager = new ConnectionManager();

        $hashedPassword = (new PasswordHasher())->hash($password);

        $result = [];

        $sql = "SELECT e.id AS Employee_id, e.first_name AS Employee_first_name, e.last_name AS Employee_last_name, e.email AS Employee_email, e.username AS Employee_username, e.pwd AS Employee_pwd, e.role_id AS Employee_role_id, e.created AS Employee_created, e.modified AS Employee_modified, e.token AS Employee_token, e.token_exp_date, e.status AS Employee_status, e.etat AS Employee_etat, r.id AS Role_id, r.code AS Role_code, r.name AS Role_name
            FROM employees e 
            JOIN roles r ON r.id = e.role_id 
            WHERE e.username = ? AND e.pwd = ? AND e.etat = ?";

        try {
            $query = $connectionManager->getConnection()->prepare($sql);

            $query->execute([$username, $hashedPassword, 1]);

            $result = $query->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new \Exception("SQL Exception: " . $e->getMessage(), 1);
        }
        
        if (empty($result)) {
            return null;
        }

        $role = new Role();
        $role->setId = $result['Role_id'];
        $role->setCode = $result['Role_code'];
        $role->setName = $result['Role_name'];

        $employee = new Employee();
        $employee->setId($result['Employee_id']);
        $employee->setFirstName($result['Employee_first_name']);
        $employee->setLastName($result['Employee_last_name']);
        $employee->setEmail($result['Employee_email']);
        $employee->setUsername($result['Employee_username']);
        $employee->setPwd($result['Employee_pwd']);
        $employee->setRoleId($result['Employee_role_id']);
        $employee->setCreated($result['Employee_created']);
        $employee->setModified($result['Employee_modified']);
        $employee->setToken($result['Employee_token']);
        $employee->setStatus($result['Employee_status']);
        $employee->setEtat($result['Employee_etat']);
        $employee->setRole($role);

        return $employee;
    }
}