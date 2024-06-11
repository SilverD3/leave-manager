<?php

declare(strict_types=1);

/**
 * Leave manager : Simple app for contract and leave management.
 *
 * @copyright Copyright (c) Silevester D. (https://github.com/SilverD3)
 * @link      https://github.com/SilverD3/leave-manager Leave Manager Project
 * @since     2.0 (2024)
 */

namespace App\Service;

use App\Entity\InternshipType;

require_once dirname(dirname(__DIR__)) . DS . DS . 'autoload.php';

use Core\Database\ConnectionManager;
use App\Entity\Employee;
use App\Entity\Internship;
use Core\FlashMessages\Flash;
use Core\Utils\Session;

/**
 * Internships Services
 */
class InternshipsServices
{
    private $connectionManager;

    function __construct()
    {
        $this->connectionManager = new ConnectionManager();
    }

    /**
     * Get internships
     * 
     * @param string $status Status to consider
     * @param string|null $keywords Filter by name
     * 
     * @return array<Internship> Array containing passed internships
     */
    function getAll(string $status = 'all', ?string $keywords = null)
    {
        $results = [];

        $select = "SELECT itn.id AS Internship_id, itn.internship_type_id AS Internship_internship_type_id, itn.supervisor AS Internship_supervisor, "
            . "itn.first_name AS Internship_first_name, itn.last_name AS Internship_last_name, itn.sex AS Internship_sex, itn.birthdate AS Internship_birthdate, "
            . "itn.school_name AS Internship_school_name, itn.start_date AS Internship_start_date, itn.end_date AS Internship_end_date, "
            . "itn.report AS Internship_report, itn.status AS Internship_status, itn.user_id AS Internship_user_id, itn.created AS Internship_created, "
            . "itn.modified AS Internship_modified, itn.etat AS Internship_etat,";

        $select .= " sp.id AS Supervisor_id, sp.first_name AS Supervisor_first_name, sp.last_name AS Supervisor_last_name, sp.username AS Supervisor_username,";
        $select .= " it.id AS InternshipType_id, it.title AS InternshipType_title, it.description AS InternshipType_description";

        $from = " FROM internships itn";

        $join = " LEFT JOIN employees sp ON sp.id = itn.supervisor";
        $join .= " INNER JOIN internship_types it ON it.id = itn.internship_type_id";

        $where = " WHERE itn.etat = :etat";

        if ($status != 'all') {
            $where .= " AND itn.status = :status ";
        }

        if (!empty($keywords)) {
            $where .= " AND CONCAT(itn.first_name, itn.last_name) LIKE '%:keywords%' ";
        }

        $sql = $select . $from . $join . $where;

        try {
            $query = $this->connectionManager->getConnection()->prepare($sql);

            $query->bindValue(':etat', 1, \PDO::PARAM_BOOL);

            if ($status != 'all') {
                $query->bindParam(':status', $status, \PDO::PARAM_STR);
            }

            if (!empty($keywords)) {
                $query->bindParam(':keywords', $keywords, \PDO::PARAM_STR);
            }

            $query->execute();

            $results = $query->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new \Exception("SQL Exception: " . $e->getMessage(), (int)$e->getCode());
        }

        if (empty($results)) {
            return [];
        }

        return call_user_func_array($this->getMapper(), [$results]);
    }


    /**
     * Get all passed internships
     * 
     * @param string|null $keywords To filter by name
     * 
     * @return array<Internship> Array containing passed internships
     */
    function getPassed(?string $keywords = null)
    {
        $results = [];

        $select = "SELECT itn.id AS Internship_id, itn.internship_type_id AS Internship_internship_type_id, itn.supervisor AS Internship_supervisor, "
            . "itn.first_name AS Internship_first_name, itn.last_name AS Internship_last_name, itn.sex AS Internship_sex, itn.birthdate AS Internship_birthdate, "
            . "itn.school_name AS Internship_school_name, itn.start_date AS Internship_start_date, itn.end_date AS Internship_end_date, "
            . "itn.report AS Internship_report, itn.status AS Internship_status, itn.user_id AS Internship_user_id, itn.created AS Internship_created, "
            . "itn.modified AS Internship_modified, itn.etat AS Internship_etat,";

        $select .= " sp.id AS Supervisor_id, sp.first_name AS Supervisor_first_name, sp.last_name AS Supervisor_last_name, sp.username AS Supervisor_username,";
        $select .= " it.id AS InternshipType_id, it.title AS InternshipType_title, it.description AS InternshipType_description";

        $from = " FROM internships itn";

        $join = " LEFT JOIN employees sp ON sp.id = itn.supervisor";
        $join .= " INNER JOIN internship_types it ON it.id = itn.internship_type_id";

        $where = " WHERE itn.etat = :etat AND itn.status = :status AND itn.end_date < :end_date";

        if (!empty($keywords)) {
            $where .= " AND CONCAT(itn.first_name, itn.last_name) LIKE '%:keywords%' ";
        }

        $sql = $select . $from . $join . $where;

        try {
            $query = $this->connectionManager->getConnection()->prepare($sql);

            $query->bindValue(':etat', 1, \PDO::PARAM_BOOL);
            $query->bindValue(':status', 'active', \PDO::PARAM_STR);
            $query->bindValue(':end_date', date('Y-m-d'), \PDO::PARAM_STR);

            if (!empty($keywords)) {
                $query->bindParam(':keywords', $keywords, \PDO::PARAM_STR);
            }

            $query->execute();

            $results = $query->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new \Exception("SQL Exception: " . $e->getMessage(), (int)$e->getCode());
        }

        if (empty($results)) {
            return [];
        }

        return call_user_func_array($this->getMapper(), [$results]);
    }

    /**
     * Get user's internships
     * 
     * @param string|int $userId User id
     * 
     * @return array<Internship> Array containing user's internships
     */
    function getByUserId(int|string $userId)
    {
        $results = [];

        $select = "SELECT itn.id AS Internship_id, itn.internship_type_id AS Internship_internship_type_id, itn.supervisor AS Internship_supervisor, "
            . "itn.first_name AS Internship_first_name, itn.last_name AS Internship_last_name, itn.sex AS Internship_sex, itn.birthdate AS Internship_birthdate, "
            . "itn.school_name AS Internship_school_name, itn.start_date AS Internship_start_date, itn.end_date AS Internship_end_date, "
            . "itn.report AS Internship_report, itn.status AS Internship_status, itn.user_id AS Internship_user_id, itn.created AS Internship_created, "
            . "itn.modified AS Internship_modified, itn.etat AS Internship_etat,";

        $select .= " sp.id AS Supervisor_id, sp.first_name AS Supervisor_first_name, sp.last_name AS Supervisor_last_name, sp.username AS Supervisor_username,";
        $select .= " it.id AS InternshipType_id, it.title AS InternshipType_title, it.description AS InternshipType_description";

        $from = " FROM internships itn";

        $join = " LEFT JOIN employees sp ON sp.id = itn.supervisor";
        $join .= " INNER JOIN internship_types it ON it.id = itn.internship_type_id";

        $where = " WHERE itn.etat = :etat AND user_id = :user_id";

        $sql = $select . $from . $join . $where;

        try {
            $query = $this->connectionManager->getConnection()->prepare($sql);

            $query->bindValue(':etat', 1, \PDO::PARAM_BOOL);
            $query->bindParam(':user_id', $userId, \PDO::PARAM_INT);

            $query->execute();

            $results = $query->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new \Exception("SQL Exception: " . $e->getMessage(), (int)$e->getCode());
        }

        if (empty($results)) {
            return [];
        }

        return call_user_func_array($this->getMapper(), [$results]);
    }

    /**
     * Count passed internships
     * 
     * @return int The number of passed internships
     */
    public function countPassed(): int
    {
        $count = 0;

        $sql = "SELECT COUNT(*) AS count FROM internships i WHERE i.etat = :etat AND i.status = :status AND i.end_date < :end_date";

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
     * Count all internships
     * 
     * @param string $status Status to be considered
     * 
     * @return int The number of internships
     */
    public function countAll(string $status = 'all'): int
    {
        $count = 0;

        $sql = "SELECT COUNT(*) AS count FROM interhnships i WHERE i.etat = :etat";
        if ($status != 'all') {
            $sql .= " AND i.status = :status";
        }

        try {
            $query = $this->connectionManager->getConnection()->prepare($sql);

            $query->bindValue(':etat', 1, \PDO::PARAM_BOOL);
            if ($status != 'all') {
                $query->bindValue(':status', $status, \PDO::PARAM_STR);
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
     * Get internship by id
     * 
     * @param int|string $id The internship ID
     * 
     * @return null|\App\Entity\Internship The internship that matches the id
     */
    function get(int|string $id)
    {
        $results = [];

        $select = "SELECT itn.id AS Internship_id, itn.internship_type_id AS Internship_internship_type_id, itn.supervisor AS Internship_supervisor, "
            . "itn.first_name AS Internship_first_name, itn.last_name AS Internship_last_name, itn.sex AS Internship_sex, itn.birthdate AS Internship_birthdate, "
            . "itn.school_name AS Internship_school_name, itn.start_date AS Internship_start_date, itn.end_date AS Internship_end_date, "
            . "itn.report AS Internship_report, itn.status AS Internship_status, itn.user_id AS Internship_user_id, itn.created AS Internship_created, "
            . "itn.modified AS Internship_modified, itn.etat AS Internship_etat,";

        $select .= " sp.id AS Supervisor_id, sp.first_name AS Supervisor_first_name, sp.last_name AS Supervisor_last_name, sp.username AS Supervisor_username,";
        $select .= " it.id AS InternshipType_id, it.title AS InternshipType_title, it.description AS InternshipType_description";

        $from = " FROM internships itn";

        $join = " LEFT JOIN employees sp ON sp.id = itn.supervisor";
        $join .= " INNER JOIN internship_types it ON it.id = itn.internship_type_id";

        $where = " WHERE itn.etat = :etat AND itn.id = :id";

        $sql = $select . $from . $join . $where;

        try {
            $query = $this->connectionManager->getConnection()->prepare($sql);

            $query->bindValue(':etat', 1, \PDO::PARAM_BOOL);
            $query->bindValue(':id', intval($id), \PDO::PARAM_INT);

            $query->execute();

            $results = $query->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new \Exception("SQL Exception: " . $e->getMessage(), (int)$e->getCode());
        }

        if (empty($results)) {
            return null;
        }

        /**
         * @var array<Internship> $mappedResults
         */
        $mappedResults = call_user_func_array($this->getMapper(), [$results]);

        return reset($mappedResults);
    }

    /**
     * Add internship
     *
     * @param array|Internship $rawInternship Internship to save
     * @return int|bool Returns the id of the internship if successful, false otherwise.
     */
    public function add(array|Internship $rawInternship): int|bool
    {
        if (is_array($rawInternship)) {
            $internship = $this->toEntity($rawInternship);
        } else {
            $internship = $rawInternship;
        }

        if ($this->checkInternshipDocument($internship)) {
            Flash::error("Un stage pour la même personne et pour la même période existe déjà.");

            Session::write('__formdata__', json_encode($_POST));

            return false;
        }

        $errors = $internship->validation();
        if (!empty($errors)) {
            foreach ($errors as $error) {
                Flash::error($error);
            }

            Session::write('__formdata__', json_encode($_POST));

            return false;
        }

        if (strtotime($internship->getStartDate()) < time()) {
            $internship->setStatus('active');
        } else {
            $internship->setStatus('pending');
        }

        $internship->setCreated(date('Y-m-d H:i:s'));
        $internship->setModified(null);
        $internship->setEtat(true);

        $sql = "INSERT INTO internships(internship_type_id, supervisor, first_name, last_name, sex, birthdate, school_name, start_date, "
            . " end_date, report, status, user_id, created, modified, etat) VALUES (:internship_type_id, :supervisor, :first_name, :last_name, "
            . " :sex, :birthdate, :school_name, :start_date, :end_date, :report, :status, :user_id, :created, :modified, :etat)";

        $this->connectionManager->getConnection()->beginTransaction();

        try {

            $query = $this->connectionManager->getConnection()->prepare($sql);

            if (isset($rawInternship["create_user"]) && boolval($rawInternship["create_user"]) === true) {
                $usersService = new EmployeesServices();
                $userId = $usersService->add([
                    'first_name' => $rawInternship['first_name'],
                    'last_name' => $rawInternship['last_name'],
                    'email' => $rawInternship['email'],
                    'username' => $rawInternship['username'],
                    'password' => $rawInternship['password'],
                    'role_id' => 3,
                ]);

                if ($userId !== false) {
                    $internship->setUserId($userId);
                }
            }

            $query->bindValue(":internship_type_id", $internship->getInternshipTypeId(), \PDO::PARAM_INT);
            $query->bindValue(":supervisor", $internship->getSupervisorId(), \PDO::PARAM_INT);
            $query->bindValue(":first_name", $internship->getFirstName(), \PDO::PARAM_STR);
            $query->bindValue(":last_name", $internship->getLastName(), \PDO::PARAM_STR);
            $query->bindValue(":sex", $internship->getSex(), \PDO::PARAM_STR);
            $query->bindValue(":birthdate", $internship->getBirthdate(), \PDO::PARAM_STR);
            $query->bindValue(":school_name", $internship->getSchoolName(), \PDO::PARAM_STR);
            $query->bindValue(":start_date", $internship->getStartDate(), \PDO::PARAM_STR);
            $query->bindValue(":end_date", $internship->getEndDate(), \PDO::PARAM_STR);
            $query->bindValue(":report", $internship->getReportPath(), \PDO::PARAM_STR);
            $query->bindValue(":status", $internship->getStatus(), \PDO::PARAM_STR);
            $query->bindValue("user_id", $internship->getUserId(), \PDO::PARAM_INT);
            $query->bindValue("created", $internship->getCreated(), \PDO::PARAM_STR);
            $query->bindValue(":modified", $internship->getModified(), \PDO::PARAM_STR);
            $query->bindValue(":etat", $internship->getEtat(), \PDO::PARAM_BOOL);

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
     * Update internship
     *
     * @param array|Internship $internship Internship to update
     * @return int|bool Returns true if the internship has been updated, false otherwise.
     */
    public function update(array|Internship $internship): bool
    {
        if (is_array($internship)) {
            $internship = $this->toEntity($internship);
        }

        // Check if the internship exists
        $existedInternship = $this->get($internship->getId());
        if (empty($existedInternship)) {
            Flash::error("Aucun stage trouvé avec l'id " . $internship->getId());

            return false;
        }

        // Check if another internship with same data already exists
        if ($this->checkInternshipDocument($internship)) {
            Flash::error("Un stage pour la même personne et pour la même période existe déjà.");

            Session::write('__formdata__', json_encode($_POST));

            return false;
        }

        $existedInternship->setInternshipTypeId($internship->getInternshipTypeId());
        $existedInternship->setSupervisorId($internship->getSupervisorId());
        $existedInternship->setFirstName($internship->getFirstName());
        $existedInternship->setLastName($internship->getLastName());
        $existedInternship->setSex($internship->getSex());
        $existedInternship->setBirthdate($internship->getBirthdate());
        $existedInternship->setSchoolName($internship->getSchoolName());
        $existedInternship->setEndDate($internship->getEndDate());
        $existedInternship->setStartDate($internship->getStartDate());
        $existedInternship->setModified(date('Y-m-d H:i:s'));

        if (strtotime($existedInternship->getStartDate()) < time()) {
            $existedInternship->setStatus('active');
        } else {
            $existedInternship->setStatus('pending');
        }

        // Validation
        $errors = $existedInternship->validation();
        if (!empty($errors)) {
            foreach ($errors as $error) {
                Flash::error($error);
            }

            Session::write('__formdata__', json_encode($_POST));

            return false;
        }

        $sql = "UPDATE internships SET internship_type_id = :internship_type_id, supervisor = :supervisor, "
            . " first_name = :first_name, last_name = :last_name, sex = :sex, birthdate = :birthdate, "
            . " school_name = :school_name, start_date = :start_date, end_date = :end_date, modified = :modified, status = :status WHERE id = :id";

        try {
            $query = $this->connectionManager->getConnection()->prepare($sql);

            $query->bindValue(':internship_type_id', $existedInternship->getInternshipTypeId(), \PDO::PARAM_INT);
            $query->bindValue(':supervisor', $existedInternship->getSupervisorId(), \PDO::PARAM_INT);
            $query->bindValue(':first_name', $existedInternship->getFirstName(), \PDO::PARAM_STR);
            $query->bindValue(':last_name', $existedInternship->getLastName(), \PDO::PARAM_STR);
            $query->bindValue(':sex', $existedInternship->getSex(), \PDO::PARAM_STR);
            $query->bindValue(':birthdate', $existedInternship->getBirthdate(), \PDO::PARAM_STR);
            $query->bindValue(':school_name', $existedInternship->getSchoolName(), \PDO::PARAM_STR);
            $query->bindValue(':start_date', $existedInternship->getStartDate(), \PDO::PARAM_STR);
            $query->bindValue(':end_date', $existedInternship->getEndDate(), \PDO::PARAM_STR);
            $query->bindValue(':modified', $existedInternship->getModified(), \PDO::PARAM_STR);
            $query->bindValue(':status', $existedInternship->getStatus(), \PDO::PARAM_STR);
            $query->bindValue(':id', $existedInternship->getId(), \PDO::PARAM_INT);

            $updated = $query->execute();

            return $updated;
        } catch (\PDOException $e) {
            throw new \Exception("SQL Exception: " . $e->getMessage(), 1);
        }
    }

    /**
     * Delete internship
     *
     * @param int $id Internship id
     * @return bool Returns true if internship was deleted, false otherwise.
     */
    public function delete($id): bool
    {
        // Check if the internship exists
        $existedInternship = $this->get($id);
        if (empty($existedInternship)) {
            Flash::error("Aucun stage trouvé avec l'id " . $id);

            return false;
        }

        $sql = "UPDATE internships SET status = :status, etat = :etat WHERE id = :id";

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
     * Mark internship as completed
     *
     * @param int $id Internship id
     * @return bool Returns true if internship was marked as completed, false otherwise.
     */
    public function complete($id): bool
    {
        // Check if the internship exists
        $existedInternship = $this->get($id);
        if (empty($existedInternship)) {
            Flash::error("Aucun stage trouvé avec l'id " . $id);

            return false;
        }

        $sql = "UPDATE internships SET status = :status WHERE id = :id";

        try {
            $query = $this->connectionManager->getConnection()->prepare($sql);

            $query->bindValue(':status', 'terminated', \PDO::PARAM_STR);
            $query->bindValue(':id', $id, \PDO::PARAM_INT);

            $terminated = $query->execute();

            return $terminated;
        } catch (\PDOException $e) {
            throw new \Exception("SQL Exception: " . $e->getMessage(), 1);
        }
    }

    /**
     * Extend Internship
     *
     * @param int $id Internship id
     * @param string $end_date Internship end date
     * @return bool Returns true if the internship has been extended, false otherwise.
     */
    public function extend(int|string $id, string $end_date): bool
    {
        // Check if the Internship exists
        $existedInternship = $this->get($id);
        if (empty($existedInternship)) {
            Flash::error("Aucun stage trouvé avec l'id " . $id);

            return false;
        }

        $sql = "UPDATE internships SET end_date = :end_date WHERE id = :id";

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
     * Save internship report
     * 
     * @param int|string $id Internship id
     * @param string $filePath The report file path
     * 
     * @return bool True if the report has been saved, false otherwise
     */
    public function saveReport(int|string $id, string $filePath): bool
    {
        $existedInternship = $this->get($id);

        if (empty($existedInternship)) {
            Flash::error("Aucun stage trouvé avec l'id " . $id);
            return false;
        }

        $sql = "UPDATE internships SET report = :report WHERE id = :id";
        try {
            $query = $this->connectionManager->getConnection()->prepare($sql);
            $query->bindValue(':report', $filePath, \PDO::PARAM_STR);
            $query->bindValue(':id', $existedInternship->getId(), \PDO::PARAM_INT);
            $updated = $query->execute();

            return $updated;
        } catch (\PDOException $e) {
            throw new \Exception("SQL Exception: " . $e->getMessage(), 1);
        }
    }

    /**
     * Assign a supervisor to an intern
     * @param int|string $id Internship id
     * @param int $supervisorId Supervisor id
     * 
     * @return bool True if the supervisor has been assigned, false otherwise
     */
    public function assignSupervisor(int|string $id, int $supervisorId): bool
    {
        $existedInternship = $this->get($id);

        if (empty($existedInternship)) {
            Flash::error("Aucun stage trouvé avec l'id " . $id);
            return false;
        }

        $sql = "UPDATE internships SET supervisor = :supervisor WHERE id = :id";
        try {
            $query = $this->connectionManager->getConnection()->prepare($sql);
            $query->bindValue(':supervisor', $supervisorId, \PDO::PARAM_STR);
            $query->bindValue(':id', $existedInternship->getId(), \PDO::PARAM_INT);
            $updated = $query->execute();

            return $updated;
        } catch (\PDOException $e) {
            throw new \Exception("SQL Exception: " . $e->getMessage(), 1);
        }
    }

    /**
     * Check if the internship already exists
     *
     * @param Internship $internship Internship to check
     * @return boolean Returns true if Internship already exists, false otherwise.
     */
    public function checkInternshipDocument(Internship $internship): bool
    {
        $sql = "SELECT * FROM internships i WHERE i.etat = :etat AND i.first_name = :first_name AND i.last_name = :last_name "
            . " AND i.birthdate = :birthdate "
            . " AND i.start_date = :start_date AND i.end_date = :end_date";

        if (!empty($internship->getId())) {
            $sql .= " AND i.id != :id";
        }

        try {
            $query = $this->connectionManager->getConnection()->prepare($sql);

            $query->bindValue(':etat', 1, \PDO::PARAM_BOOL);
            $query->bindValue(':first_name', $internship->getFirstName(), \PDO::PARAM_STR);
            $query->bindValue(':last_name', $internship->getLastName(), \PDO::PARAM_STR);
            $query->bindValue(':birthdate', $internship->getBirthdate(), \PDO::PARAM_STR);
            $query->bindValue(':start_date', $internship->getStartDate(), \PDO::PARAM_STR);
            $query->bindValue(':end_date', $internship->getEndDate(), \PDO::PARAM_STR);
            if (!empty($internship->getId())) {
                $query->bindValue(':id', $internship->getId(), \PDO::PARAM_INT);
            }

            $query->execute();
            $results = $query->fetchAll(\PDO::FETCH_ASSOC);

            return !empty($results);
        } catch (\PDOException $e) {
            throw new \Exception("SQL Exception: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    function toEntity(array $raw_data): Internship
    {
        $entity = new Internship();
        $entity->setId(!empty($raw_data['id']) ? intval($raw_data['id']) : null)
            ->setInternshipTypeId(!empty($raw_data['internship_type_id']) ? intval($raw_data['internship_type_id']) : null)
            ->setSupervisorId(!empty($raw_data['supervisorId']) ? intval($raw_data['supervisorId']) : null)
            ->setFirstName(!empty($raw_data['first_name']) ? $raw_data['first_name'] : null)
            ->setLastName(!empty($raw_data['last_name']) ? $raw_data['last_name'] : null)
            ->setBirthdate(!empty($raw_data['birthdate']) ? $raw_data['birthdate'] : null)
            ->setEmail(!empty($raw_data['email']) ? $raw_data['email'] : null)
            ->setSex(!empty($raw_data['sex']) ? $raw_data['sex'] : null)
            ->setSchoolName(!empty($raw_data['school_name']) ? $raw_data['school_name'] : null)
            ->setStartDate(!empty($raw_data['start_date']) ? $raw_data['start_date'] : null)
            ->setEndDate(!empty($raw_data['end_date']) ? $raw_data['end_date'] : null)
            ->setReportPath(!empty($raw_data['report_path']) ? $raw_data['report_path'] : null)
            ->setStatus(!empty($raw_data['status']) ? $raw_data['status'] : null)
            ->setUserId(!empty($raw_data['user_id']) ? intval($raw_data['user_id']) : null)
            ->setCreated(!empty($raw_data['created']) ? $raw_data['created'] : null)
            ->setModified(!empty($raw_data['modified']) ? $raw_data['modified'] : null)
            ->setEtat(!empty($raw_data['etat']) ? $raw_data['etat'] : null)
            ->setUser(!empty($raw_data['user']) && $raw_data['user'] instanceof Employee ? $raw_data['user'] : null)
            ->setSupervisor(!empty($raw_data['supervisor']) && $raw_data['supervisor'] instanceof Employee ? $raw_data['supervisor'] : null)
            ->setInternshipType(!empty($raw_data['intenshipType']) && $raw_data['intenshipType'] instanceof InternshipType ? $raw_data['intenshipType'] : null);

        return $entity;
    }

    /**
     * This method return a \Closure that can be used to parse raw sql results into
     * Internship entities
     * 
     * @return \Closure
     */
    public function getMapper(): \Closure
    {
        return function (array $result) {
            $internships = [];

            foreach ($result as $row) {
                $internship = new Internship();
                $internship->setId($row['Internship_id']);
                $internship->setInternshipTypeId($row['Internship_internship_type_id']);
                $internship->setSupervisorId($row['Internship_supervisor']);
                $internship->setFirstName($row['Internship_first_name']);
                $internship->setLastName($row['Internship_last_name']);
                $internship->setSex($row['Internship_sex']);
                $internship->setBirthdate($row['Internship_birthdate']);
                $internship->setSchoolName($row['Internship_school_name']);
                $internship->setStartDate($row['Internship_start_date']);
                $internship->setEndDate($row['Internship_end_date']);
                $internship->setReportPath($row['Internship_report']);
                $internship->setStatus($row['Internship_status']);
                $internship->setUserId($row['Internship_user_id']);
                $internship->setCreated($row['Internship_created']);
                $internship->setModified($row['Internship_modified']);
                $internship->setEtat(boolval($row['Internship_etat']));

                if (isset($row['Supervisor_id'])) {
                    $supervisor = new Employee();
                    $supervisor->setId($row['Supervisor_id']);
                    $supervisor->setFirstName($row['Supervisor_first_name']);
                    $supervisor->setLastName($row['Supervisor_last_name']);
                    $supervisor->setUsername($row['Supervisor_username']);

                    $internship->setSupervisor($supervisor);
                }

                if (isset($row['InternshipType_id'])) {
                    $internshipType = new InternshipType();
                    $internshipType->setId($row['InternshipType_id']);
                    $internshipType->setTitle($row['InternshipType_title']);
                    $internshipType->setDescription($row['InternshipType_description']);


                    $internship->setInternshipType($internshipType);
                }

                $internships[] = $internship;
            }

            return $internships;
        };
    }
}
