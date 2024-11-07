<?php

declare(strict_types=1);

/**
 * Leave manager : Simple app for contract and leave management.
 *
 * @copyright Copyright (c) Silevester D. (https://github.com/SilverD3)
 * @link      https://github.com/SilverD3/leave-manager Leave Manager Project
 * @since     1.0 (2022)
 */

namespace App\Service;

require_once dirname(dirname(__DIR__)) . DS . DS . 'autoload.php';

use Core\Database\ConnectionManager;
use App\Entity\Leave;
use App\Entity\Employee;
use App\View\Helpers\DateHelper;
use Core\FlashMessages\Flash;
use Core\Utils\DateUtils;
use Core\Utils\Session;

/**
 * Leaves Services
 */
class LeavesServices
{
    private $connectionManager;
    private $configsServices;

    function __construct()
    {
        $this->connectionManager = new ConnectionManager();
        $this->configsServices = new ConfigsServices();
    }

    /**
     * Get all leaves
     *
     * @param int|null $year        Year to consider
     * @param bool $joinEmployee    Wheter to join employees or not
     * @param int|null $employe_id  Employee to consider
     * @return Leave[]              Returns all leaves
     */
    public function getAll($year = null, bool $joinEmployee = false, $employee_id = null)
    {
        $results = [];
        $leaves = [];

        $select = "SELECT lv.id AS Leave_id, lv.employee_id AS Leave_employee_id, lv.year AS Leave_year, lv.days AS Leave_days, "
            . "lv.start_date AS Leave_start_date, lv.end_date AS Leave_end_date, lv.created AS Leave_created, lv.modified AS Leave_modified, "
            . "lv.note AS Leave_note, lv.etat AS Leave_etat ";
        $from = ' FROM leaves lv ';
        $join = '';
        $where = ' WHERE lv.etat = :etat';
        $order = " ORDER BY lv.start_date ASC, lv.end_date DESC ";

        if ($joinEmployee) {
            $select .= " , e.id AS Employee_id, e.first_name AS Employee_first_name, e.last_name AS Employee_last_name, e.email AS Employee_email ";
            $join .= " INNER JOIN employees e ON e.id = lv.employee_id ";
        }

        if (is_null($year)) {
            $year = date('Y');
        }

        if ($year != 'all') {
            $where .= ' AND lv.year = :year';
        }

        $sql = $select . $from . $join . $where . $order;

        try {
            $query = $this->connectionManager->getConnection()->prepare($sql);

            $query->bindValue(':etat', 1, \PDO::PARAM_BOOL);

            if ($year != 'all') {
                $query->bindValue(':year', $year, \PDO::PARAM_INT);
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
            $leave = new Leave();
            $leave->setId($row['Leave_id']);
            $leave->setEmployeeId($row['Leave_employee_id']);
            $leave->setYear($row['Leave_year']);
            $leave->setDays($row['Leave_days']);
            $leave->setStartDate($row['Leave_start_date']);
            $leave->setEndDate($row['Leave_end_date']);
            $leave->setCreated($row['Leave_created']);
            $leave->setModified($row['Leave_modified']);
            $leave->setNote($row['Leave_note']);
            $leave->setEtat($row['Leave_etat']);

            if ($joinEmployee) {
                $employee = new Employee();
                $employee->setId($row['Employee_id']);
                $employee->setFirstName($row['Employee_first_name']);
                $employee->setLastName($row['Employee_last_name']);
                $employee->setEmail($row['Employee_email']);

                $leave->setEmployee($employee);
            }

            $leaves[] = $leave;
        }

        return $leaves;
    }

    /**
     * Get all current leave
     *
     * @param boolean $joinEmployee Wheter to join employees or not
     * @return Leave[] Return all current leave
     */
    public function getCurrentLeaves(bool $joinEmployee = true)
    {
        $results = [];
        $leaves = [];

        $select = "SELECT lv.id AS Leave_id, lv.employee_id AS Leave_employee_id, lv.year AS Leave_year, lv.days AS Leave_days, "
            . "lv.start_date AS Leave_start_date, lv.end_date AS Leave_end_date, lv.created AS Leave_created, lv.modified AS Leave_modified, "
            . "lv.note AS Leave_note, lv.etat AS Leave_etat ";
        $from = ' FROM leaves lv ';
        $join = '';
        $where = ' WHERE lv.etat = :etat AND (DATE(lv.start_date) <= :date AND DATE(lv.end_date) >= :date)';
        $order = " ORDER BY lv.start_date ASC, lv.end_date DESC ";

        if ($joinEmployee) {
            $select .= " , e.id AS Employee_id, e.first_name AS Employee_first_name, e.last_name AS Employee_last_name, e.email AS Employee_email ";
            $join .= " INNER JOIN employees e ON e.id = lv.employee_id ";
        }

        $sql = $select . $from . $join . $where . $order;

        try {
            $query = $this->connectionManager->getConnection()->prepare($sql);

            $query->bindValue(':etat', 1, \PDO::PARAM_BOOL);
            $query->bindValue(':date', date('Y-m-d'), \PDO::PARAM_STR);

            $query->execute();
            $results = $query->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new \Exception("SQL Exception: " . $e->getMessage(), (int)$e->getCode());
        }

        if (empty($results)) {
            return [];
        }

        foreach ($results as $row) {
            $leave = new Leave();
            $leave->setId($row['Leave_id']);
            $leave->setEmployeeId($row['Leave_employee_id']);
            $leave->setYear($row['Leave_year']);
            $leave->setDays($row['Leave_days']);
            $leave->setStartDate($row['Leave_start_date']);
            $leave->setEndDate($row['Leave_end_date']);
            $leave->setCreated($row['Leave_created']);
            $leave->setModified($row['Leave_modified']);
            $leave->setNote($row['Leave_note']);
            $leave->setEtat($row['Leave_etat']);

            if ($joinEmployee) {
                $employee = new Employee();
                $employee->setId($row['Employee_id']);
                $employee->setFirstName($row['Employee_first_name']);
                $employee->setLastName($row['Employee_last_name']);
                $employee->setEmail($row['Employee_email']);

                $leave->setEmployee($employee);
            }

            $leaves[] = $leave;
        }

        return $leaves;
    }

    /**
     * Get all leaves in a period
     *
     * @param string  $start Start date
     * @param string  $end End date
     * @param boolean $countAll whether to count all leaves or not
     * @param boolean $joinEmployee Wheter to join employees or not
     * @return int|Leave[] Return period leaves array or number of period leaves
     */
    public function getByPeriod($start, $end, bool $countAll = false, bool $joinEmployee = true, $skip = [])
    {
        $results = [];
        $leaves = [];
        $start = DateHelper::toTimestamp($start);
        $end = DateHelper::toTimestamp($end);

        $select = "SELECT lv.id AS Leave_id, lv.employee_id AS Leave_employee_id, lv.year AS Leave_year, lv.days AS Leave_days, "
            . "lv.start_date AS Leave_start_date, lv.end_date AS Leave_end_date, lv.created AS Leave_created, lv.modified AS Leave_modified, "
            . "lv.note AS Leave_note, lv.etat AS Leave_etat ";
        $from = ' FROM leaves lv ';
        $join = '';
        $where = ' WHERE lv.etat = :etat AND ((DATE(lv.start_date) BETWEEN :start_date AND :end_date) OR (DATE(lv.end_date) BETWEEN :start_date AND :end_date))';

        if (!empty($skip)) {
            $where .= " AND lv.id NOT IN (" . implode(',', $skip) . ") ";
        }

        $order = " ORDER BY lv.start_date ASC, lv.end_date DESC ";

        if ($joinEmployee) {
            $select .= " , e.id AS Employee_id, e.first_name AS Employee_first_name, e.last_name AS Employee_last_name, e.email AS Employee_email ";
            $join .= " INNER JOIN employees e ON e.id = lv.employee_id ";
        }

        $sql = $select . $from . $join . $where . $order;

        if ($countAll) {
            $sql = 'SELECT count(*) AS count FROM leaves lv ' . $where;
        }

        try {
            $query = $this->connectionManager->getConnection()->prepare($sql);

            $query->bindValue(':etat', 1, \PDO::PARAM_BOOL);
            $query->bindValue(':start_date', date('Y-m-d', $start), \PDO::PARAM_STR);
            $query->bindValue(':end_date', date('Y-m-d', $end), \PDO::PARAM_STR);

            $query->execute();

            if ($countAll) {
                $result = $query->fetch(\PDO::FETCH_ASSOC);
                return $result['count'];
            }

            $results = $query->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new \Exception("SQL Exception: " . $e->getMessage(), (int)$e->getCode());
        }

        if (empty($results)) {
            return [];
        }

        foreach ($results as $row) {
            $leave = new Leave();
            $leave->setId($row['Leave_id']);
            $leave->setEmployeeId($row['Leave_employee_id']);
            $leave->setYear($row['Leave_year']);
            $leave->setDays($row['Leave_days']);
            $leave->setStartDate($row['Leave_start_date']);
            $leave->setEndDate($row['Leave_end_date']);
            $leave->setCreated($row['Leave_created']);
            $leave->setModified($row['Leave_modified']);
            $leave->setNote($row['Leave_note']);
            $leave->setEtat($row['Leave_etat']);

            if ($joinEmployee) {
                $employee = new Employee();
                $employee->setId($row['Employee_id']);
                $employee->setFirstName($row['Employee_first_name']);
                $employee->setLastName($row['Employee_last_name']);
                $employee->setEmail($row['Employee_email']);

                $leave->setEmployee($employee);
            }

            $leaves[] = $leave;
        }

        return $leaves;
    }

    /**
     * Get leaves by month
     *
     * @param string $month Month to be considered
     * @param bool $countAll Wheter to return count of all leaves
     * @param bool $joinEmployee Wheter to join employees or not
     * @return int|Leave[] Return month's leaves or count of them
     */
    public function getByMonth(string $month, bool $countAll = false, bool $joinEmployee = true)
    {
        $results = [];
        $leaves = [];

        $select = "SELECT lv.id AS Leave_id, lv.employee_id AS Leave_employee_id, lv.year AS Leave_year, lv.days AS Leave_days, "
            . "lv.start_date AS Leave_start_date, lv.end_date AS Leave_end_date, lv.created AS Leave_created, lv.modified AS Leave_modified, "
            . "lv.note AS Leave_note, lv.etat AS Leave_etat ";
        $from = ' FROM leaves lv ';
        $join = '';
        $where = ' WHERE lv.etat = :etat AND (DATE(lv.start_date) <= :dateFrom AND DATE(lv.end_date) >= :dateTo)';
        $order = " ORDER BY lv.start_date ASC, lv.end_date DESC ";

        if ($joinEmployee) {
            $select .= " , e.id AS Employee_id, e.first_name AS Employee_first_name, e.last_name AS Employee_last_name, e.email AS Employee_email ";
            $join .= " INNER JOIN employees e ON e.id = lv.employee_id ";
        }

        $sql = $select . $from . $join . $where . $order;

        if ($countAll) {
            $sql = "SELECT COUNT(*) AS month_nb_leaves FROM leaves lv WHERE lv.etat = :etat AND (DATE(lv.start_date) <= :dateFrom AND DATE(lv.end_date) >= :dateTo)";
        }

        try {
            $dateFrom = DateHelper::firstDayOfMonth($month);
            $dateTo = DateHelper::lastDayOfMonth($month);

            $query = $this->connectionManager->getConnection()->prepare($sql);

            $query->bindValue(':etat', 1, \PDO::PARAM_BOOL);
            $query->bindValue(':dateFrom', $dateFrom, \PDO::PARAM_STR);
            $query->bindValue(':dateTo', $dateTo, \PDO::PARAM_STR);

            $query->execute();

            if ($countAll) {
                $result = $query->fetch(\PDO::FETCH_ASSOC);
                return $result['month_nb_leaves'];
            }

            $results = $query->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new \Exception("SQL Exception: " . $e->getMessage(), (int)$e->getCode());
        }

        if (empty($results)) {
            return [];
        }

        foreach ($results as $row) {
            $leave = new Leave();
            $leave->setId($row['Leave_id']);
            $leave->setEmployeeId($row['Leave_employee_id']);
            $leave->setYear($row['Leave_year']);
            $leave->setDays($row['Leave_days']);
            $leave->setStartDate($row['Leave_start_date']);
            $leave->setEndDate($row['Leave_end_date']);
            $leave->setCreated($row['Leave_created']);
            $leave->setModified($row['Leave_modified']);
            $leave->setNote($row['Leave_note']);
            $leave->setEtat($row['Leave_etat']);

            if ($joinEmployee) {
                $employee = new Employee();
                $employee->setId($row['Employee_id']);
                $employee->setFirstName($row['Employee_first_name']);
                $employee->setLastName($row['Employee_last_name']);
                $employee->setEmail($row['Employee_email']);

                $leave->setEmployee($employee);
            }

            $leaves[] = $leave;
        }

        return $leaves;
    }

    /**
     * Get leave by id
     *
     * @param int  $leave_id Leave id
     * @param bool $joinEmployee Wheter to join employees or not
     * @return Leave|null
     */
    public function get($leave_id, bool $joinEmployee = true): ?Leave
    {
        $select = "SELECT lv.id AS Leave_id, lv.employee_id AS Leave_employee_id, lv.year AS Leave_year, lv.days AS Leave_days, "
            . "lv.start_date AS Leave_start_date, lv.end_date AS Leave_end_date, lv.created AS Leave_created, lv.modified AS Leave_modified, "
            . "lv.note AS Leave_note, lv.etat AS Leave_etat ";
        $from = ' FROM leaves lv ';
        $join = '';
        $where = ' WHERE lv.etat = :etat AND lv.id = :id';

        if ($joinEmployee) {
            $select .= " , e.id AS Employee_id, e.first_name AS Employee_first_name, e.last_name AS Employee_last_name, e.email AS Employee_email ";
            $join .= " INNER JOIN employees e ON e.id = lv.employee_id ";
        }

        $sql = $select . $from . $join . $where;

        try {
            $query = $this->connectionManager->getConnection()->prepare($sql);

            $query->bindValue(':etat', 1, \PDO::PARAM_BOOL);
            $query->bindValue(':id', $leave_id, \PDO::PARAM_INT);

            $query->execute();
            $results = $query->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new \Exception("SQL Exception: " . $e->getMessage(), (int)$e->getCode());
        }

        if (empty($results)) {
            return null;
        }

        $leave = new Leave();
        $leave->setId($results['Leave_id']);
        $leave->setEmployeeId($results['Leave_employee_id']);
        $leave->setYear($results['Leave_year']);
        $leave->setDays($results['Leave_days']);
        $leave->setStartDate($results['Leave_start_date']);
        $leave->setEndDate($results['Leave_end_date']);
        $leave->setCreated($results['Leave_created']);
        $leave->setModified($results['Leave_modified']);
        $leave->setNote($results['Leave_note']);
        $leave->setEtat($results['Leave_etat']);

        if ($joinEmployee) {
            $employee = new Employee();
            $employee->setId($results['Employee_id']);
            $employee->setFirstName($results['Employee_first_name']);
            $employee->setLastName($results['Employee_last_name']);
            $employee->setEmail($results['Employee_email']);

            $leave->setEmployee($employee);
        }

        return $leave;
    }

    /**
     * Get remaining time (in days) before an employee can take a leave
     * 
     * @param int $employeeId Employee Id
     * @return int The number of days remaining. If the value of the 
     * config `LM_LEAVE_MATURATION_NB_DAYS` is `0`, the function returns 0. 
     * If there's no contract related to the employee, the method returns 0
     */
    public function getRemainingMaturationTime(int $employeeId): int
    {
        $leavesConfig = $this->configsServices->getByCode('LM_LEAVE_MATURATION_NB_DAYS');
        $maturationNbDays = intval($leavesConfig->getValue());

        if (is_null($leavesConfig) || $maturationNbDays === 0) {
            return 0;
        }

        $contractServices = new ContractsServices();
        $employeeContract = $contractServices->getByEmployeeId($employeeId);

        if (is_null($employeeContract)) {
            return 0;
        }

        $nbDays = DateUtils::differenceInDays(date('Y-m-d H:i:s'), $employeeContract->getStartDate());

        if ($nbDays > $maturationNbDays) {
            return 0;
        }

        return $maturationNbDays - $nbDays;
    }

    /**
     * Get total leave days spent by an employee
     *
     * @param int       $employeeId Employee id
     * @param int|null  $year Year to be considered
     * @param array     $skip Array of ids to be skipped
     * @return int      Returns the number of spent days
     */
    public function getSpentDays($employeeId, ?int $year = null, $skip = []): int
    {
        $nb_spent_days = 0;
        $results = [];
        $permissionsServices = new PermissionRequestsServices();

        $sql = "SELECT lv.id, lv.start_date, lv.end_date FROM leaves lv WHERE lv.employee_id = :employee_id AND lv.etat = :etat AND lv.year = :year";

        if (is_null($year)) {
            $year = date('Y');
        }

        if (!empty($skip)) {
            $sql .= " AND lv.id NOT IN (:exclude) ";
        }

        try {
            $query = $this->connectionManager->getConnection()->prepare($sql);

            $query->bindValue(':employee_id', $employeeId, \PDO::PARAM_INT);
            $query->bindValue(':etat', 1, \PDO::PARAM_BOOL);
            $query->bindValue(':year', $year, \PDO::PARAM_INT);
            if (!empty($skip)) {
                $query->bindValue(':exclude', implode(',', $skip), \PDO::PARAM_INT);
            }

            $query->execute();
            $results = $query->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new \Exception("SQL Exception: " . $e->getMessage(), (int)$e->getCode());
        }

        if (!empty($results)) {
            foreach ($results as $row) {
                $leaveWorkingDays = $this->getWorkingDays($row['start_date'], $row['end_date']);

                if ($leaveWorkingDays > 0) {
                    $nb_spent_days += $leaveWorkingDays;
                }
            }
        }

        // Add days spent on permissions
        $leavesConfig = $this->configsServices->getByCode('LM_PERMISSION_REDUCE_LEAVE');
        if ($leavesConfig->getValue() == 'OUI') {
            $nb_spent_days_in_permissions = $permissionsServices->getSentDays($employeeId, $year);
            if ($nb_spent_days_in_permissions > 0) {
                $nb_spent_days += $nb_spent_days_in_permissions;
            }
        }

        return (int)$nb_spent_days;
    }

    /**
     * Compute working days within a period
     *
     * @param mixed  $dateFrom Start date
     * @param mixed  $dateTo End date
     * @param string $year Year to consider
     * @return int Returns the number of working days
     */
    public function getWorkingDays($dateFrom, $dateTo)
    {
        // Get holidays
        $holidays = [];
        $holidaysConfig = $this->configsServices->getByCode('LM_HOLIDAYS');

        $startYear = date('Y', DateHelper::toTimestamp($dateFrom));
        $endYear = date('Y', DateHelper::toTimestamp($dateTo));

        if (intval($startYear) < intval($endYear)) {
            $startYearHolidays = explode(',', str_replace('*', $startYear, $holidaysConfig->getValue()));
            $endYearHolidays = [];
            for ($i = 1; $i <= intval($endYear) - intval($startYear); $i++) {
                $year = intval($startYear) + $i;
                $endYearHolidays = array_unique(
                    array_merge(explode(',', str_replace('*', "$year", $holidaysConfig->getValue())))
                );
            }
            $holidays = array_unique(array_merge($startYearHolidays, $endYearHolidays));
        } else {
            $holidays = explode(',', str_replace('*', $startYear, $holidaysConfig->getValue()));
        }

        // Get working days
        $workingDays = [];
        $workingDaysConfigs = $this->configsServices->getByCode('LM_WORKING_DAYS');
        $workingDays = DateHelper::daysNumbers(explode(',', $workingDaysConfigs->getValue()));

        $periodWorkingDays = DateHelper::getWorkingDays($dateFrom, $dateTo, $holidays, $workingDays);

        return $periodWorkingDays;
    }

    /**
     * Get leaves by employee id and year
     *
     * @param int           $employeeId Employee id
     * @param int|null      $year Year to be considered
     * @param bool          $joinEmployee Wheter to join employee or not
     * @param bool          $countAll Wheter to return count of leaves or not.
     * @return Leave[]|int  Array of Leaves or number of Leaves.
     */
    public function getByEmployeeId($employeeId, int $year = null, bool $joinEmployee = false, bool $countAll = false, $skip = [])
    {
        $results = [];
        $leaves = [];

        $select = "SELECT lv.id AS Leave_id, lv.employee_id AS Leave_employee_id, lv.year AS Leave_year, lv.days AS Leave_days, "
            . "lv.start_date AS Leave_start_date, lv.end_date AS Leave_end_date, lv.created AS Leave_created, lv.modified AS Leave_modified, "
            . "lv.note AS Leave_note, lv.etat AS Leave_etat ";
        $from = ' FROM leaves lv ';
        $join = '';
        $where = ' WHERE lv.etat = :etat AND lv.year = :year AND lv.employee_id = :employee_id';

        if (!empty($skip)) {
            $where .= " AND lv.id NOT IN (" . implode(',', $skip) . ") ";
        }

        $order = " ORDER BY lv.start_date ASC, lv.end_date DESC ";

        if ($joinEmployee) {
            $select .= " , e.id AS Employee_id, e.first_name AS Employee_first_name, e.last_name AS Employee_last_name, e.email AS Employee_email ";
            $join .= " INNER JOIN employees e ON e.id = lv.employee_id ";
        }

        $sql = $select . $from . $join . $where . $order;

        if ($countAll) {
            $sql = "SELECT COUNT(*) AS employee_nb_leaves FROM leaves lv " . $where;
        }

        if (is_null($year)) {
            $year = date('Y');
        }

        try {

            $query = $this->connectionManager->getConnection()->prepare($sql);

            $query->bindValue(':etat', 1, \PDO::PARAM_BOOL);
            $query->bindValue(':year', $year, \PDO::PARAM_INT);
            $query->bindValue(':employee_id', $employeeId, \PDO::PARAM_INT);

            $query->execute();

            if ($countAll) {
                $result = $query->fetch(\PDO::FETCH_ASSOC);
                return $result['employee_nb_leaves'];
            }

            $results = $query->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new \Exception("SQL Exception: " . $e->getMessage(), (int)$e->getCode());
        }

        if (empty($results)) {
            return [];
        }

        foreach ($results as $row) {
            $leave = new Leave();
            $leave->setId($row['Leave_id']);
            $leave->setEmployeeId($row['Leave_employee_id']);
            $leave->setYear($row['Leave_year']);
            $leave->setDays($row['Leave_days']);
            $leave->setStartDate($row['Leave_start_date']);
            $leave->setEndDate($row['Leave_end_date']);
            $leave->setCreated($row['Leave_created']);
            $leave->setModified($row['Leave_modified']);
            $leave->setNote($row['Leave_note']);
            $leave->setEtat($row['Leave_etat']);

            if ($joinEmployee) {
                $employee = new Employee();
                $employee->setId($row['Employee_id']);
                $employee->setFirstName($row['Employee_first_name']);
                $employee->setLastName($row['Employee_last_name']);
                $employee->setEmail($row['Employee_email']);

                $leave->setEmployee($employee);
            }

            $leaves[] = $leave;
        }

        return $leaves;
    }

    /**
     * Check if a employee is in currently in vaccations
     *
     * @param int    $employeeId Employee id
     * @return bool  Returns true if the employee is in vaccations, false otherwise
     */
    public function isEmployeeInLeave($employeeId)
    {
        $sql = "SELECT * FROM leaves lv WHERE lv.etat = :etat AND lv.year = :year AND lv.employee_id = :employee_id "
            . " AND (DATE(lv.start_date) <= :date AND DATE(lv.end_date) >= :date)";

        try {

            $query = $this->connectionManager->getConnection()->prepare($sql);

            $query->bindValue(':etat', 1, \PDO::PARAM_BOOL);
            $query->bindValue(':year', date('Y'), \PDO::PARAM_INT);
            $query->bindValue(':employee_id', $employeeId, \PDO::PARAM_INT);
            $query->bindValue(':date', date('Y-m-d'), \PDO::PARAM_STR);

            $query->execute();

            $results = $query->fetchAll(\PDO::FETCH_ASSOC);

            if (empty($results)) {
                return false;
            }

            return true;
        } catch (\PDOException $e) {
            throw new \Exception("SQL Exception: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    /**
     * Add leave
     *
     * @param array|Leave $leave Leave to add
     * @return int|bool Returns the id of the leave if successfully added, false otherwise
     */
    public function add(array|Leave $leave): int|bool
    {
        if (is_array($leave)) {
            $leave = $this->toEntity($leave);
        }

        $days = $this->getWorkingDays($leave->getStartDate(), $leave->getEndDate());
        $leave->setDays($days);

        // Validation
        $errors = $leave->validation();
        if (!empty($errors)) {
            foreach ($errors as $error) {
                Flash::error($error);
            }

            Session::write('__formdata__', json_encode($_POST));

            return false;
        }

        // Check if another leave period coincides with the leave period
        if ($this->checkLeave($leave)) {
            Flash::error("La période de congé coïncide avec un autre congé pour cet employé.");

            Session::write('__formdata__', json_encode($_POST));

            return false;
        }

        $leave_nb_days = (int)$this->configsServices->getByCode('LM_LEAVE_NB_DAYS')->getValue();
        $overrideLeaveNbDays = $this->configsServices->getByCode('LM_OVERRIDE_LEAVE_NB_DAYS')->getValue();
        $sameTimeLeaves = (int)$this->configsServices->getByCode('LM_SAME_TIME_NB_LEAVE')->getValue();

        // Check if allowed leave days is reached
        if ($overrideLeaveNbDays == 'NON') {
            $nb_spent_days = $this->getSpentDays($leave->getEmployeeId(), (int)$leave->getYear());

            if ($days + $nb_spent_days > $leave_nb_days) {
                Flash::error("Le nombre de jours de congé alloué a été dépassé de " . ($nb_spent_days + $days - $leave_nb_days) . " jour(s)");

                Session::write('__formdata__', json_encode($_POST));

                return false;
            }
        }

        // Check if max employee in leave at the same time is reached
        if ($sameTimeLeaves > 0) {
            $nb_leaves = $this->getByPeriod($leave->getStartDate(), $leave->getEndDate(), true, false);

            if ($nb_leaves >= $sameTimeLeaves) {
                Flash::error("Le nombre maximun d'employé en congé est atteint pour la période choisie.");

                Session::write('__formdata__', json_encode($_POST));

                return false;
            }
        }

        $leave->setCreated(date('Y-m-d H:i:s'));
        $leave->setModified(null);
        $leave->setEtat(true);

        $sql = "INSERT INTO leaves (employee_id, year, days, start_date, end_date, created, modified, note, etat) VALUES (?,?,?,?,?,?,?,?,?)";

        try {
            $this->connectionManager->getConnection()->beginTransaction();
            $query = $this->connectionManager->getConnection()->prepare($sql);

            $query->bindValue(1, $leave->getEmployeeId(), \PDO::PARAM_INT);
            $query->bindValue(2, $leave->getYear(), \PDO::PARAM_INT);
            $query->bindValue(3, $leave->getDays(), \PDO::PARAM_INT);
            $query->bindValue(4, $leave->getStartDate(), \PDO::PARAM_STR);
            $query->bindValue(5, $leave->getEndDate(), \PDO::PARAM_STR);
            $query->bindValue(6, $leave->getCreated(), \PDO::PARAM_STR);
            $query->bindValue(7, $leave->getModified(), \PDO::PARAM_STR);
            $query->bindValue(8, $leave->getNote(), \PDO::PARAM_STR);
            $query->bindValue(9, $leave->getEtat(), \PDO::PARAM_BOOL);

            $query->execute();

            $leave_id = (int)$this->connectionManager->getConnection()->lastInsertId();

            $this->connectionManager->getConnection()->commit();

            return $leave_id;
        } catch (\PDOException $e) {
            $this->connectionManager->getConnection()->rollBack();

            throw new \Exception("SQL Exception: " . $e->getMessage(), 1);
        }
    }

    /**
     * Update leave
     *
     * @param array|Leave $leave Leave to update
     * @return bool Returns true if the leave has been updated, false otherwise
     */
    public function update(array|Leave $leave): bool
    {
        if (is_array($leave)) {
            $leave = $this->toEntity($leave);
        }

        // Check if the leave exists
        $existedLeave = $this->get($leave->getId());
        if (empty($existedLeave)) {
            Flash::error("Aucun contrat trouvé avec l'id " . $leave->getId());

            return false;
        }

        // Check if another leave period coincides with the leave period
        if ($this->checkLeave($leave)) {
            Flash::error("La période de congé coïncide avec un autre congé pour cet employé.");

            Session::write('__formdata__', json_encode($_POST));

            return false;
        }

        if (
            !empty($leave->getStartDate()) && $leave->getStartDate() != $existedLeave->getStartDate()
            || !empty($leave->getEndDate()) && $leave->getEndDate() != $existedLeave->getEndDate()
        ) {
            $days = $this->getWorkingDays($leave->getStartDate(), $leave->getEndDate());
            $existedLeave->setDays($days);

            $leave_nb_days = (int)$this->configsServices->getByCode('LM_LEAVE_NB_DAYS')->getValue();
            $overrideLeaveNbDays = $this->configsServices->getByCode('LM_OVERRIDE_LEAVE_NB_DAYS')->getValue();
            $sameTimeLeaves = (int)$this->configsServices->getByCode('LM_SAME_TIME_NB_LEAVE')->getValue();

            // Check if allowed leave days is reached
            if ($overrideLeaveNbDays == 'NON') {
                $nb_spent_days = $this->getSpentDays($existedLeave->getEmployeeId(), (int)$existedLeave->getYear(), [$existedLeave->getId()]);

                if ($days + $nb_spent_days > $leave_nb_days) {
                    Flash::error("Le nombre de jours de congé alloué a été dépassé de " . ($nb_spent_days + $days - $leave_nb_days) . " jour(s)");

                    Session::write('__formdata__', json_encode($_POST));

                    return false;
                }
            }

            // Check if max employee in leave at the same time is reached
            if ($sameTimeLeaves > 0) {
                $nb_leaves = $this->getByPeriod($leave->getStartDate(), $leave->getEndDate(), true, false, [$existedLeave->getId()]);

                if ($nb_leaves >= $sameTimeLeaves) {
                    Flash::error("Le nombre maximun d'employé en congé est atteint pour la période choisie.");

                    Session::write('__formdata__', json_encode($_POST));

                    return false;
                }
            }
        }

        if (!empty($leave->getStartDate())) {
            $existedLeave->setStartDate($leave->getStartDate());
        }
        if (!empty($leave->getEndDate())) {
            $existedLeave->setEndDate($leave->getEndDate());
        }
        if (!empty($leave->getNote())) {
            $existedLeave->setNote($leave->getNote());
        }

        $existedLeave->setModified(date('Y-m-d H:i:s'));

        // Validation
        $errors = $existedLeave->validation();
        if (!empty($errors)) {
            foreach ($errors as $error) {
                Flash::error($error);
            }

            Session::write('__formdata__', json_encode($_POST));

            return false;
        }

        $sql = "UPDATE leaves SET days = :days, start_date = :start_date, end_date = :end_date, note = :note, modified = :modified WHERE id = :id";

        try {
            $query = $this->connectionManager->getConnection()->prepare($sql);

            $query->bindValue(':id', $existedLeave->getId(), \PDO::PARAM_INT);
            $query->bindValue(':days', $existedLeave->getDays(), \PDO::PARAM_INT);
            $query->bindValue(':start_date', $existedLeave->getStartDate(), \PDO::PARAM_STR);
            $query->bindValue(':end_date', $existedLeave->getEndDate(), \PDO::PARAM_STR);
            $query->bindValue(':note', $existedLeave->getNote(), \PDO::PARAM_STR);
            $query->bindValue(':modified', $existedLeave->getModified(), \PDO::PARAM_STR);

            $updated = $query->execute();

            return $updated;
        } catch (\PDOException $e) {
            throw new \Exception("SQL Exception: " . $e->getMessage(), 1);
        }
    }

    /**
     * Delete a leave
     *
     * @param int $id Leave id
     * @return bool Returns true if the leave was deleted, false otherwise
     */
    public function delete($id): bool
    {
        // Check if the leave exists
        $existedLeave = $this->get($id, false);
        if (empty($existedLeave)) {
            Flash::error("Aucun congé trouvé avec l'id " . $id);

            return false;
        }

        $sql = "UPDATE leaves SET etat = :etat WHERE id = :id";

        try {
            $query = $this->connectionManager->getConnection()->prepare($sql);

            $query->bindValue(':etat', false, \PDO::PARAM_BOOL);
            $query->bindValue(':id', $id, \PDO::PARAM_INT);

            $deleted = $query->execute();

            return $deleted;
        } catch (\PDOException $e) {
            throw new \Exception("SQL Exception: " . $e->getMessage(), 1);
        }
    }

    /**
     * Get all years for permissions
     *
     * @return array Array of years
     */
    public function getYears()
    {
        $sql = "SELECT DISTINCT(year) as year FROM leaves";

        try {
            $query = $this->connectionManager->getConnection()->prepare($sql);
            $query->execute();

            $result = $query->fetchAll(\PDO::FETCH_ASSOC);

            return $result;
        } catch (\PDOException $e) {
            throw new \Exception("SQL Exception: " . $e->getMessage(), 1);
        }
    }

    /**
     * Check if leave period coincides with another leave
     * 
     * @param Leave $leave Leave to check
     * @return bool Returns true if leave period coincides with another leave, false otherwise
     */
    public function checkLeave(Leave $leave): bool
    {
        $existed = true;
        $sql = "SELECT * FROM leaves lv WHERE lv.etat = :etat AND lv.employee_id = :employee_id AND lv.year = :year "
            . "AND ((DATE(lv.start_date) <= :start_date AND DATE(lv.end_date) >= :start_date) OR (DATE(lv.start_date) <= :end_date AND DATE(lv.end_date) >= :start_date))";

        if (!empty($leave->getId())) {
            $sql .= ' AND lv.id != :leave_id';
        }

        try {
            $query = $this->connectionManager->getConnection()->prepare($sql);

            $query->bindValue(':etat', 1, \PDO::PARAM_BOOL);
            $query->bindValue(':year', $leave->getYear(), \PDO::PARAM_INT);
            $query->bindValue(':employee_id', $leave->getEmployeeId(), \PDO::PARAM_INT);
            $query->bindValue(':start_date', $leave->getStartDate(), \PDO::PARAM_STR);
            $query->bindValue(':end_date', $leave->getEndDate(), \PDO::PARAM_STR);
            if (!empty($leave->getId())) {
                $query->bindValue(':leave_id', $leave->getId(), \PDO::PARAM_INT);
            }

            $query->execute();

            $results = $query->fetchAll(\PDO::FETCH_ASSOC);

            $existed = !empty($results);
            return $existed;
        } catch (\PDOException $e) {
            throw new \Exception("SQL Exception: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    /**
     * Parse array to Leave object
     *
     * @param array $data Data to parse
     * @return Leave|null Returns parsed object or null
     */
    public function toEntity(array $data): ?Leave
    {
        $id = !empty($data['id']) ? $data['id'] : null;
        $employee_id = !empty($data['employee_id']) ? $data['employee_id'] : null;
        $year = !empty($data['year']) ? $data['year'] : null;
        $days = !empty($data['days']) ? $data['days'] : null;
        $start_date = !empty($data['start_date']) ? $data['start_date'] : null;
        $end_date = !empty($data['end_date']) ? $data['end_date'] : null;
        $created = !empty($data['created']) ? $data['created'] : null;
        $modified = !empty($data['modified']) ? $data['id'] : null;
        $note = !empty($data['note']) ? $data['note'] : null;
        $etat = !empty($data['etat']) ? $data['etat'] : null;
        $employee = !empty($data['employee']) ? $data['employee'] : null;

        $leave = new Leave();
        $leave->setId($id);
        $leave->setEmployeeId($employee_id);
        $leave->setYear($year);
        $leave->setDays($days);
        $leave->setStartDate($start_date);
        $leave->setEndDate($end_date);
        $leave->setCreated($created);
        $leave->setModified($modified);
        $leave->setNote($note);
        $leave->setEtat($etat);
        $leave->setEmployee($employee);

        return $leave;
    }
}