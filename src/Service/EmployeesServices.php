<?php
declare(strict_types=1);

namespace App\Service;

require_once dirname(dirname(__DIR__)) . DS . DS . 'autoload.php';

use Core\Database\ConnectionManager;
use App\Entity\Employee;
use App\Entity\Role;
use Core\Auth\PasswordHasher;
use Core\FlashMessages\Flash;
use Core\Utils\Session;

/**
 * Employees Services
 */
class EmployeesServices
{
	/**
	 * @var ConnectionManager $connectionManager
	 */
	private $connectionManager;

	/**
	 * Default configuration for queries
	 * @var array $query_default_config
	 */
	private $query_default_config = [
		'joinRole' => false,
		'limit' => 50,
		'offset' => 0,
		'conditions' => [],
		'order' => 'first_name',
		'order_dir' => 'DESC',
	];

	function __construct()
	{
		$this->connectionManager = new ConnectionManager();
	}

	/**
	 * Get All employee
	 * @param  bool|boolean $joinRole Determines if roles should be joined
	 * @return array                  Array of Employee or empty array
	 * @throw \Exception When error occurs
	 */
	public function getAll(bool $joinRole = false)
	{
		$result = [];
		$employees = [];
		$join = '';

		$select = "SELECT e.id AS Employee_id, e.first_name AS Employee_first_name, e.last_name AS Employee_last_name, e.email AS Employee_email, e.username AS Employee_username, e.pwd AS Employee_pwd, e.role_id AS Employee_role_id, e.created AS Employee_created, e.modified AS Employee_modified, e.token AS Employee_token, e.token_exp_date, e.status AS Employee_status, e.etat AS Employee_etat ";

		if ($joinRole) {
			$select .= " , r.id AS Role_id, r.code AS Role_code, r.name AS Role_name ";
			$join = " JOIN roles r ON r.id = e.role_id  ";
		}

		$sql = $select . " FROM employees e " . $join . " WHERE e.etat = ?";

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
			$employee = new Employee();
			$employee->setId($row['Employee_id']);
			$employee->setFirstName($row['Employee_first_name']);
			$employee->setLastName($row['Employee_last_name']);
			$employee->setEmail($row['Employee_email']);
			$employee->setUsername($row['Employee_username']);
			$employee->setPwd($row['Employee_pwd']);
			$employee->setRoleId($row['Employee_role_id']);
			$employee->setCreated($row['Employee_created']);
			$employee->setModified($row['Employee_modified']);
			$employee->setToken($row['Employee_token']);
			$employee->setStatus($row['Employee_status']);
			$employee->setEtat($row['Employee_etat']);

			if ($joinRole) {
				$role = new Role();
				$role->setId($row['Role_id']);
				$role->setCode($row['Role_code']);
				$role->setName($row['Role_name']);
				$employee->setRole($role);
			} 
			
			$employees[] = $employee;
		}
		
		return $employees;
	}

	/**
	 * Count all employees
	 * 
	 * @return int Number of employees
	 */
	public function countAll(): int
	{
		$count = 0;
		$join = '';

		$sql = "SELECT COUNT(*) AS count FROM employees e WHERE e.etat = ?";

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

	public function getById($id): ?Employee
	{
		$result = [];

		$sql = "SELECT e.id AS Employee_id, e.first_name AS Employee_first_name, e.last_name AS Employee_last_name, e.email AS Employee_email, e.username AS Employee_username, e.pwd AS Employee_pwd, e.role_id AS Employee_role_id, e.created AS Employee_created, e.modified AS Employee_modified, e.token AS Employee_token, e.token_exp_date, e.status AS Employee_status, e.etat AS Employee_etat, r.id AS Role_id, r.code AS Role_code, r.name AS Role_name
			FROM employees e 
			JOIN roles r ON r.id = e.role_id 
			WHERE e.id = ? AND e.etat = ?";

		try {
			$query = $this->connectionManager->getConnection()->prepare($sql);

			$query->execute([$id, 1]);

			$result = $query->fetch(\PDO::FETCH_ASSOC);
		} catch (\PDOException $e) {
			throw new \Exception("SQL Exception: " . $e->getMessage(), 1);
		}
		
		if (empty($result)) {
			return null;
		}

		$role = new Role();
		$role->setId($result['Role_id']);
		$role->setCode($result['Role_code']);
		$role->setName($result['Role_name']);

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

	/**
	 * Add new employee
	 *
	 * @param array|Employee $employee Employee data
	 * @return integer|bool Returns the id of the employee on success, false otherwise
	 */
	public function add(array|Employee $employee): bool|int
	{
		if (is_array($employee)) {
			$employee = $this->toEntity($employee);
		}
	
		$employee->setCreated(date('Y-m-d H:i:s'));
		$employee->setModified(null);
		$employee->setToken(null);
		$employee->setTokenExpDate(null);
		$employee->setStatus('active');
		$employee->setEtat(true);

		if ($this->checkEmployee($employee)) {
			Flash::error("Un employé avec les mêmes informations existe déjà.");

            Session::write('__formdata__', json_encode($_POST));

            return false;
		}

		$errors = $employee->validation();
		if (!empty($errors)) {
			foreach ($errors as $error) {
				Flash::error($error);
			}

			Session::write('__formdata__', json_encode($_POST));

			return false;
		}

		$sql = "INSERT INTO employees (first_name, last_name, email, username, pwd, role_id, created, modified, token, token_exp_date, status, etat) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";

		try {

			$this->connectionManager->getConnection()->beginTransaction();

			$query = $this->connectionManager->getConnection()->prepare($sql);
			$query->bindValue(1, $employee->getFirstName(), \PDO::PARAM_STR);
			$query->bindValue(2, $employee->getLastName(), \PDO::PARAM_STR);
			$query->bindValue(3, $employee->getEmail(), \PDO::PARAM_STR);
			$query->bindValue(4, $employee->getUsername(), \PDO::PARAM_STR);
			$query->bindValue(5, $employee->getPwd(), \PDO::PARAM_STR);
			$query->bindValue(6, $employee->getRoleId(), \PDO::PARAM_INT);
			$query->bindValue(7, $employee->getCreated(), \PDO::PARAM_STR);
			$query->bindValue(8, $employee->getModified(), \PDO::PARAM_STR);
			$query->bindValue(9, $employee->getToken(), \PDO::PARAM_STR);
			$query->bindValue(10, $employee->getTokenExpDate(), \PDO::PARAM_STR);
			$query->bindValue(11, $employee->getStatus(), \PDO::PARAM_STR);
			$query->bindValue(12, $employee->getEtat(), \PDO::PARAM_BOOL);

			$query->execute();
			$employeeId = $this->connectionManager->getConnection()->lastInsertId();

			$this->connectionManager->getConnection()->commit();

			return $employeeId;
		} catch (\PDOException $e) {
			throw new \Exception("SQL Exception: " . $e->getMessage(), 1);
		}
	}

	/**
	 * Update employee
	 *
	 * @param array|Employee $employee Employee data
	 * @return bool Returns true if the employee was updated, false otherwise
	 */
	public function update(array|Employee $employee): bool
	{
		if (is_array($employee)) {
			$employee = $this->toEntity($employee);
		}

		$existedEmployee = $this->getById($employee->getId());

		if (empty($existedEmployee)) {
			Flash::error("Aucun employé trouvé avec l'id " . $employee->getId());

			return false;
		}

		$employee->setModified(date('Y-m-d H:i:s'));

		if ($this->checkEmployee($employee)) {
			Flash::error("Un employé avec les mêmes informations existe déjà.");

            Session::write('__formdata__', json_encode($_POST));

            return false;
		}

		$sql = "UPDATE employees SET first_name = ?, last_name = ?, email = ?, username = ?, pwd = ?, role_id = ?, modified = ? WHERE id = ?";

		try {

			$this->connectionManager->getConnection()->beginTransaction();

			$query = $this->connectionManager->getConnection()->prepare($sql);

			if (empty($employee->getFirstName())) {
				$query->bindValue(1, $existedEmployee->getFirstName(), \PDO::PARAM_STR);
			} else {
				$query->bindValue(1, $employee->getFirstName(), \PDO::PARAM_STR);
			}

			if (empty($employee->getLastName())) {
				$query->bindValue(2, $existedEmployee->getLastName(), \PDO::PARAM_STR);
			} else {
				$query->bindValue(2, $employee->getLastName(), \PDO::PARAM_STR);
			}

			if (empty($employee->getEmail())) {
				$query->bindValue(3, $existedEmployee->getEmail(), \PDO::PARAM_STR);
			} else {
				$query->bindValue(3, $employee->getEmail(), \PDO::PARAM_STR);
			}

			if (empty($employee->getUsername())) {
				$query->bindValue(4, $existedEmployee->getUsername(), \PDO::PARAM_STR);
			} else {
				$query->bindValue(4, $employee->getUsername(), \PDO::PARAM_STR);
			}

			if (empty($employee->getPwd())) {
				$query->bindValue(5, $existedEmployee->getPwd(), \PDO::PARAM_STR);
			} else {
				$query->bindValue(5, $employee->getPwd(), \PDO::PARAM_STR);
			}

			if (empty($employee->getRoleId())) {
				$query->bindValue(6, $existedEmployee->getRoleId(), \PDO::PARAM_STR);
			} else {
				$query->bindValue(6, $employee->getRoleId(), \PDO::PARAM_STR);
			}
			$query->bindValue(7, $employee->getModified(), \PDO::PARAM_STR);
			$query->bindValue(8, $employee->getId(), \PDO::PARAM_INT);

			$updated = $query->execute();

			$this->connectionManager->getConnection()->commit();

			return $updated;
		} catch (\PDOException $e) {
			throw new \Exception("SQL Exception: " . $e->getMessage(), 1);
		}
	}

	/**
	 * Delete employee method
	 *
	 * @param integer $id Employee id
	 * @return boolean Returns true if employee was deleted, false otherwise.
	 */
	public function delete(int $id): bool
	{
		$existedEmployee = $this->getById($id);
		if (empty($existedEmployee)) {
			Flash::error("Aucun employé trouvé avec l'id " . $id);

			return false;
		}

		$sql = "UPDATE employees SET etat = ?, status = ? WHERE id = ?";

		try {
			$query = $this->connectionManager->getConnection()->prepare($sql);

			$query->bindValue(1, 0, \PDO::PARAM_BOOL);
			$query->bindValue(2, 'deleted', \PDO::PARAM_STR);
			$query->bindValue(3, $id, \PDO::PARAM_INT);

			$deleted = $query->execute();

			return $deleted;
		} catch (\PDOException $e) {
			throw new \Exception("SQL Exception: " . $e->getMessage(), 1);
		}
	}

	/**
	 * Check if the employee already exists
	 *
	 * @param Employee $employee Employee
	 * @return boolean Returns true if employee exists, false otherwise.
	 */
	public function checkEmployee(Employee $employee): bool
	{
		$exist = true;

		$sql = "SELECT * FROM employees WHERE first_name = ? AND last_name = ? AND email = ? AND etat = ?";
		if (!is_null($employee->getId())) {
			$sql .= " AND id != ?";
		}

		$sql .= " LIMIT 0,1";

		try {
			$query = $this->connectionManager->getConnection()->prepare($sql);
			$query->bindValue(1, $employee->getFirstName());
			$query->bindValue(2, $employee->getLastName());
			$query->bindValue(3, $employee->getEmail());
			$query->bindValue(4, true, \PDO::PARAM_BOOL);
			if (!is_null($employee->getId())) {
				$query->bindValue(5, $employee->getId(), \PDO::PARAM_INT);
			}

			$query->execute();

			$result = $query->fetch(\PDO::FETCH_ASSOC);

			if (empty($result)) {
				$exist = false;
			} else {
				$exist = true;
			}
		} catch (\PDOException $e) {
			throw new \Exception("SQL Exception: " . $e->getMessage(), 1);
		}

		return $exist;
	}

	/**
	 * Parse employee data to entity
	 *
	 * @param array $data Employee data
	 * @return Employee|null Return Employee entity if success, null otherwise.
	 */
	public function toEntity(array $data): ?Employee
	{
		$id = $data['id'] ? (int)$data['id'] : null;
		$first_name = !empty($data['first_name']) ? htmlentities($data['first_name']) : null;
		$last_name = !empty($data['last_name']) ? htmlentities($data['last_name']) : null;
		$username = !empty($data['username']) ? htmlentities($data['username']) : null;
		$email = !empty($data['email']) ? htmlentities($data['email']) : null;
		$password = !empty($data['password']) ? (new PasswordHasher())->hash(htmlentities($data['password'])) : null;
		$role_id = !empty($data['role_id']) ? htmlentities($data['role_id']) : null;

		$employee = new Employee();
		$employee->setId($id);
		$employee->setFirstName($first_name);
		$employee->setLastName($last_name);
		$employee->setEmail($email);
		$employee->setUsername($username);
		$employee->setRoleId($role_id);
		$employee->setPwd($password);

		return $employee;
	}
}