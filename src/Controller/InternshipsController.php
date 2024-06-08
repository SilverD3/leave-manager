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

use App\Service\InternshipsServices;
use App\Service\EmployeesServices;
use App\Service\InternshipTypesServices;
use Core\Auth\Auth;
use Core\FileManager\FileManager;
use Core\FileManager\FileType;
use Core\FileManager\FileValidation;
use Core\FlashMessages\Flash;
use Core\Utils\Session;

require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'autoload.php';

class InternshipsController
{
    /**
     * @var InternshipsServices $services Internships services
     */
    private $service;

    function __construct()
    {
        $this->service = new InternshipsServices();
    }

    /**
     * Index method
     * @return void
     */
    public function index()
    {
        $_SESSION['page_title'] = 'Stages';
        unset($_SESSION['subpage_title']);

        AuthController::require_employee_priv();

        if (isset($_GET['status']) && !empty($_GET['status'])) {
            $status = $_GET['status'];
        } else {
            $status = 'all';
        }

        if (isset($_GET['keywords']) && !empty($_GET['keywords'])) {
            $keywords = $_GET['keywords'];
        } else {
            $keywords = null;
        }

        $internships = $this->service->getAll($status, $keywords);

        $nb_passed = $this->service->countPassed();

        $GLOBALS['status'] = $status;
        $GLOBALS['nb_passed'] = $nb_passed;
        $GLOBALS['internships'] = $internships;
    }

    /**
     * User's internships
     * 
     * @return void
     */
    public function myInternships()
    {
        $_SESSION['page_title'] = 'Stages';
        $_SESSION['subpage_title'] = 'Mes stages';

        $auth_user = (new Auth())->getAuthUser();
        if (empty($auth_user)) {
            AuthController::require_auth();
        }

        $internships = $this->service->getByUserId($auth_user->getId());

        $GLOBALS['internships'] = $internships;
    }

    /**
     * Passed internships
     *
     * @return void
     */
    public function passed()
    {
        AuthController::require_employee_priv();

        $_SESSION['page_title'] = 'Stages';
        $_SESSION['subpage_title'] = 'Stages passés';

        if (isset($_GET['keywords']) && !empty($_GET['keywords'])) {
            $keywords = $_GET['keywords'];
        } else {
            $keywords = null;
        }

        $internships = $this->service->getPassed($keywords);
        $GLOBALS['internships'] = $internships;
    }

    /**
     * Internship details
     *
     * @return void
     */
    public function view()
    {
        $_SESSION['page_title'] = 'Stages';
        $_SESSION['subpage_title'] = 'Détails';

        $auth_user = (new Auth())->getAuthUser();
        if (empty($auth_user)) {
            AuthController::require_auth();
        }

        if (!isset($_GET['id'])) {
            Flash::error("Mauvaise requête");
            header('Location: ' . VIEWS . 'Internships');
            exit;
        }

        $internship = $this->service->get((int)$_GET['id']);
        if (empty($internship)) {
            Flash::error("Aucun stage trouvé avec l'id " . $_GET['id']);
            header('Location: ' . VIEWS . 'Internships');
            exit;
        }

        // Check privileges
        if ($auth_user->getRole()->getCode() != 'ADM' && $auth_user->getRole()->getCode() != 'EMP') {
            if ($auth_user->getId() != $internship->getUserId()) {
                Flash::error("Défaut de privilège. Permission non accordée");

                header('Location: ' . BASE_URL);
                exit;
            }
        }

        $GLOBALS['internship'] = $internship;
    }

    /**
     * Add new internship
     *
     * @return void
     */
    public function add()
    {
        AuthController::require_admin_priv();

        if (isset($_POST['add_internship'])) {
            $internship_id = $this->service->add($_POST);

            if ($internship_id) {
                Flash::success("Le stage a été enregistré avec succès.");

                header("Location: " . VIEWS . "Internships/view.php?id=" . $internship_id);
                exit;
            }
        }

        $_SESSION['page_title'] = 'Stages';
        $_SESSION['subpage_title'] = 'Nouveau';

        $internshipTypesServices = new InternshipTypesServices();

        $internship_types = $internshipTypesServices->getAll();

        $GLOBALS['internship_types'] = $internship_types;

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
        AuthController::require_admin_priv();

        if (!isset($_GET['id'])) {
            Flash::error("Mauvaise requête");
            header('Location: ' . VIEWS . 'Internships');
            exit;
        }

        $internship = $this->service->get($_GET['id']);
        if (!$internship) {
            Flash::error("Aucun stage trouvé avec l'id " . $_GET['id']);
            header('Location: ' . VIEWS . 'Internships');
            exit;
        }

        // Check internship status
        if ($internship->getStatus() != 'pending' && $internship->getStatus() != 'active') {
            Flash::error("Vous ne pouvez pas modifier un stage qui n'est ni en cours ni en attente ");
            header('Location: ' . VIEWS . 'Internships');
            exit;
        }

        if (isset($_POST['update_internship'])) {
            $data = $_POST;
            $data['id'] = $_GET['id'];

            $updated = $this->service->update($data);
            if ($updated) {
                Flash::success("Le stage a été mis à jour avec succès.");

                header("Location: " . VIEWS . "Internships/view.php?id=" . $internship->getId());
                exit;
            }
        }

        $_SESSION['page_title'] = 'Stages';
        $_SESSION['subpage_title'] = 'Mise à jour';

        // Check if form data is cached
        $formdata = Session::consume('__formdata__');

        if (!empty($formdata)) {
            $GLOBALS['form_data'] = json_decode($formdata, true);
        }

        $internshipTypesServices = new InternshipTypesServices();
        $internship_types = $internshipTypesServices->getAll();

        $GLOBALS['internship_types'] = $internship_types;
        $GLOBALS['internship'] = $internship;
    }

    /**
     * Extend expired internship
     *
     * @return void
     */
    public function extend()
    {
        AuthController::require_admin_priv();

        if (isset($_POST['extend_internship'])) {
            $extended = $this->service->extend($_POST['iid'], $_POST['extend_to']);
            if ($extended) {
                Flash::success("Le stage a été prolongé avec succès.");
            }

            header('Location: ' . VIEWS . 'Internships/view.php?id=' . $_POST['iid']);
            exit;
        }

        header('Location: ' . VIEWS . 'Internships');
        exit;
    }

    /**
     * Assign a supervisor to the internship
     *
     * @return void
     */
    public function assignSupervisor()
    {
        AuthController::require_admin_priv();

        if (!isset($_GET['id']) || empty($_GET['id'])) {
            Flash::error("Requête mal formée");
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }

        if (isset($_POST['assign_supervisor'])) {
            $extended = $this->service->assignSupervisor($_POST['internship_id'], intval($_POST['supervisor_id']));
            if ($extended) {
                Flash::success("Le superviseur a été assigné avec succès.");
                header('Location: ' . VIEWS . 'Internships/view.php?id=' . $_POST['internship_id']);
                exit;
            }
        }

        $internship = $this->service->get($_GET['id']);
        if (!$internship) {
            Flash::error("Aucun stage trouvé avec l'id " . $_GET['id']);
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }

        $usersServices = new EmployeesServices();
        $employees = $usersServices->getEmployees();

        $GLOBALS['employees'] = $employees;
        $GLOBALS['internship'] = $internship;
    }

    /**
     * Upload report
     *
     * @return void
     */
    public function uploadReport()
    {
        AuthController::require_auth();

        if (isset($_POST['upload_internship_report']) && isset($_FILES['internship_report'])) {
            $fileManager = new FileManager();
            $fileValidation = new FileValidation();
            $fileValidation->setMaxSize(1024 * 1024 * 15);
            $fileValidation->setFileTypes([FileType::PDF, FileType::WORD]);

            $uploadStatus = $fileManager->saveFile($_FILES['internship_report'], $fileValidation, UPLOADS_PATH . INTERNSHIP_REPORTS_DIR_NAME);

            if (!$uploadStatus->hasSucceeded()) {
                Flash::error(!empty($uploadStatus->getErrors()) ? implode('. ', $uploadStatus->getErrors()) : "Le rapport n'a pas pu être sauvegardé.");
                header("Location: " . $_SERVER['HTTP_REFERER']);
                exit;
            }

            $filePath = $uploadStatus->getFilename();
            $extended = $this->service->saveReport($_POST['internship_id'], $filePath);

            $internship = $this->service->get($_POST['internship_id']);
            if (!empty($internship) && !empty($internship->getReportPath()) && $internship->getReportPath() !== $filePath) {
                $fileManager->deleteFile($internship->getReportPath(), UPLOADS_PATH . INTERNSHIP_REPORTS_DIR_NAME);
            }


            if ($extended) {
                Flash::success("Le rapport de stage a été enregistré avec succès.");
                header('Location: ' . VIEWS . 'Internships/view.php?id=' . $_POST['internship_id']);
            } else {
                header("Location: " . $_SERVER['HTTP_REFERER']);
            }

            exit;
        }

        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    }

    /**
     * Mark internship as completed
     *
     * @return void
     */
    public function complete()
    {
        AuthController::require_admin_priv();

        if (!isset($_GET['id']) || empty($_GET['id'])) {
            if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
                http_response_code(403);
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => "Mauvaise requête"]);

                exit;
            }

            header('Location: ' . VIEWS . 'Internships');
            exit;
        }

        // check if the internship exists
        $check_internship = $this->service->get((int)$_GET['id']);
        if (!$check_internship) {
            if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
                http_response_code(404);
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => "Aucun stage trouvé avec l'id " . $_GET['id']]);

                exit;
            }

            Flash::error("Aucun stage trouvé avec l'id " . $_GET['id']);

            header('Location: ' . VIEWS . 'Internships');
            exit;
        }

        $completed = $this->service->complete((int)$_GET['id']);

        if ($completed) {
            Flash::success("Le stage a été marqué comme terminé avec succès.");
        } else {
            if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
                http_response_code(500);
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => "Le stage n'a pas pu être marqué comme terminé. Veuillez réessayer !"]);

                exit;
            }

            Flash::error("Le stage n'a pas pu être marqué comme terminé. Veuillez réessayer !");
        }

        if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'message' => "Stage marqué comme terminé avec succès."]);

            exit;
        }

        header('Location: ' . VIEWS . 'Internships');
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

            header('Location: ' . VIEWS . 'Internships');
            exit;
        }

        // check if the internship exists
        $check_internship = $this->service->get((int)$_GET['id']);
        if (!$check_internship) {
            if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
                http_response_code(404);
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => "Aucun stage trouvé avec l'id " . $_GET['id']]);

                exit;
            }

            Flash::error("Aucun stage trouvé avec l'id " . $_GET['id']);

            header('Location: ' . VIEWS . 'Internships');
            exit;
        }

        $deleted = $this->service->delete((int)$_GET['id']);

        if ($deleted) {
            Flash::success("Le stage a été supprimé avec succès.");
        } else {
            if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
                http_response_code(500);
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => "Le stage n'a pas pu être supprimé. Veuillez réessayer !"]);

                exit;
            }

            Flash::error("Le stage n'a pas pu être supprimé. Veuillez réessayer !");
        }

        if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'message' => "Stage supprimé avec succès."]);

            exit;
        }

        header('Location: ' . VIEWS . 'Internships');
    }
}
