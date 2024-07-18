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

use App\Entity\Company;
use App\Service\CompanyServices;
use App\Service\ContractModelsServices;
use App\Service\ContractsServices;
use App\Service\ContractTypesServices;
use App\Service\EmployeesServices;
use App\View\Helpers\DateHelper;
use App\View\Helpers\UtilsHelper;
use Core\Auth\Auth;
use Core\FlashMessages\Flash;
use Core\Utils\Session;

require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'autoload.php';

use \Dompdf\Dompdf;

class ContractsController
{
    /**
     * @var ContractsServices $services Contracts services
     */
    private $service;

    function __construct()
    {
        $this->service = new ContractsServices();
    }

    /**
     * Index method
     * @return void
     */
    public function index()
    {
        $_SESSION['page_title'] = 'Contrats';
        unset($_SESSION['subpage_title']);

        $auth_user = (new Auth())->getAuthUser();
        if (empty($auth_user)) {
            AuthController::require_auth();
        }

        if (isset($_GET['status']) && !empty($_GET['status'])) {
            $status = $_GET['status'];
        } else {
            $status = 'all';
        }

        if ($auth_user->getRole()->getCode() == 'ADM') {
            $contracts = $this->service->getAll($status, true, true);

            $nb_expired = $this->service->countExpired();
            $GLOBALS['nb_expired'] = $nb_expired;
        } else {
            $contracts = $this->service->getAll($status, true, true, $auth_user->getId());
        }

        $GLOBALS['contracts'] = $contracts;
    }

    /**
     * Expired contracts
     *
     * @return void
     */
    public function expired()
    {
        AuthController::require_admin_priv();

        $_SESSION['page_title'] = 'Contrats';
        $_SESSION['subpage_title'] = 'Contrats expirés';

        $expiredContracts = $this->service->getExpired(true, true);
        $GLOBALS['expiredContracts'] = $expiredContracts;
    }

    /**
     * Contract details
     *
     * @return void
     */
    public function view()
    {
        $_SESSION['page_title'] = 'Contrats';
        $_SESSION['subpage_title'] = 'Détails';

        $auth_user = (new Auth())->getAuthUser();
        if (empty($auth_user)) {
            AuthController::require_auth();
        }

        if (!isset($_GET['id'])) {
            Flash::error("Mauvaise requête");
            header('Location: ' . VIEWS . 'Contracts');
            exit;
        }

        $contract = $this->service->get((int)$_GET['id'], true, true);
        if (!$contract) {
            Flash::error("Aucun contrat trouvé avec l'id " . $_GET['id']);
            header('Location: ' . VIEWS . 'Contracts');
            exit;
        }

        // Check privileges
        if ($auth_user->getRole()->getCode() != 'ADM') {
            if ($auth_user->getId() != $contract->getEmployeeId()) {
                Flash::error("Défaut de privilège. Permission non accordée");

                header('Location: ' . BASE_URL);
                exit;
            }
        }

        if (empty($contract->getPdf())) {
            $preview = $this->generate($contract->getId());
            $GLOBALS['preview'] = $preview;
        }

        $GLOBALS['contract'] = $contract;
    }

    /**
     * Preview contract before export it to PDF
     *
     * @return void
     */
    public function preview()
    {
        AuthController::require_admin_priv();

        if (!isset($_GET['id'])) {
            Flash::error("Mauvaise requête");
            header('Location: ' . VIEWS . 'Contracts');
            exit;
        }

        $contract = $this->service->get((int)$_GET['id'], true, true);
        if (!$contract) {
            Flash::error("Aucun contrat trouvé avec l'id " . $_GET['id']);
            header('Location: ' . VIEWS . 'Contracts');
            exit;
        }

        $_SESSION['page_title'] = 'Contrats';
        $_SESSION['subpage_title'] = 'Exporter en PDF';

        $preview = $this->generate($contract->getId());
        $GLOBALS['preview'] = $preview;
        $GLOBALS['contract'] = $contract;
    }

    public function exportpdf()
    {
        if (isset($_POST['export_contract'])) {
            $contract = $this->service->get($_POST['contract_id'], true, true);
            if (!$contract) {
                Flash::error("Aucun contrat trouvé avec l'id " . $_GET['id']);
                header('Location: ' . VIEWS . 'Contracts');
                exit;
            }

            $filepath = ASSETS_PATH . 'pdf';
            $filename =  $contract->getContractType()->getName();
            $filename .= '_' . $contract->getEmployee()->getFirstName() . '_' . $contract->getEmployee()->getLastName();
            $filename .= '_' . $contract->getId() . '.pdf';

            $html = '
                <!DOCTYPE html>
                <html lang="fr">
                    <head>
                        <title>' . $filename . '</title>
                        <meta charset="utf-8">
                        <meta name="author" content="Silevester D.">
                        <meta name="keywords" content="Congés, Contrats, Permissions, ERP">
                        <!-- <style>
                            @font-face {
                                font-family: "DejaVuSans";
                                font-style: normal;
                                font-weight: normal;
                                src: url(' . VENDOR . 'dompdf/vendor/dompdf/dompdf/lib/fonts/DejaVuSans.ttf);
                            }

                            @font-face {
                                font-family: "Helvetica";
                                font-style: normal;
                                font-weight: normal;
                                src: url(' . VENDOR . 'dompdf/vendor/dompdf/dompdf/lib/fonts/Helvetica.afm);
                            }

                            body * {
                                font-family: "DejaVuSans";
                            }
                            body .h1, body .h2, body .h3, body .h4, body .h5, body .h6 {
                                font-family: "Helvetica" !important;
                            }
                        </style> -->
                    </head>
                    <body>
                        ' . $_POST['content'] . '
                    </body>
                </html>
            ';

            $dompdf = new Dompdf();
            $dompdf->loadHtml($html);

            $options = $dompdf->getOptions();
            $options->setDefaultFont('Courier');
            $dompdf->setOptions($options);

            $dompdf->setPaper('A4', 'portrait');

            $dompdf->render();

            $pdf = $dompdf->output();

            // Create pdf dir if it doesn't exist
            if (!is_dir($filepath)) {
                $dir_created = mkdir($filepath, 0777, true);

                if (!$dir_created) {
                    Flash::error("Impossible de créer le repertoire de destination des PDF");
                    header("Location: " . $_SERVER['HTTP_REFERER']);
                    exit;
                }
            }

            $saved = file_put_contents($filepath . DS . $filename, $pdf);
            if ($saved !== false) {
                $contract->setPdf($filename);
                $this->service->update($contract);
            }

            header('Location: ' . ASSETS . 'pdf/' . $filename);
            exit;
        }

        header('Location: ' . VIEWS . 'Contracts');
        exit;
    }

    /**
     * Add new contract
     *
     * @return void
     */
    public function add()
    {
        AuthController::require_admin_priv();

        if (isset($_POST['add_contract'])) {
            $contract_id = $this->service->add($_POST);

            if ($contract_id) {
                Flash::success("Le contrat a été enregistré avec succès.");

                header("Location: " . VIEWS . "Contracts/view.php?id=" . $contract_id);
                exit;
            }
        }

        $_SESSION['page_title'] = 'Contrats';
        $_SESSION['subpage_title'] = 'Nouveau';

        $contractTypesServices = new ContractTypesServices();
        $employeesServices = new EmployeesServices();

        $contract_types = $contractTypesServices->getAll();
        $employees = $employeesServices->getAll();

        $GLOBALS['contract_types'] = $contract_types;
        $GLOBALS['employees'] = $employees;

        // Check if form data is cached
        $formdata = Session::consume('__formdata__');

        if (!empty($formdata)) {
            $GLOBALS['form_data'] = json_decode($formdata, true);
        }
    }

    /**
     * Update existing contract
     *
     * @return void
     */
    public function update()
    {
        AuthController::require_admin_priv();

        if (!isset($_GET['id'])) {
            Flash::error("Mauvaise requête");
            header('Location: ' . VIEWS . 'Contracts');
            exit;
        }

        $contract = $this->service->get((int)$_GET['id'], true, true);
        if (!$contract) {
            Flash::error("Aucun contrat trouvé avec l'id " . $_GET['id']);
            header('Location: ' . VIEWS . 'Contracts');
            exit;
        }

        // Check contract status
        if ($contract->getStatus() != 'pending' && $contract->getStatus() != 'active') {
            Flash::error("Vous ne pouvez pas modifier un contrat qui n'est ni en cours ni en attente ");
            header('Location: ' . VIEWS . 'Contracts');
            exit;
        }

        if (isset($_POST['update_contract'])) {
            $data = $_POST;
            $data['id'] = $_GET['id'];

            $updated = $this->service->update($data);
            if ($updated) {
                Flash::success("Le contrat a été mis à jour avec succès.");

                header("Location: " . VIEWS . "Contracts/view.php?id=" . $contract->getId());
                exit;
            }
        }

        $_SESSION['page_title'] = 'Contrats';
        $_SESSION['subpage_title'] = 'Mis à jour';

        // Check if form data is cached
        $formdata = Session::consume('__formdata__');

        if (!empty($formdata)) {
            $GLOBALS['form_data'] = json_decode($formdata, true);
        }

        $GLOBALS['contract'] = $contract;
    }

    /**
     * Extend expired contract
     *
     * @return void
     */
    public function extend()
    {
        AuthController::require_admin_priv();

        if (isset($_POST['extend_contract'])) {
            $extended = $this->service->extend($_POST['cid'], $_POST['extend_to']);
            if ($extended) {
                Flash::success("Le contrat a été prolongé avec succès.");
            }

            header('Location: ' . VIEWS . 'Contracts/view.php?id=' . $_POST['cid']);
            exit;
        }

        header('Location: ' . VIEWS . 'Contracts');
        exit;
    }

    /**
     * Terminate contract
     *
     * @return void
     */
    public function terminate()
    {
        AuthController::require_admin_priv();

        if (!isset($_GET['id']) || empty($_GET['id'])) {
            if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
                http_response_code(403);
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => "Mauvaise requête"]);

                exit;
            }

            header('Location: ' . VIEWS . 'Contracts');
            exit;
        }

        // check if the contract exists
        $check_contract = $this->service->get((int)$_GET['id']);
        if (!$check_contract) {
            if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
                http_response_code(404);
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => "Aucun contrat trouvé avec l'id " . $_GET['id']]);

                exit;
            }

            Flash::error("Aucun contrat trouvé avec l'id " . $_GET['id']);

            header('Location: ' . VIEWS . 'Contracts');
            exit;
        }

        $terminated = $this->service->terminate((int)$_GET['id']);

        if ($terminated) {
            Flash::success("Le contrat a été résilié avec succès.");
        } else {
            if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
                http_response_code(500);
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => "Le contrat n'a pas pu être résilié. Veuillez réessayer !"]);

                exit;
            }

            Flash::error("Le contrat n'a pas pu être résilié. Veuillez réessayer !");
        }

        if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'message' => "Contrat résilié avec succès."]);

            exit;
        }

        header('Location: ' . VIEWS . 'Contracts');
    }

    /**
     * Delete contract
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

            header('Location: ' . VIEWS . 'Contracts');
            exit;
        }

        // check if the contract exists
        $check_contract = $this->service->get((int)$_GET['id']);
        if (!$check_contract) {
            if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
                http_response_code(404);
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => "Aucun contrat trouvé avec l'id " . $_GET['id']]);

                exit;
            }

            Flash::error("Aucun contrat trouvé avec l'id " . $_GET['id']);

            header('Location: ' . VIEWS . 'Contracts');
            exit;
        }

        $deleted = $this->service->delete((int)$_GET['id']);

        if ($deleted) {
            Flash::success("Le contrat a été supprimé avec succès.");
        } else {
            if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
                http_response_code(500);
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => "Le contrat n'a pas pu être supprimé. Veuillez réessayer !"]);

                exit;
            }

            Flash::error("Le contrat n'a pas pu être supprimé. Veuillez réessayer !");
        }

        if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'message' => "Contrat supprimé avec succès."]);

            exit;
        }

        header('Location: ' . VIEWS . 'Contracts');
    }

    /**
     * Generate contract
     *
     * @param int $id Contract id
     * @return string|null 
     */
    public function generate($id): ?string
    {
        $employeesServices = new EmployeesServices();
        $companyServices = new CompanyServices();
        $contractModelsServices = new ContractModelsServices();

        $contract = $this->service->get($id);
        if (empty($contract)) {
            Flash::error("Impossible de trouver le contrat avec l'id " . $id);

            return null;
        }

        $employee = $employeesServices->getById($contract->getEmployeeId(), true);
        if (empty($employee)) {
            Flash::error("Impossible de trouver l'employé associé au contrat ");

            return null;
        }

        $company = $companyServices->getCompany();
        if (empty($company)) {
            $company = new Company();
        }

        $contractModel = $contractModelsServices->getCurrent($contract->getContractTypeId());
        if (empty($contractModel)) {
            Flash::error("Impossible de charger le modèle de contract associé.");

            return null;
        }

        $replacementsArray = [];
        $placeholders = [
            '$_company_name',
            '$_company_address',
            '$_employer_name',
            '$_candidate_name',
            '$_candidate_birth',
            '$_candidate_baddress',
            '$_candidate_nationality',
            '$_candidate_nss',
            '$_candidate_address',
            '$_job_start_date',
            '$_job_end_date',
            '$_job_object',
            '$_job_description',
            '$_job_delay',
            '$_job_salary',
            '$_hourly_rate',
            '$_generated_date',
        ];

        $replacementsArray[] = !empty($company->getName()) ? $company->getName() : '________________________________';
        $replacementsArray[] = !empty($company->getAddress()) ? $company->getAddress() : '________________________________';
        $replacementsArray[] = !empty($company->getDirectorName()) ? $company->getDirectorName() : '________________________________';
        $replacementsArray[] = !empty($employee->getFirstName()) ? $employee->getFirstName() . ' ' . $employee->getLastName() : '________________________________';
        $replacementsArray[] = '________________________________';
        $replacementsArray[] = '________________________________';
        $replacementsArray[] = '________________________________';
        $replacementsArray[] = '________________________________';
        $replacementsArray[] = '________________________________';
        $replacementsArray[] = !empty($contract->getStartDate()) ? $contract->getStartDate() : '________________________________';
        $replacementsArray[] = !empty($contract->getEndDate()) ? $contract->getEndDate() : '________________________________';
        $replacementsArray[] = !empty($contract->getJobObject()) ? $contract->getJobObject() : '________________________________';
        $replacementsArray[] = !empty($contract->getJobDescription()) ? $contract->getJobDescription() : '________________________________';
        if (!empty($contract->getEndDate())) {
            $_job_delay = DateHelper::dateDiff($contract->getStartDate(), $contract->getEndDate());
        } else {
            $_job_delay = '________________________________';
        }
        $replacementsArray[] = $_job_delay;
        $replacementsArray[] = !empty($contract->getJobSalary()) ? UtilsHelper::currency((float)$contract->getJobSalary()) : '________________________________';
        $replacementsArray[] = !empty($contract->getHourlyRate()) ? $contract->getHourlyRate() : '________________________________';
        $replacementsArray[] = date('d/m/Y');

        $generatedContract = str_replace($placeholders, $replacementsArray, $contractModel->getContent());

        return $generatedContract;
    }
}
