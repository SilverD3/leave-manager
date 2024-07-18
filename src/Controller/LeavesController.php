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

use App\Service\ConfigsServices;
use App\Service\EmployeesServices;
use App\Service\LeavesServices;
use App\Service\PermissionRequestsServices;
use App\View\Helpers\DateHelper;
use Core\Auth\Auth;
use Core\FlashMessages\Flash;
use Core\Utils\Session;

require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'autoload.php';

class LeavesController
{
    /**
     * @var LeavesServices $services Permission Requests services
     */
    private $service;

    /**
     * @var ConfigsServices $configsServices Configs Services
     */
    private $configsServices;

    function __construct()
    {
        $this->service = new LeavesServices();
        $this->configsServices = new ConfigsServices();
    }

    /**
     * Index method
     * @return void
     */
    public function index()
    {
        $_SESSION['page_title'] = 'Congés';
        unset($_SESSION['subpage_title']);

        $auth_user = (new Auth())->getAuthUser();

        if (empty($auth_user)) {
            AuthController::require_auth();
        }

        if (isset($_GET['year']) && !empty($_GET['year'])) {
            $year = $_GET['year'];
        } else {
            $year = date('Y');
        }

        if ($auth_user->getRole()->getCode() == 'ADM') {
            $leaves = $this->service->getAll($year, true);
        } else {
            $leaves = $this->service->getAll($year, true, $auth_user->getId());
        }

        $years = $this->service->getYears();
        $GLOBALS['years'] = $years;
        $GLOBALS['current_year'] = $year;
        $GLOBALS['leaves'] = $leaves;
    }

    public function calendar()
    {
        $_SESSION['page_title'] = 'Congés';
        $_SESSION['subpage_title'] = 'Calendrier';
    }

    /**
     * Get leaves by period
     *
     * @return void
     */
    public function getByPeriodJson()
    {
        if (!isset($_GET['start']) || !isset($_GET['end'])) {
            die("Veuillez spécifier une période !");
        }

        $start = date('Y-m-d H:i:s', strtotime($_GET['start']));
        $end = date('Y-m-d H:i:s', strtotime($_GET['end']));

        $leaves = $this->service->getByPeriod($start, $end);

        $json_leaves = [];

        if (!empty($leaves)) {
            foreach ($leaves as $leave) {
                $leave_infos = [
                    'id' => $leave->getId(),
                    'title' => 'Congés de ' . $leave->getEmployee()->getFirstName() . ' ' . $leave->getEmployee()->getLastName(),
                    'start' => $leave->getStartDate(),
                    'end' => $leave->getEndDate(),
                    'url' => VIEWS . 'Leaves/view.php?id=' . $leave->getId()
                ];

                $json_leaves[] = $leave_infos;
            }
        }

        echo json_encode($json_leaves);
    }

    public function view()
    {
        if (!isset($_GET['id'])) {
            Flash::error("Mauvaise requête");
            header('Location: ' . VIEWS . 'Employees');
            exit;
        }

        // check if the employee exists
        $checkLeave = $this->service->get($_GET['id']);
        if (!$checkLeave) {
            Flash::error("Aucun congé trouvé avec l'id " . $_GET['id']);
            header('Location: ' . VIEWS . 'Leaves');
            exit;
        }

        $_SESSION['page_title'] = 'Congés';
        $_SESSION['subpage_title'] = 'Détails';


        $leave_nb_days = (int)$this->configsServices->getByCode('LM_LEAVE_NB_DAYS')->getValue();
        $nb_spent_days = $this->service->getSpentDays($checkLeave->getEmployeeId(), $checkLeave->getYear());
        $otherLeaves = $this->service->getByEmployeeId($checkLeave->getEmployeeId(), $checkLeave->getYear(), false, false, [$checkLeave->getId()]);

        $reduce = $this->configsServices->getByCode('LM_PERMISSION_REDUCE_LEAVE')->getValue();
        if ($reduce == 'OUI') {
            $permissionsServices = new PermissionRequestsServices();
            $spent_days_in_permissions = $permissionsServices->getSentDays($checkLeave->getEmployeeId(), $checkLeave->getYear());
            $GLOBALS['spent_days_in_permissions'] = $spent_days_in_permissions;
        }

        $GLOBALS['leave_nb_days'] = $leave_nb_days;
        $GLOBALS['nb_spent_days'] = $nb_spent_days;
        $GLOBALS['nb_remaining_days'] = ($leave_nb_days - $nb_spent_days > 0 ? $leave_nb_days - $nb_spent_days : 0);
        $GLOBALS['leave'] = $checkLeave;
        $GLOBALS['other_leaves'] = $otherLeaves;
    }

    public function add()
    {
        AuthController::require_admin_priv();

        if (isset($_POST['add_leave'])) {
            // First, check if the employe can take a leave
            $nb_remaining_maturation_time = $this->service->getRemainingMaturationTime(intval($_POST['employee_id']));
            
            if ($nb_remaining_maturation_time !== 0) {
                Flash::error("Il est reste $nb_remaining_maturation_time jour(s) avant que cet employé puisse prendre des congés.");
            } else {
                $leave_id = $this->service->add($_POST);
    
                if ($leave_id) {
                    Flash::success("Le congé a été planifié avec succès.");
    
                    header("Location: " . VIEWS . "Leaves");
                    exit;
                }
            }

        }

        $_SESSION['page_title'] = 'Congés';
        $_SESSION['subpage_title'] = 'Planifier';

        $employeesServices = new EmployeesServices();

        $employees = $employeesServices->getAll();
        $leaveNbDaysConfig = $this->configsServices->getByCode('LM_LEAVE_NB_DAYS');
        $leave_nb_days = (int)$leaveNbDaysConfig->getValue();

        $GLOBALS['employees'] = $employees;
        $GLOBALS['leave_nb_days'] = $leave_nb_days;

        // Check if form data is cached
        $formdata = Session::consume('__formdata__');

        if (!empty($formdata)) {
            $GLOBALS['form_data'] = json_decode($formdata, true);
        } elseif(isset($_POST['add_leave'])) {
            $GLOBALS['form_data'] = $_POST;
        }
    }

    /**
     * Generate leave for many employees
     *
     * @return void
     */
    public function generate()
    {
    }

    public function update()
    {
        AuthController::require_admin_priv();

        if (!isset($_GET['id'])) {
            Flash::error("Mauvaise requête");
            header('Location: ' . VIEWS . 'Employees');
            exit;
        }

        // check if the employee exists
        $checkLeave = $this->service->get($_GET['id']);
        if (!$checkLeave) {
            Flash::error("Aucun congé trouvé avec l'id " . $_GET['id']);
            header('Location: ' . VIEWS . 'Leaves');
            exit;
        }

        if (isset($_POST['update_leave'])) {
            $data = $_POST;
            $data['id'] = $_GET['id'];

            $updated = $this->service->update($data);

            if ($updated) {
                Flash::success("Le congé a été mis à jour avec succès.");

                header("Location: " . VIEWS . "Leaves/view.php?id=" . $_GET['id']);
                exit;
            }
        }

        $_SESSION['page_title'] = 'Congés';
        $_SESSION['subpage_title'] = 'Mise à jour';

        $leaveNbDaysConfig = $this->configsServices->getByCode('LM_LEAVE_NB_DAYS');
        $leave_nb_days = (int)$leaveNbDaysConfig->getValue();

        $GLOBALS['leave_nb_days'] = $leave_nb_days;
        $GLOBALS['leave'] = $checkLeave;

        // Check if form data is cached
        $formdata = Session::consume('__formdata__');

        if (!empty($formdata)) {
            $GLOBALS['form_data'] = json_decode($formdata, true);
        }
    }

    /**
     * Delete a leave
     *
     * @return void
     */
    public function delete()
    {
        AuthController::require_admin_priv();

        if (!isset($_GET['id']) || empty($_GET['id'])) {
            if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'success', 'message' => 'Mauvaise requête']);

                exit;
            }

            header('Location: ' . VIEWS . 'Leaves');
            exit;
        }

        // check if the leave exists
        $checkLeave = $this->service->get($_GET['id'], false);
        if (!$checkLeave) {
            if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'success', 'message' => "Aucun congé trouvé avec l'id " . $_GET['id']]);

                exit;
            }

            Flash::error("Aucun congé trouvé avec l'id " . $_GET['id']);

            header('Location: ' . VIEWS . 'Leaves');
            exit;
        }

        $deleted = $this->service->delete((int)$_GET['id']);

        if ($deleted) {
            Flash::success("Le congé a été supprimé avec succès.");
        } else {
            Flash::error("Le congé n'a pas été supprimé. Veuillez réessayer !");
        }

        if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'message' => 'Congé supprimé avec succès.']);

            exit;
        }

        header('Location: ' . VIEWS . 'Leaves');
    }

    /**
     * Get working days within a time period
     *
     * Ajax only
     */
    public function getNbWorkingDays()
    {
        if (!isset($_GET['from']) || !isset($_GET['to'])) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => "Mauvaise requête"]);

            exit;
        }

        if (isset($_GET['year']) && !empty($_GET['year'])) {
            $year = $_GET['year'];
        } else {
            $year = date('Y');
        }

        $nb_working_days = $this->service->getWorkingDays($_GET['from'], $_GET['to'], $year);

        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'nb_working_days' => $nb_working_days]);
        exit;
    }

    /**
     * Get leave days that an employee has spent
     *
     * Ajax only
     */
    public function getSpentLeaveDays()
    {
        if (!isset($_GET['eid'])) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => "Mauvaise requête"]);

            exit;
        }

        $year = (isset($_GET['year']) && !empty($_GET['year'])) ? $_GET['year'] : date('Y');

        $nb_spent_days = $this->service->getSpentDays($_GET['eid'], (int)$year);
        $nb_remaining_maturation_time = $this->service->getRemainingMaturationTime(intval($_GET['eid']));

        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'nb_remaining_maturation_time' => $nb_remaining_maturation_time,
            'nb_spent_days' => $nb_spent_days
        ]);

        exit;
    }

    public function getWorkingDaysAndHours()
    {
        $workBeginAt = $this->configsServices->getByCode('LM_WORK_BEGIN_AT')->getValue();
        $workEndAt = $this->configsServices->getByCode('LM_WORK_END_AT')->getValue();
        $workingDays = DateHelper::daysNumbers(explode(',', $this->configsServices->getByCode('LM_WORKING_DAYS')->getValue()));

        $json_response = ['businessDays' => $workingDays, 'workEndAt' => $workEndAt, 'workBeginAt' => $workBeginAt];

        echo json_encode($json_response);
    }
}