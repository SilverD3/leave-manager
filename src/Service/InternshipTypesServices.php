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

require_once dirname(dirname(__DIR__)) . DS . DS . 'autoload.php';

use App\Entity\InternshipType;
use Core\Database\ConnectionManager;
use Core\FlashMessages\Flash;
use Core\Utils\Session;

/**
 * Internship Types Services
 */
class InternshipTypesServices
{
    private $connectionManager;

    function __construct()
    {
        $this->connectionManager = new ConnectionManager();
    }

    /**
     * Count all Internship types
     * 
     * @return int Number of Internship types
     */
    public function countAll(): int
    {
        $count = 0;
        $join = '';

        $sql = "SELECT COUNT(*) AS count FROM internship_types ct WHERE ct.etat = ?";

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
     * Get all internship types
     * 
     * @return array<\App\Entity\InternshipType> List of internship types of empty array
     */
    public function getAll()
    {
        $result = [];
        $internship_types = [];

        $sql = "SELECT * FROM internship_types WHERE etat = ?";

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
            $internshipType = new InternshipType();
            $internshipType->setId($row['id']);
            $internshipType->setTitle($row['title']);
            $internshipType->setDescription($row['description']);
            $internshipType->setCreated($row['created']);
            $internshipType->setEtat($row['etat']);

            $internship_types[] = $internshipType;
        }

        return $internship_types;
    }

    /**
     * Retrieve specific internship type
     * 
     * @param int $id Internship type id
     * @return InternshipType|null Return the internship type or null if not found
     */
    public function get(int $id): ?InternshipType
    {
        $sql = "SELECT * FROM internship_types WHERE etat = :etat AND id = :id";

        try {
            $query = $this->connectionManager->getConnection()->prepare($sql);
            $query->bindParam(':id', $id, \PDO::PARAM_INT);
            $query->bindValue(':etat', 1, \PDO::PARAM_BOOL);

            $query->execute();

            $result = $query->fetch(\PDO::FETCH_ASSOC);

            if (empty($result)) {
                return null;
            }

            $internshipType = new InternshipType();
            $internshipType->setId($result['id']);
            $internshipType->setTitle($result['title']);
            $internshipType->setDescription($result['description']);
            $internshipType->setCreated($result['created']);
            $internshipType->setEtat($result['etat']);

            return $internshipType;
        } catch (\PDOException $e) {
            throw new \Exception("SQL Exception: " . $e->getMessage(), $e->getCode());
        }
    }

    /**
     * Add a internship type
     * 
     * @param array $internshipType internship type data
     * 
     */
    public function add(array|InternshipType $internshipType): InternshipType|bool
    {
        if (is_array($internshipType)) {
            $title = htmlentities($internshipType['title']);
            $description = htmlentities($internshipType['description']);
            if (empty($description)) {
                $description = null;
            }

            $internshipType = new InternshipType();
            $internshipType->setTitle($title);
            $internshipType->setDescription($description);
        } elseif (!$internshipType instanceof InternshipType) {
            throw new \Exception("Invalid parameter. Type of parameter passed must be array or InternshipType");
        }

        // Check if the internship type already exists
        if ($this->checkInternshipType($internshipType)) {
            Flash::error("Le nom du type de stage est déjà utilisé.");

            Session::write('__formdata__', json_encode($_POST));

            return false;
        }

        $internshipType->setCreated(date('Y-m-d H:i:s'));
        $internshipType->setEtat(true);

        $errors = $internshipType->validation();

        if (!empty($errors)) {
            foreach ($errors as $error) {
                Flash::error($error);
            }

            Session::write('__formdata__', json_encode($_POST));

            return false;
        }

        $sql = "INSERT INTO internship_types(title, description, created, etat) VALUES(?,?,?,?)";

        try {
            $this->connectionManager->getConnection()->beginTransaction();

            $query = $this->connectionManager->getConnection()->prepare($sql);
            $query->bindValue(1, $internshipType->getTitle(), \PDO::PARAM_STR);
            $query->bindValue(2, $internshipType->getDescription(), \PDO::PARAM_STR);
            $query->bindValue(3, $internshipType->getCreated(), \PDO::PARAM_STR);
            $query->bindValue(4, $internshipType->getEtat(), \PDO::PARAM_BOOL);
            $query->execute();

            $id = (int)$this->connectionManager->getConnection()->lastInsertId();
            $internshipType->setId($id);
            $this->connectionManager->getConnection()->commit();

            return $internshipType;
        } catch (\PDOException $e) {
            $this->connectionManager->getConnection()->rollBack();

            throw new \Exception("SQL Exception: " . $e->getMessage(), $e->getCode());
        }
    }

    /**
     * Update a Internship type
     * 
     * @param array|InternshipType $internship_type Internship type data
     * @return bool Returns true if Internship type was updated successfully, false otherwise.
     */
    public function update(array|InternshipType $internship_type): bool
    {
        if (is_array($internship_type)) {
            $title = htmlentities($internship_type['title']);
            $id = (int)$internship_type['id'];
            $description = htmlentities($internship_type['description']);
            if (empty($description)) {
                $description = null;
            }

            $internship_type = new InternshipType();
            $internship_type->setID($id);
            $internship_type->setTitle($title);
            $internship_type->setDescription($description);
        } elseif (!$internship_type instanceof InternshipType) {
            throw new \Exception("Invalid parameter. Type of parameter passed must be array or InternshipType");
        }

        $internshipType = $this->get($internship_type->getId());

        if (empty($internshipType)) {
            throw new \Exception("Record non trouvé dans les types de stage.", 1);
        }

        $internshipType->setTitle($internship_type->getTitle());
        $internshipType->setDescription($internship_type->getDescription());

        // Check if the internship type already exists
        if ($this->checkInternshipType($internshipType)) {
            Flash::error("Le nom du type de stage est déjà utilisé.");

            Session::write('__formdata__', json_encode($_POST));

            return false;
        }

        $errors = $internshipType->validation();

        if (!empty($errors)) {
            foreach ($errors as $error) {
                Flash::error($error);
            }

            Session::write('__formdata__', json_encode($_POST));

            return false;
        }

        $sql = "UPDATE internship_types SET title = :title, description = :description WHERE id = :id";

        try {
            $this->connectionManager->getConnection()->beginTransaction();

            $query = $this->connectionManager->getConnection()->prepare($sql);
            $query->bindValue(':title', $internshipType->getTitle(), \PDO::PARAM_STR);
            $query->bindValue(':description', $internshipType->getDescription(), \PDO::PARAM_STR);
            $query->bindValue(':id', $internshipType->getId(), \PDO::PARAM_INT);
            $updated = $query->execute();

            $this->connectionManager->getConnection()->commit();

            return $updated;
        } catch (\PDOException $e) {
            $this->connectionManager->getConnection()->rollBack();

            throw new \Exception("SQL Exception: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    /**
     * Delete internship type
     *
     * @param int $id
     * @return bool Returns true if internship type has been deleted successfully, false otherwise.
     */
    public function delete(int $id): bool
    {
        // First check if the internship type exists
        $internshipType = $this->get($id);
        if (empty($internshipType)) {
            throw new \Exception("Record non trouvé dans les types de stage.", 1);
        }

        $sql = "UPDATE internship_types SET etat = :etat WHERE id = :id";

        try {
            $query = $this->connectionManager->getConnection()->prepare($sql);
            $query->bindValue(':etat', 0, \PDO::PARAM_BOOL);
            $query->bindValue(':id', $id, \PDO::PARAM_INT);

            $deleted = $query->execute();

            return $deleted;
        } catch (\PDOException $e) {
            throw new \Exception("SQL Exception: " . $e->getMessage(), $e->getCode());
        }
    }

    /**
     * Check if internship type already exists
     *
     * @param InternshipType $internshipType internship type to check
     * @return bool Returns true if internship type already exists, false otherwise.
     */
    public function checkInternshipType(InternshipType $internshipType): bool
    {
        if (is_null($internshipType->getId())) {
            $sql = "SELECT * FROM internship_types WHERE etat = :etat AND title = :title limit 0,1";
        } else {
            $sql = "SELECT * FROM internship_types WHERE etat = :etat AND title = :title AND id != :id limit 0,1";
        }

        try {
            $query = $this->connectionManager->getConnection()->prepare($sql);
            $query->bindValue(':etat', 1, \PDO::PARAM_BOOL);
            $query->bindValue(':title', $internshipType->getTitle(), \PDO::PARAM_STR);
            if (!is_null($internshipType->getId())) {
                $query->bindValue(':id', $internshipType->getId(), \PDO::PARAM_INT);
            }
            $query->execute();

            $result = $query->fetchAll(\PDO::FETCH_ASSOC);
            if (empty($result)) {
                return false;
            }

            return true;
        } catch (\PDOException $e) {
            throw new \Exception("SQL Exception: " . $e->getMessage(), $e->getCode());
        }
    }
}
