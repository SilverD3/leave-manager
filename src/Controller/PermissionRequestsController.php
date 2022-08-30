<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\PermissionRequestsServices;
use Core\Auth\Auth;
use Core\Auth\AuthSession;
use Core\FlashMessages\Flash;
use Core\Utils\Session;

require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'autoload.php';

class PermissionRequestsController
{
    /**
     * @var PermissionRequestsServices $services Permission Requests services
     */
    private $service;

    function __construct()
	{
		$this->service = new PermissionRequestsServices();
	}

    /**
     * Index method
     * @return void
     */
    public function index()
	{
        $_SESSION['page_title'] = 'Demandes de permission';
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
            $permission_requests = $this->service->getAll(true, null, $year);
        } else {
            $permission_requests = $this->service->getAll(true, $auth_user->getId(), $year);
        }

        $years = $this->service->getYears();
        $GLOBALS['years'] = $years;
        $GLOBALS['current_year'] = $year;
        $GLOBALS['permission_requests'] = $permission_requests;
    }

    public function permissions()
    {
        $_SESSION['page_title'] = 'Demandes de permission';
        $_SESSION['subpage_title'] = 'Permissions';

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
            $permissions = $this->service->getAllPermissions(true, null, $year);
        } else {
            $permissions = $this->service->getAllPermissions(true, $auth_user->getId(), $year);
        }

        $years = $this->service->getYears();
        $GLOBALS['years'] = $years;
        $GLOBALS['current_year'] = $year;
        $GLOBALS['permissions'] = $permissions;
    }

    /**
     * Add method
     *
     * @return void
     */
    public function add()
    {
        if (isset($_POST['add_permission_request'])) {
            $data = $_POST;

            $auth_user = (new Auth())->getAuthUser();
            $employee_id = $auth_user->getId();

            if (!empty($data['start_date_time'])) {
                $data['start_date'] = $data['start_date'] . ' ' . $data['start_date_time'];
                unset($data['start_date_time']);
            }

            if (!empty($data['end_date_time'])) {
                $data['end_date'] = $data['end_date'] . ' ' . $data['end_date_time'];
                unset($data['end_date_time']);
            }

            $data['employee_id'] = $employee_id;

            $permission_request_id = $this->service->add($data);

            if ($permission_request_id) {
                Flash::success("Le demande de permission a été enregistré avec succès.");

                header("Location: " . VIEWS . "PermissionRequests");
                exit;
            }
        }

        $_SESSION['page_title'] = 'Demandes de permission';
		$_SESSION['subpage_title'] = 'Ajout';

        // Check if form data is cached
		$formdata = Session::consume('__formdata__');
        
		if(!empty($formdata)) {
			$GLOBALS['form_data'] = json_decode($formdata, true);
		}
    }

    /**
     * Update method
     *
     * @return void
     */
    public function update()
    {
        if (!isset($_GET['id'])) {
			Flash::error("Mauvaise requête");
			header('Location: '.VIEWS . 'PermissionRequests');
			exit;
		}

        $permissionRequest = $this->service->get((int)$_GET['id']);
        
        if(!$permissionRequest) {
            Flash::error("Aucune demande de permission trouvée avec l'id ". $_GET['id']);
			header('Location: '.VIEWS . 'PermissionRequests');
			exit;
		}

        $auth_user = (new Auth())->getAuthUser();
        if (empty($auth_user) || $auth_user->getId() != $permissionRequest->getEmployeeId()) {
            Flash::error("Défaut de privilège. Permission non accordée");

			header('Location: ' . BASE_URL);
			exit;
        }

        if (isset($_POST['update_permission_request'])) {
			$data = $_POST;
			$data['id'] = $_GET['id'];

            if (!empty($data['start_date_time'])) {
                $data['start_date'] = $data['start_date'] . ' ' . $data['start_date_time'];
                unset($data['start_date_time']);
            }

            if (!empty($data['end_date_time'])) {
                $data['end_date'] = $data['end_date'] . ' ' . $data['end_date_time'];
                unset($data['end_date_time']);
            }

            $updated = $this->service->update($data);

			if ($updated) {
				Flash::success("La demande de permission a été mis à jour avec succès.");

                header("Location: " . VIEWS . "PermissionRequests/view?id=" . $data['id']);
                exit;
			}
        }

        $GLOBALS['permissionRequest'] = $permissionRequest;

        $_SESSION['page_title'] = 'Demandes de permission';
		$_SESSION['subpage_title'] = 'Editier';

        // Check if form data is cached
		$formdata = Session::consume('__formdata__');
        
		if(!empty($formdata)) {
			$GLOBALS['form_data'] = json_decode($formdata, true);
		}
    }

    public function view()
    {
        if (!isset($_GET['id'])) {
			Flash::error("Mauvaise requête");
			header('Location: '.VIEWS . 'PermissionRequests');
			exit;
		}

        $permissionRequest = $this->service->get((int)$_GET['id']);

        if(!$permissionRequest) {
			Flash::error("Aucune demande de permission trouvée avec l'id ". $_GET['id']);
			header('Location: '.VIEWS . 'PermissionRequests');
			exit;
		}

        $_SESSION['page_title'] = 'Demandes de permission';
		$_SESSION['subpage_title'] = 'Détails';

        $GLOBALS['permissionRequest'] = $permissionRequest;
    }

    public function approve()
    {
        AuthController::require_admin_priv();

        if(!isset($_GET['id']) || empty($_GET['id'])) {
            if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
                http_response_code(403);
				header('Content-Type: application/json');
				echo json_encode(['status' => 'error', 'message' => "Mauvaise requête"]);
	
				exit;
			}

			header('Location: ' . VIEWS . 'PermissionRequests');
			exit;
		}

		// check if the contract model exists
		$check_permission_request = $this->service->get((int)$_GET['id'], false);
		if(!$check_permission_request) {
			if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
                http_response_code(404);
				header('Content-Type: application/json');
				echo json_encode(['status' => 'error', 'message' => "Aucune demande de permission trouvée avec l'id ". $_GET['id']]);
	
				exit;
			}

			Flash::error("Aucune demande de permission trouvé avec l'id ". $_GET['id']);

			header('Location: '.VIEWS . 'PermissionRequests');
			exit;
		}

		$approved = $this->service->approve((int)$_GET['id']);

		if ($approved) {
			Flash::success("La demande de permission a été approuvé avec succès.");
		} else {
            if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
                http_response_code(500);
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => "La demande de perimssion n'a pas été approuvé. Veuillez réessayer !"]);

                exit;
            }

			Flash::error("La demande de permission n'a pas été approuvé. Veuillez réessayer !");
		}

		if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
			header('Content-Type: application/json');
			echo json_encode(['status' => 'success', 'message' => 'Demande de permission approuvée avec succès.']);

			exit;
		}

		header('Location: ' . VIEWS . 'PermissionRequests');
    }

    public function disapprove()
    {
        AuthController::require_admin_priv();

        if(!isset($_GET['id']) || empty($_GET['id'])) {
            if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
                http_response_code(403);
				header('Content-Type: application/json');
				echo json_encode(['status' => 'error', 'message' => "Mauvaise requête"]);
	
				exit;
			}

			header('Location: ' . VIEWS . 'PermissionRequests');
			exit;
		}

		// check if the contract model exists
		$check_permission_request = $this->service->get((int)$_GET['id'], false);
		if(!$check_permission_request) {
			if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
                http_response_code(404);
				header('Content-Type: application/json');
				echo json_encode(['status' => 'error', 'message' => "Aucune demande de permission trouvée avec l'id ". $_GET['id']]);
	
				exit;
			}

			Flash::error("Aucune demande de permission trouvé avec l'id ". $_GET['id']);

			header('Location: '.VIEWS . 'PermissionRequests');
			exit;
		}

		$approved = $this->service->disapprove((int)$_GET['id']);

		if ($approved) {
			Flash::success("La demande de permission a été rejeté avec succès.");
		} else {
            if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
                http_response_code(500);
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => "La demande de perimssion n'a pas été rejeté. Veuillez réessayer !"]);

                exit;
            }

			Flash::error("La demande de permission n'a pas été rejeté. Veuillez réessayer !");
		}

		if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
			header('Content-Type: application/json');
			echo json_encode(['status' => 'success', 'message' => 'Demande de permission rejetée avec succès.']);

			exit;
		}

		header('Location: ' . VIEWS . 'PermissionRequests');
    }

    public function delete()
    {
        AuthController::require_admin_priv();

        if(!isset($_GET['id']) || empty($_GET['id'])) {
            if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
                http_response_code(403);
				header('Content-Type: application/json');
				echo json_encode(['status' => 'error', 'message' => "Mauvaise requête"]);
	
				exit;
			}

			header('Location: ' . VIEWS . 'PermissionRequests');
			exit;
		}

		// check if the contract model exists
		$check_permission_request = $this->service->get((int)$_GET['id']);
		if(!$check_permission_request) {
			if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
                http_response_code(404);
				header('Content-Type: application/json');
				echo json_encode(['status' => 'error', 'message' => "Aucune demande de permission trouvée avec l'id ". $_GET['id']]);
	
				exit;
			}

			Flash::error("Aucune demande de permission trouvé avec l'id ". $_GET['id']);

			header('Location: '.VIEWS . 'PermissionRequests');
			exit;
		}

		$deleted = $this->service->delete((int)$_GET['id']);

		if ($deleted) {
			Flash::success("La demande de permission a été supprimé avec succès.");
		} else {
            if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
                http_response_code(500);
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => "La demande de perimssion n'a pas été supprimé. Veuillez réessayer !"]);

                exit;
            }

			Flash::error("La demande de permission n'a pas été supprimé. Veuillez réessayer !");
		}

		if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
			header('Content-Type: application/json');
			echo json_encode(['status' => 'success', 'message' => 'Demande de permission supprimée avec succès.']);

			exit;
		}

		header('Location: ' . VIEWS . 'PermissionRequests');
    }

}