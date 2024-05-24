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

use App\Entity\InternshipDocumentType;
use Core\Database\ConnectionManager;
use Core\FlashMessages\Flash;
use Core\Utils\Session;

/**
 * Internship Document Types Services
 */
class InternshipDocumentTypesServices
{
    private $connectionManager;

    function __construct()
    {
        $this->connectionManager = new ConnectionManager();
    }

    /**
     * Count all Internship document types
     * 
     * @return int Number of Internship document types
     */
    public function countAll(): int
    {
        $count = 0;

        $sql = "SELECT COUNT(*) AS count FROM internship_document_types ct WHERE ct.etat = ?";

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
     * Get all internship document types
     * 
     * @return array<\App\Entity\InternshipDocumentType> List of internship document types of empty array
     */
    public function getAll()
    {
        $result = [];
        $internship_document_types = [];

        $sql = "SELECT * FROM internship_document_types WHERE etat = ?";

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
            $internshipDocumentType = new InternshipDocumentType();
            $internshipDocumentType->setId($row['id']);
            $internshipDocumentType->setCode($row['code']);
            $internshipDocumentType->setDescription($row['description']);
            $internshipDocumentType->setMultipe(boolval($row['is_multiple']));
            $internshipDocumentType->setRequired(boolval($row['is_required']));
            $internshipDocumentType->setCreated($row['created']);
            $internshipDocumentType->setEtat($row['etat']);

            $internship_document_types[] = $internshipDocumentType;
        }

        return $internship_document_types;
    }

    /**
     * Retrieve specific internship type
     * 
     * @param int $id Internship type id
     * @return InternshipDocumentType|null Return the internship type or null if not found
     */
    public function get(int $id): ?InternshipDocumentType
    {
        $sql = "SELECT * FROM internship_document_types WHERE etat = :etat AND id = :id";

        try {
            $query = $this->connectionManager->getConnection()->prepare($sql);
            $query->bindParam(':id', $id, \PDO::PARAM_INT);
            $query->bindValue(':etat', 1, \PDO::PARAM_BOOL);

            $query->execute();

            $result = $query->fetch(\PDO::FETCH_ASSOC);

            if (empty($result)) {
                return null;
            }

            $internshipDocumentType = new InternshipDocumentType();
            $internshipDocumentType->setId($result['id']);
            $internshipDocumentType->setCode($result['code']);
            $internshipDocumentType->setDescription($result['description']);
            $internshipDocumentType->setMultipe(boolval($result['is_multiple']));
            $internshipDocumentType->setRequired(boolval($result['is_required']));
            $internshipDocumentType->setCreated($result['created']);
            $internshipDocumentType->setEtat($result['etat']);

            return $internshipDocumentType;
        } catch (\PDOException $e) {
            throw new \Exception("SQL Exception: " . $e->getMessage(), $e->getCode());
        }
    }

    /**
     * Add a internship document type
     * 
     * @param array $internship_document_type internship document type data
     * 
     */
    public function add(array|InternshipDocumentType $internship_document_type): InternshipDocumentType|bool
    {
        if (is_array($internship_document_type)) {
            $code = htmlspecialchars($internship_document_type['code']);
            $description = htmlspecialchars($internship_document_type['description']);
            if (empty($description)) {
                $description = null;
            }

            $internshipDocumentType = new InternshipDocumentType();
            $internshipDocumentType->setCode($code);
            $internshipDocumentType->setDescription($description);
            $internshipDocumentType->setMultipe(boolval($internship_document_type['is_multiple']));
            $internshipDocumentType->setRequired(boolval($internship_document_type['is_required']));
        } elseif (!$internship_document_type instanceof InternshipDocumentType) {
            throw new \Exception("Invalid parameter. Type of parameter passed must be array or InternshipDocumentType");
        }

        // Check if the internship type already exists
        if ($this->checkInternshipDocumentType($internshipDocumentType)) {
            Flash::error("Le nom du type de document de stage est déjà utilisé.");

            Session::write('__formdata__', json_encode($_POST));

            return false;
        }

        $internshipDocumentType->setCreated(date('Y-m-d H:i:s'));
        $internshipDocumentType->setEtat(true);

        $errors = $internshipDocumentType->validation();

        if (!empty($errors)) {
            foreach ($errors as $error) {
                Flash::error($error);
            }

            Session::write('__formdata__', json_encode($_POST));

            return false;
        }

        $sql = "INSERT INTO internship_document_types(code, description, is_multiple, is_required, created, etat) VALUES(?,?,?,?,?,?)";

        try {
            $this->connectionManager->getConnection()->beginTransaction();

            $query = $this->connectionManager->getConnection()->prepare($sql);
            $query->bindValue(1, $internshipDocumentType->getCode(), \PDO::PARAM_STR);
            $query->bindValue(2, $internshipDocumentType->getDescription(), \PDO::PARAM_STR);
            $query->bindValue(3, $internshipDocumentType->isMultipe(), \PDO::PARAM_BOOL);
            $query->bindValue(4, $internshipDocumentType->isRequired(), \PDO::PARAM_BOOL);
            $query->bindValue(5, $internshipDocumentType->getCreated(), \PDO::PARAM_STR);
            $query->bindValue(6, $internshipDocumentType->getEtat(), \PDO::PARAM_BOOL);
            $query->execute();

            $id = (int)$this->connectionManager->getConnection()->lastInsertId();
            $internshipDocumentType->setId($id);
            $this->connectionManager->getConnection()->commit();

            return $internshipDocumentType;
        } catch (\PDOException $e) {
            $this->connectionManager->getConnection()->rollBack();

            throw new \Exception("SQL Exception: " . $e->getMessage(), $e->getCode());
        }
    }

    /**
     * Update a Internship document type
     * 
     * @param array|InternshipDocumentType $internship_document_type Internship document type data
     * @return bool Returns true if Internship document type was updated successfully, false otherwise.
     */
    public function update(array|InternshipDocumentType $internship_document_type): bool
    {
        if (is_array($internship_document_type)) {
            $code = htmlspecialchars($internship_document_type['code']);
            $id = (int)$internship_document_type['id'];
            $isMultipe = boolval($internship_document_type['is_multiple']);
            $isRequired = boolval($internship_document_type['is_required']);
            $description = htmlspecialchars($internship_document_type['description']);
            if (empty($description)) {
                $description = null;
            }

            $internship_document_type = new InternshipDocumentType();
            $internship_document_type->setID($id);
            $internship_document_type->setCode($code);
            $internship_document_type->setDescription($description);
            $internship_document_type->setMultipe($isMultipe);
            $internship_document_type->setRequired($isRequired);
        } elseif (!$internship_document_type instanceof InternshipDocumentType) {
            throw new \Exception("Invalid parameter. Type of parameter passed must be array or InternshipDocumentType");
        }

        $internshipDocumentType = $this->get($internship_document_type->getId());

        if (empty($internshipDocumentType)) {
            throw new \Exception("Record non trouvé dans les types de stage.", 1);
        }

        $internshipDocumentType->setCode($internship_document_type->getCode());
        $internshipDocumentType->setDescription($internship_document_type->getDescription());
        $internshipDocumentType->setMultipe($internship_document_type->isMultipe());
        $internshipDocumentType->setRequired($internship_document_type->isRequired());

        // Check if the internship type already exists
        if ($this->checkInternshipDocumentType($internshipDocumentType)) {
            Flash::error("Le nom du type de document de stage est déjà utilisé.");

            Session::write('__formdata__', json_encode($_POST));

            return false;
        }

        $errors = $internshipDocumentType->validation();

        if (!empty($errors)) {
            foreach ($errors as $error) {
                Flash::error($error);
            }

            Session::write('__formdata__', json_encode($_POST));

            return false;
        }

        $sql = "UPDATE internship_document_types SET code = :code, description = :description, is_multiple = :multiple, is_required= :required WHERE id = :id";

        try {
            $this->connectionManager->getConnection()->beginTransaction();

            $query = $this->connectionManager->getConnection()->prepare($sql);
            $query->bindValue(':code', $internshipDocumentType->getCode(), \PDO::PARAM_STR);
            $query->bindValue(':description', $internshipDocumentType->getDescription(), \PDO::PARAM_STR);
            $query->bindValue(':id', $internshipDocumentType->getId(), \PDO::PARAM_INT);
            $query->bindValue(':multiple', $internshipDocumentType->isMultipe(), \PDO::PARAM_BOOL);
            $query->bindValue(':required', $internshipDocumentType->isRequired(), \PDO::PARAM_BOOL);
            $updated = $query->execute();

            $this->connectionManager->getConnection()->commit();

            return $updated;
        } catch (\PDOException $e) {
            $this->connectionManager->getConnection()->rollBack();

            throw new \Exception("SQL Exception: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    /**
     * Delete internship document type
     *
     * @param int $id
     * @return bool Returns true if internship document type has been deleted successfully, false otherwise.
     */
    public function delete(int $id): bool
    {
        // First check if the internship document type exists
        $internshipDocumentType = $this->get($id);
        if (empty($internshipDocumentType)) {
            throw new \Exception("Record non trouvé dans les types de document de stage.", 1);
        }

        $sql = "UPDATE internship_document_types SET etat = :etat WHERE id = :id";

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
     * Check if internship document type already exists
     *
     * @param InternshipDocumentType $internshipDocumentType internship document type to check
     * @return bool Returns true if internship document type already exists, false otherwise.
     */
    public function checkInternshipDocumentType(InternshipDocumentType $internshipDocumentType): bool
    {
        if (is_null($internshipDocumentType->getId())) {
            $sql = "SELECT * FROM internship_document_types WHERE etat = :etat AND code = :code limit 0,1";
        } else {
            $sql = "SELECT * FROM internship_document_types WHERE etat = :etat AND code = :code AND id != :id limit 0,1";
        }

        try {
            $query = $this->connectionManager->getConnection()->prepare($sql);
            $query->bindValue(':etat', 1, \PDO::PARAM_BOOL);
            $query->bindValue(':code', $internshipDocumentType->getCode(), \PDO::PARAM_STR);
            if (!is_null($internshipDocumentType->getId())) {
                $query->bindValue(':id', $internshipDocumentType->getId(), \PDO::PARAM_INT);
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
