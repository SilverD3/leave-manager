<?php

declare(strict_types=1);

/**
 * Leave manager : Simple app for contract and leave management.
 *
 * @copyright Copyright (c) Silevester D. (https://github.com/SilverD3)
 * @link      https://github.com/SilverD3/leave-manager Leave Manager Project
 * @since     1.0 (2022)
 */

namespace App\Controller;

use App\Service\InternshipDocumentsServices;
use App\Service\InternshipDocumentTypesServices;
use App\Service\InternshipsServices;
use App\Service\InternshipTypesServices;
use Core\Auth\Auth;
use Core\FileManager\FileManager;
use Core\FileManager\FileType;
use Core\FileManager\FileValidation;
use Core\FlashMessages\Flash;
use Core\Utils\Session;

require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'autoload.php';

class InternshipDocumentsController
{
    /**
     * @var InternshipDocumentsServices $services InternshipDocuments services
     */
    private $service;

    function __construct()
    {
        $this->service = new InternshipDocumentsServices();
    }

    /**
     * Index method
     * @return void
     */
    public function index()
    {
        /**
         * @var \App\Entity\Employee
         */
        $auth_user = (new Auth())->getAuthUser();
        if (empty($auth_user)) {
            AuthController::require_auth();
        }

        if (!isset($_GET['id']) || empty($_GET['id'])) {
            Flash::error("Mauvaise requête");
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }

        $_SESSION['page_title'] = 'Stages';
        $_SESSION['subpage_title'] = 'Documents de stage';

        $internshipsService = new InternshipsServices();
        $internship = $internshipsService->get(intval($_GET['id']));
        if (empty($internship)) {
            Flash::error("Aucun stage trouvé avec l'id " . $_GET['id']);
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }

        if (
            $auth_user->getRole()->getCode() !== 'ADM'
            && $auth_user->getRole()->getCode() !== 'EMP'
            && $internship->getUserId() !== $auth_user->getId()
        ) {
            Flash::error("Defaut de privilège. Vous n'avez pas le droit d'acceder à la ressource sollicitée.");
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }

        $internshipDocuments = $this->service->getAll(intval($_GET['id']));

        $GLOBALS['internshipDocuments'] = $internshipDocuments;
        $GLOBALS['internshipId'] = $_GET['id'];
    }

    /**
     * Add new internship's document
     *
     * @return void
     */
    public function add()
    {
        AuthController::require_employee_priv();

        if (!isset($_GET['id']) || empty($_GET['id'])) {
            Flash::error('Mauvaise requête');
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit;
        }

        if (isset($_POST['add_internship_document'])) {
            $documents = $_FILES['internship_documents'];
            $fileManager = new FileManager();
            $fileValidation = new FileValidation();
            $fileValidation->setMaxSize(1024 * 1024 * 15);
            $fileValidation->setFileTypes([FileType::PDF, FileType::WORD, FileType::IMAGE]);
            $internshipDocuments = [];

            for ($i = 0; $i < count($documents['tmp_name']); $i++) {
                $internshipDocument = [
                    'internship_document_type_id' => intval($_POST['internship_document_type_id']),
                    'internship_id' => intval($_GET['id'])
                ];

                $documentFile = [
                    'name' => $documents['name'][$i],
                    'full_path' => $documents['full_path'][$i],
                    'type' => $documents['type'][$i],
                    'tmp_name' => $documents['tmp_name'][$i],
                    'error' => $documents['error'][$i],
                    'size' => $documents['size'][$i],
                ];

                $uploadStatus = $fileManager->saveFile($documentFile, $fileValidation, UPLOADS_PATH . INTERNSHIP_DOCUMENTS_DIR_NAME);
                if (!$uploadStatus->hasSucceeded()) {
                    Flash::error(
                        !empty($uploadStatus->getErrors())
                            ? implode('. ', $uploadStatus->getErrors())
                            : sprintf("Une erreur est survenue lors de l'enregistrement %s. Veuillez réessayer", count($documents['tmp_name']) > 1 ? 'des documents' : 'du document')
                    );
                    header("Location: " . $_SERVER['HTTP_REFERER']);
                    exit;
                }

                $filePath = $uploadStatus->getFilename();
                $internshipDocument['document'] = $filePath;

                $internshipDocuments[] = $internshipDocument;
            }

            $documentsSaved = false;
            if (count($internshipDocuments) > 1) {
                $documentIds = $this->service->addMultiple($internshipDocuments);
                if (!empty($documentIds)) {
                    $documentsSaved = true;
                }
            } else {
                $documentId = $this->service->add(reset($internshipDocuments));
                if ($documentId !== false) {
                    $documentsSaved = true;
                }
            }

            if ($documentsSaved) {
                Flash::success("Document(s) enregistré(s) avec succès.");

                header("Location: " . VIEWS . "Internships/documents.php?id=" . $_GET['id']);
                exit;
            }
        }

        $_SESSION['page_title'] = 'Stages';
        $_SESSION['subpage_title'] = 'Ajouter un document de stage';

        $internshipDocumentTypesServices = new InternshipDocumentTypesServices();

        $document_types = $internshipDocumentTypesServices->getAll();

        $GLOBALS['document_types'] = $document_types;

        // Check if form data is cached
        $formdata = Session::consume('__formdata__');

        if (!empty($formdata)) {
            $GLOBALS['form_data'] = json_decode($formdata, true);
        }
    }

    /**
     * Update existing internship
     *
     * @return void
     */
    public function update()
    {
        AuthController::require_employee_priv();

        if (!isset($_GET['id'])) {
            Flash::error("Mauvaise requête");
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }

        $internshipDocument = $this->service->getById(intval($_GET['id']));
        if (!$internshipDocument) {
            Flash::error("Aucun document de stage trouvé avec l'id " . $_GET['id']);
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }

        if (isset($_POST['update_internship_document'])) {
            $fileManager = new FileManager();
            $fileValidation = new FileValidation();
            $fileValidation->setMaxSize(1024 * 1024 * 5);
            $fileValidation->setFileTypes([FileType::PDF, FileType::WORD, FileType::IMAGE]);

            $uploadStatus = $fileManager->saveFile($_FILES['internship_document'], $fileValidation, UPLOADS_PATH . INTERNSHIP_DOCUMENTS_DIR_NAME);

            if (!$uploadStatus->hasSucceeded()) {
                Flash::error(!empty($uploadStatus->getErrors()) ? implode('. ', $uploadStatus->getErrors()) : "Le document n'a pas pu être sauvegardé.");
                header("Location: " . $_SERVER['HTTP_REFERER']);
                exit;
            }

            $filePath = $uploadStatus->getFilename();
            $oldDocumentName = $internshipDocument->getDocument();
            $internshipDocument->setDocument($filePath);

            $updated = $this->service->update($internshipDocument);
            if ($updated) {
                Flash::success("Le document de stage a été mis à jour avec succès.");

                if ($oldDocumentName !== $filePath) {
                    $fileManager->deleteFIle($oldDocumentName, UPLOADS_PATH . INTERNSHIP_DOCUMENTS_DIR_NAME);
                }

                header("Location: " . VIEWS . "Internships/documents.php?id=" . $internshipDocument->getInternshipId());
                exit;
            }
        }

        $_SESSION['page_title'] = 'Stages';
        $_SESSION['subpage_title'] = 'Mise à jour du document de stage';

        // Check if form data is cached
        $formdata = Session::consume('__formdata__');

        if (!empty($formdata)) {
            $GLOBALS['form_data'] = json_decode($formdata, true);
        }

        $internshipDocumentTypesServices = new InternshipDocumentTypesServices();
        $document_types = $internshipDocumentTypesServices->getAll();

        $GLOBALS['document_types'] = $document_types;
        $GLOBALS['document'] = $internshipDocument;
    }

    /**
     * Delete internship
     *
     * @return void
     */
    public function delete()
    {
        AuthController::require_admin_priv();

        if (!isset($_GET['id']) || empty($_GET['id'])) {
            if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
                http_response_code(403);
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => "Mauvaise requête"]);

                exit;
            }

            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }

        // check if the internship's document exists
        $checkInternshipDocument = $this->service->getbYiD(intval($_GET['id']));
        if (!$checkInternshipDocument) {
            if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
                http_response_code(404);
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => "Aucun document de stage trouvé avec l'id " . $_GET['id']]);

                exit;
            }

            Flash::error("Aucun document de stage trouvé avec l'id " . $_GET['id']);

            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }

        $deleted = $this->service->delete(intval($_GET['id']));

        if ($deleted) {
            Flash::success("Le document de stage a été supprimé avec succès.");
        } else {
            if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
                http_response_code(500);
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => "Le document de stage n'a pas pu être supprimé. Veuillez réessayer !"]);

                exit;
            }

            Flash::error("Le document de stage n'a pas pu être supprimé. Veuillez réessayer !");
        }

        if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'message' => "Document de stage supprimé avec succès."]);

            exit;
        }

        header('Location: ' . $_SERVER['HTTP_REFERER']);
    }
}
