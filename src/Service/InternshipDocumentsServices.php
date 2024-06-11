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

use Core\Database\ConnectionManager;
use App\Entity\Internship;
use App\Entity\InternshipDocument;
use App\Entity\InternshipDocumentType;
use Core\FlashMessages\Flash;
use Core\Utils\Session;

/**
 * Internship Document Services
 */
class InternshipDocumentsServices
{
    private $connectionManager;

    function __construct()
    {
        $this->connectionManager = new ConnectionManager();
    }

    /**
     * Get internship's documents
     * 
     * @param int $internshipId Internship ID
     * 
     * @return array<InternshipDocument> Array containing internship's documents
     */
    function getAll(int $internshipId)
    {
        $results = [];

        $select = "SELECT itndoc.id AS InternshipDocument_id, itndoc.internship_id AS InternshipDocument_internship_id, "
            . " itndoc.internship_document_type_id AS InternshipDocument_internship_document_type_id, itndoc.etat AS InternshipDocument_etat, "
            . " itndoc.document AS InternshipDocument_document,  itndoc.created AS InternshipDocument_created, ";

        $select .= " itn.id AS Internship_id, itn.first_name AS Internship_first_name, itn.last_name AS Internship_last_name, "
            . " itn.start_date AS Internship_start_date, itn.end_date AS Internship_end_date, itn.supervisor AS Internship_supervisor, ";
        $select .= " itd.id AS InternshipDocumentType_id, itd.code AS InternshipDocumentType_code, itd.description AS InternshipDocumentType_description, "
            . " itd.is_required AS InternshipDocumentType_required, itd.is_multiple AS InternshipDocumentType_multiple ";

        $from = " FROM internship_documents itndoc";

        $join = " LEFT JOIN internships itn ON itn.id = itndoc.internship_id";
        $join .= " INNER JOIN internship_document_types itd ON itd.id = itndoc.internship_document_type_id";

        $where = " WHERE itndoc.etat = :etat AND itndoc.internship_id = :internship_id";

        $sql = $select . $from . $join . $where;

        try {
            $query = $this->connectionManager->getConnection()->prepare($sql);

            $query->bindValue(':etat', 1, \PDO::PARAM_BOOL);
            $query->bindParam(':internship_id', $internshipId, \PDO::PARAM_INT);


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
     * Get internship's document by id
     * 
     * @param int $internshipDocumentId InternshipDocument ID
     * 
     * @return InternshipDocument|null Returns the document or null if not found
     */
    function getById(int $internshipDocumentId): InternshipDocument|null
    {
        $results = [];

        $select = "SELECT itndoc.id AS InternshipDocument_id, itndoc.internship_id AS InternshipDocument_internship_id, "
            . " itndoc.internship_document_type_id AS InternshipDocument_internship_document_type_id, itndoc.etat AS InternshipDocument_etat, "
            . " itndoc.document AS InternshipDocument_document,  itndoc.created AS InternshipDocument_created, ";

        $select .= " itn.id AS Internship_id, itn.first_name AS Internship_first_name, itn.last_name AS Internship_last_name, "
            . " itn.start_date AS Internship_start_date, itn.end_date AS Internship_end_date, itn.supervisor AS Internship_supervisor, ";
        $select .= " itd.id AS InternshipDocumentType_id, itd.code AS InternshipDocumentType_code, itd.description AS InternshipDocumentType_description, "
            . " itd.is_required AS InternshipDocumentType_required, itd.is_multiple AS InternshipDocumentType_multiple ";

        $from = " FROM internship_documents itndoc";

        $join = " LEFT JOIN internships itn ON itn.id = itndoc.internship_id";
        $join .= " INNER JOIN internship_document_types itd ON itd.id = itndoc.internship_document_type_id";

        $where = " WHERE itndoc.etat = :etat AND itndoc.id = :id";

        $sql = $select . $from . $join . $where;

        try {
            $query = $this->connectionManager->getConnection()->prepare($sql);

            $query->bindValue(':etat', 1, \PDO::PARAM_BOOL);
            $query->bindParam(':id', $internshipDocumentId, \PDO::PARAM_INT);


            $query->execute();

            $results = $query->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new \Exception("SQL Exception: " . $e->getMessage(), (int)$e->getCode());
        }

        if (empty($results)) {
            return null;
        }

        /**
         * @var array<InternshipDocument>
         */
        $mappedResults = call_user_func_array($this->getMapper(), [$results]);

        return reset($mappedResults);
    }

    /**
     * Add internship's document
     *
     * @param array|Internship $rawInternshipDocument InternshipDocument to save
     * @return int|bool Returns the id of the internship if successful, false otherwise.
     */
    public function add(array|Internship $rawInternshipDocument): int|bool
    {
        if (is_array($rawInternshipDocument)) {
            $internshipDocument = $this->toEntity($rawInternshipDocument);
        } else {
            /**
             * @var InternshipDocument $internshipDocument
             */
            $internshipDocument = $rawInternshipDocument;
        }

        if ($this->checkInternshipDocument($internshipDocument)) {
            Flash::error("Le document a déjà été fourni.");

            Session::write('__formdata__', json_encode($_POST));

            return false;
        }

        $errors = $internshipDocument->validation();
        if (!empty($errors)) {
            foreach ($errors as $error) {
                Flash::error($error);
            }

            Session::write('__formdata__', json_encode($_POST));

            return false;
        }

        $internshipDocument->setCreated(date('Y-m-d H:i:s'));
        $internshipDocument->setEtat(true);

        $sql = "INSERT INTO internship_documents(internship_id, internship_document_type_id, document, created, etat) "
            . " VALUES (:internship_id, :internship_document_type_id, :document, :created, :etat)";

        try {

            $query = $this->connectionManager->getConnection()->prepare($sql);

            $query->bindValue(":internship_id", $internshipDocument->getInternshipId(), \PDO::PARAM_INT);
            $query->bindValue(":internship_document_type_id", $internshipDocument->getInternshipDocumentTypeId(), \PDO::PARAM_INT);
            $query->bindValue(":document", $internshipDocument->getDocument(), \PDO::PARAM_STR);
            $query->bindValue(":created", $internshipDocument->getCreated(), \PDO::PARAM_STR);
            $query->bindValue(":etat", $internshipDocument->getEtat(), \PDO::PARAM_BOOL);

            $query->execute();

            $document_id = (int)$this->connectionManager->getConnection()->lastInsertId();

            return $document_id;
        } catch (\PDOException $e) {
            throw new \Exception("SQL Exception: " . $e->getMessage(), 1);
        }
    }

    /**
     * Save many documents at once
     * 
     * @param array<array|\App\Entity\InternshipDocument> $documents
     * 
     * @return array Returns an array containing saved documents' ids
     * if the operation succeeded, empty array otherwise 
     */
    public function addMultiple(array $documents): array
    {
        $ids = [];

        $this->connectionManager->getConnection()->beginTransaction();

        foreach ($documents as $index => $document) {
            if (!is_array($document) && !$document instanceof InternshipDocument) {
                Flash::error("Mauvaise requête");
                $this->connectionManager->getConnection()->rollBack();
                break;
            }

            try {
                $documentId = $this->add($document);
                if ($documentId !== false) {
                    $ids[] = $documentId;
                } else {
                    Flash::error("Echec d'enregistrement du document numéro " . ($index + 1));
                    return [];
                }
            } catch (\Exception $e) {
                $this->connectionManager->getConnection()->rollBack();
                Flash::error($e->getMessage());
                break;
            }
        }

        return $ids;
    }

    /**
     * Update internship's document
     *
     * @param array|InternshipDocument $internshipDocument InternshipDocument to update
     * @return int|bool Returns true if the internship's document has been updated, false otherwise.
     */
    public function update(array|InternshipDocument $internshipDocument): bool
    {
        if (is_array($internshipDocument)) {
            $internshipDocument = $this->toEntity($internshipDocument);
        }

        // Check if the internship's document exists
        $existedInternship = $this->getById($internshipDocument->getId());
        if (empty($existedInternship)) {
            Flash::error("Aucun stage trouvé avec l'id " . $internshipDocument->getId());

            return false;
        }

        // Check if the internship's document already exists
        if ($this->checkInternshipDocument($internshipDocument)) {
            Flash::error("Le document a déjà été fourni.");

            Session::write('__formdata__', json_encode($_POST));

            return false;
        }

        //$existedInternship->setInternshipId($internshipDocument->getInternshipId());
        $existedInternship->setInternshipDocumentTypeId($internshipDocument->getInternshipDocumentTypeId());
        $existedInternship->setDocument($internshipDocument->getDocument());

        // Validation
        $errors = $existedInternship->validation();
        if (!empty($errors)) {
            foreach ($errors as $error) {
                Flash::error($error);
            }

            Session::write('__formdata__', json_encode($_POST));

            return false;
        }

        $sql = "UPDATE internship_documents SET internship_document_type_id = :internship_document_type_id, document = :document WHERE id = :id";

        try {
            $query = $this->connectionManager->getConnection()->prepare($sql);

            $query->bindValue(':internship_document_type_id', $existedInternship->getInternshipDocumentTypeId(), \PDO::PARAM_INT);
            $query->bindValue(':document', $existedInternship->getDocument(), \PDO::PARAM_STR);
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
        $existedInternship = $this->getById($id);
        if (empty($existedInternship)) {
            Flash::error("Aucun document de stage trouvé avec l'id " . $id);

            return false;
        }

        $sql = "DELETE FROM internship_documents WHERE id = :id";

        try {
            $query = $this->connectionManager->getConnection()->prepare($sql);
            $query->bindValue(':id', $id, \PDO::PARAM_INT);
            $deleted = $query->execute();

            return $deleted;
        } catch (\PDOException $e) {
            throw new \Exception("SQL Exception: " . $e->getMessage(), 1);
        }
    }

    /**
     * Check if the internship's document already exists
     *
     * @param InternshipDocument $internshipDocument InternshipDocument to check
     * @return boolean Returns true if Internship already exists, false otherwise.
     */
    public function checkInternshipDocument(InternshipDocument $internshipDocument): bool
    {
        $sql = "SELECT * FROM internship_documents i JOIN internship_document_types itd ON itd.id = i.internship_document_type_id "
            . " WHERE i.etat = :etat AND i.internship_id = :internship_id "
            . " AND i.internship_document_type_id = :internship_document_type_id AND itd.is_multiple = :multiple ";

        if (!empty($internshipDocument->getId())) {
            $sql .= " AND i.id != :id";
        }

        try {
            $query = $this->connectionManager->getConnection()->prepare($sql);

            $query->bindValue(':etat', 1, \PDO::PARAM_BOOL);
            $query->bindValue(':internship_id', $internshipDocument->getInternshipId(), \PDO::PARAM_INT);
            $query->bindValue(':internship_document_type_id', $internshipDocument->getInternshipDocumentTypeId(), \PDO::PARAM_INT);
            $query->bindValue(':multiple', false, \PDO::PARAM_BOOL);
            if (!empty($internshipDocument->getId())) {
                $query->bindValue(':id', $internshipDocument->getId(), \PDO::PARAM_INT);
            }

            $query->execute();
            $results = $query->fetchAll(\PDO::FETCH_ASSOC);

            return !empty($results);
        } catch (\PDOException $e) {
            throw new \Exception("SQL Exception: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public function toEntity(array $rawData): InternshipDocument
    {
        $entity = new InternshipDocument();
        if (!empty($rawData["id"])) {
            $entity->setId(intval($rawData['id']));
        }
        if (!empty($rawData["internship_document_type_id"])) {
            $entity->setInternshipDocumentTypeId(intval($rawData['internship_document_type_id']));
        }
        if (!empty($rawData["internship_id"])) {
            $entity->setInternshipId(intval($rawData['internship_id']));
        }
        if (!empty($rawData["document"])) {
            $entity->setDocument($rawData['document']);
        }
        if (!empty($rawData["created"])) {
            $entity->setCreated($rawData['created']);
        }
        if (!empty($rawData["etat"])) {
            $entity->setEtat(boolval($rawData['etat']));
        }
        if (!empty($rawData["internship"]) && $rawData["internship"] instanceof Internship) {
            $entity->setInternship($rawData['internship']);
        }
        if (!empty($rawData["internship_document_type"]) && $rawData["internship_document_type"] instanceof InternshipDocumentType) {
            $entity->setInternshipDocumentType($rawData['internship_document_type']);
        }


        return $entity;
    }

    /**
     * This method return a \Closure that can be used to parse raw sql results into
     * InternshipDocument entities
     * 
     * @return \Closure
     */
    public function getMapper(): \Closure
    {
        return function (array $result) {
            $internshipDocuments = [];

            foreach ($result as $row) {
                $internshipDocument = new InternshipDocument();
                $internshipDocument->setId($row['InternshipDocument_id']);
                $internshipDocument->setInternshipDocumentTypeId($row['InternshipDocument_internship_document_type_id']);
                $internshipDocument->setDocument($row['InternshipDocument_document']);
                $internshipDocument->setInternshipId($row['InternshipDocument_internship_id']);
                $internshipDocument->setCreated($row['InternshipDocument_created']);
                $internshipDocument->setEtat(boolval($row['InternshipDocument_etat']));

                if (isset($row['Internship_id'])) {
                    $internship = new Internship();
                    $internship->setId($row['Internship_id']);
                    $internship->setFirstName($row['Internship_first_name']);
                    $internship->setLastName($row['Internship_last_name']);
                    $internship->setStartDate($row['Internship_start_date']);
                    $internship->setEndDate($row['Internship_end_date']);
                    $internship->setSupervisorId($row['Internship_supervisor']);

                    $internshipDocument->setInternship($internship);
                }

                if (isset($row['InternshipDocumentType_id'])) {
                    $internshipDocumentType = new InternshipDocumentType();
                    $internshipDocumentType->setId($row['InternshipDocumentType_id']);
                    $internshipDocumentType->setCode($row['InternshipDocumentType_code']);
                    $internshipDocumentType->setDescription($row['InternshipDocumentType_description']);
                    $internshipDocumentType->setMultipe(boolval($row['InternshipDocumentType_multiple']));
                    $internshipDocumentType->setRequired(boolval($row['InternshipDocumentType_required']));


                    $internshipDocument->setInternshipDocumentType($internshipDocumentType);
                }

                $internshipDocuments[] = $internshipDocument;
            }

            return $internshipDocuments;
        };
    }
}
