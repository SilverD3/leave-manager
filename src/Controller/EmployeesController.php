<?php
declare(strict_types=1);

namespace App\Controller;

require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'autoload.php';

use App\Service\EmployeesServices;
use App\Service\PermissionRequestsServices;
use App\Service\ContractTypesServices;
use App\Service\ContractsServices;
use App\Service\LeavesServices;
use App\Service\RolesServices;
use Core\Auth\Auth;
use Core\Auth\PasswordHasher;
use Core\FlashMessages\Flash;
use Core\Utils\Session;

/**
 * Employees Controller
 */
class EmployeesController
{
	private $service;

	function __construct()
	{
		$this->service = new EmployeesServices();
	}

	public function dashboard()
	{
		// Set page_title
		$_SESSION['page_title'] = 'Tableau de bord';
		unset($_SESSION['subpage_title']);
		$auth_user = (new Auth())->getAuthUser();

		if (empty($auth_user)) {
			Flash::error("Veuillez vous connecter avant de continuer.");

			header('Location: ' . AuthController::UNAUTHORIZED_REDIRECT . '?redirect=' . $_SERVER['REQUEST_URI']);
			exit;
		}

		$stats = [];

		$permissionRequestsServices = new PermissionRequestsServices();
		$contractsServices = new ContractsServices();
		$leavesServices = new LeavesServices();

		if ($auth_user->getRole()->getCode() == 'ADM') {
			$contractTypesServices = new ContractTypesServices();
			
			$nb_contract_types = $contractTypesServices->countAll();
			$nb_contracts = $contractsServices->countAll();
			$nb_active_contracts = $contractsServices->countAll('active');
			$nb_terminated_contracts = $contractsServices->countAll('terminated');

			$stats['nb_contract_types'] = $nb_contract_types;
			
			$nb_permission_requests = $permissionRequestsServices->countAll();
			$nb_approved_permission_requests = $permissionRequestsServices->countAll('approved');
			$nb_rejected_permission_requests = $permissionRequestsServices->countAll('disapproved');
			$recent_permission_requests = $permissionRequestsServices->getLatest(5);
		} else {
			$nb_contracts = $contractsServices->countAll('all', $auth_user->getId());
			$nb_active_contracts = $contractsServices->countAll('active', $auth_user->getId());
			$nb_terminated_contracts = $contractsServices->countAll('terminated', $auth_user->getId());

			$nb_permission_requests = $permissionRequestsServices->countAll('all', $auth_user->getId());
			$nb_approved_permission_requests = $permissionRequestsServices->countAll('approved', $auth_user->getId());
			$nb_rejected_permission_requests = $permissionRequestsServices->countAll('disapproved', $auth_user->getId());
			$recent_permission_requests = $permissionRequestsServices->getLatest(5, $auth_user->getId());
		}

		$nb_employees = $this->service->countAll();
		$nbCurrentLeaves = $leavesServices->getByPeriod(date('Y-m-d'), date('Y-m-d'), true, false);
		
		$stats['nb_current_leaves'] = $nbCurrentLeaves;
		$stats['nb_contracts'] = $nb_contracts;
		$stats['nb_active_contracts'] = $nb_active_contracts;
		$stats['nb_terminated_contracts'] = $nb_terminated_contracts;
		$stats['nb_employees'] = $nb_employees;
		$stats['nb_permission_requests'] = $nb_permission_requests;
		$stats['nb_approved_permission_requests'] = $nb_approved_permission_requests;
		$stats['nb_rejected_permission_requests'] = $nb_rejected_permission_requests;

		$GLOBALS['stats'] = $stats; 

		$GLOBALS['recent_permission_requests'] = $recent_permission_requests;
	}

	public function index()
	{
		AuthController::require_admin_priv();

		$_SESSION['page_title'] = 'Employés';
		unset($_SESSION['subpage_title']);

		$employees = $this->service->getAll(true);

		$GLOBALS['employees'] = $employees;
	}

	public function add()
	{
		AuthController::require_admin_priv();

		if (isset($_POST['add_employee'])) {
			$employee_id = $this->service->add($_POST);

			if ($employee_id) {
				Flash::success("L'employé a été ajouté avec succès.");

                header("Location: " . VIEWS . "Employees");
                exit;
			}
		}

		$_SESSION['page_title'] = 'Employés';
		$_SESSION['subpage_title'] = 'Ajout';

		$rolesServices = new RolesServices();

		$roles = $rolesServices->getAll();

		$GLOBALS['roles'] = $roles;

		// Check if form data is cached
		$formdata = Session::consume('__formdata__');
		if(!empty($formdata)) {
			$GLOBALS['form_data'] = json_decode($formdata, true);
		}
	}

	public function update()
	{
		AuthController::require_admin_priv();

		if (!isset($_GET['id'])) {
			Flash::error("Mauvaise requête");
			header('Location: '.VIEWS . 'Employees');
			exit;
		}

		// check if the employee exists
		$checkEmployee = $this->service->getById($_GET['id']);
		if(!$checkEmployee) {
			Flash::error("Aucun employé trouvé avec l'id ". $_GET['id']);
			header('Location: '.VIEWS . 'Employees');
			exit;
		}

		if (isset($_POST['update_employee'])) {
			$data = $_POST;
			$data['id'] = $_GET['id'];
			$employee_id = $this->service->update($data);

			if ($employee_id) {
				Flash::success("L'employé a été mis à jour avec succès.");

                header("Location: " . VIEWS . "Employees/view.php?id=" . $_GET['id']);
                exit;
			}
		}

		$_SESSION['page_title'] = 'Employés';
		$_SESSION['subpage_title'] = 'Mise à jour';

		$rolesServices = new RolesServices();

		$roles = $rolesServices->getAll();
		$employee = $this->service->getById($_GET['id']);

		$GLOBALS['roles'] = $roles;
		$GLOBALS['employee'] = $employee;

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
			header('Location: '.VIEWS . 'Employees');
			exit;
		}

		$auth_user = (new Auth())->getAuthUser();
		if (empty($auth_user)) {
			Flash::error("Veuillez vous connecter avant de continuer.");

			header('Location: ' . AuthController::UNAUTHORIZED_REDIRECT . '?redirect=' . $_SERVER['REQUEST_URI']);
			exit;
		}

		if ($auth_user->getRole()->getCode() != 'ADM') {
			header('Location: '.VIEWS . 'Employees/profile.php');
			exit;
		}

		$_SESSION['page_title'] = 'Employés';
		$_SESSION['subpage_title'] = 'Détails';

		$employee = $this->service->getById($_GET['id']);

		if (empty($employee)) {
			Flash::error("Aucun employé trouvé avec l'id " . $_GET['id']);

			header('Location: '.VIEWS . 'Employees');
			exit;
		}

		$leavesServices = new LeavesServices();
		$isInVaccations = $leavesServices->isEmployeeInLeave($employee->getId());

		$GLOBALS['employee'] = $employee;
		$GLOBALS['isInVaccations'] = $isInVaccations;
	}

	public function profile()
	{
		$auth_user = (new Auth())->getAuthUser();
		if (empty($auth_user)) {
			Flash::error("Veuillez vous connecter avant de continuer.");

			header('Location: ' . AuthController::UNAUTHORIZED_REDIRECT . '?redirect=' . $_SERVER['REQUEST_URI']);
			exit;
		}
		
		$employee = $this->service->getById($auth_user->getId());

		if (empty($employee)) {
			Flash::error("Une erreur est survenue. Veuillez vous reconnecter.");
			header("Location: " . BASE_URL);
		}

		if (isset($_POST['edit_profile'])) {
			$passwordHasher = new PasswordHasher();
			$data = $_POST;

			// check password
			if (!$passwordHasher->check($data['upwd'], $employee->getPwd())) {
				Flash::error("Mot de passe incorrect");
			} elseif(!empty($data['password']) && $data['password'] != $data['cfmpwd']) {
				Flash::error("Les mots de passe ne correspondent pas");
			} else {
				$data['id'] = $employee->getId();
				$updated = $this->service->update($data);

				if ($updated) {
					Flash::success("Vos informations ont été mis à jour avec succès. Veuillez vous reconnecter pour appliquer les modifications.");
				}
			}
		}

		$_SESSION['page_title'] = 'Employés';
		$_SESSION['subpage_title'] = 'Profil';

		$GLOBALS['employee'] = $employee;
	}

	public function delete()
	{
		AuthController::require_admin_priv();

		if(!isset($_GET['id']) || empty($_GET['id'])) {
			header('Location: ' . VIEWS . 'Employees');
			exit;
		}

		// check if the employee exists
		$checkEmployee = $this->service->getById($_GET['id']);
		if(!$checkEmployee) {
			if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
				header('Content-Type: application/json');
				echo json_encode(['status' => 'success', 'message' => "Aucun employé trouvé avec l'id ". $_GET['id']]);
	
				exit;
			}

			Flash::error("Aucun employé trouvé avec l'id ". $_GET['id']);

			header('Location: '.VIEWS . 'Employees');
			exit;
		}


		$deleted = $this->service->delete((int)$_GET['id']);

		if ($deleted) {
			Flash::success("L'employé a été supprimé avec succès.");
		} else {
			Flash::error("L'employé n'a pas été supprimé. Veuillez réessayer !");
		}

		if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
			header('Content-Type: application/json');
			echo json_encode(['status' => 'success', 'message' => 'Employé supprimé avec succès.']);

			exit;
		}

		header('Location: ' . VIEWS . 'Employees');
	}
}