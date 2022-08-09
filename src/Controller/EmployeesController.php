<?php
declare(strict_types=1);

namespace App\Controller;

require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'autoload.php';

use App\Service\EmployeesServices;
use App\Service\PermissionRequestsServices;
use App\Service\ContractTypesServices;
use App\Service\ContractsServices;
use Core\FlashMessages\Flash;

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
		$permissionRequestsServices = new PermissionRequestsServices();
		$contractTypesServices = new ContractTypesServices();
		$contractsServices = new ContractsServices();

		$nb_employees = $this->service->countAll();
		$nb_contract_types = $contractTypesServices->countAll();

		$nb_permission_requests = $permissionRequestsServices->countAll();
		$nb_approved_permission_requests = $permissionRequestsServices->countAll('approved');
		$nb_rejected_permission_requests = $permissionRequestsServices->countAll('rejected');

		$nb_contracts = $contractsServices->countAll();
		$nb_active_contracts = $contractsServices->countAll('active');
		$nb_terminated_contracts = $contractsServices->countAll('terminated');

		$recent_permission_requests = $permissionRequestsServices->getLatest(5);

		$GLOBALS['stats'] = [
			'nb_employees' => $nb_employees,
			'nb_contract_types' => $nb_contract_types,
			'nb_permission_requests' => $nb_permission_requests,
			'nb_approved_permission_requests' => $nb_approved_permission_requests,
			'nb_rejected_permission_requests' => $nb_rejected_permission_requests,
			'nb_contracts' => $nb_contracts,
			'nb_active_contracts' => $nb_active_contracts,
			'nb_terminated_contracts' => $nb_terminated_contracts,
		];

		$GLOBALS['recent_permission_requests'] = $recent_permission_requests;
	}

	public function index()
	{

	}

	public function add()
	{

	}

	public function edit()
	{

	}

	public function details()
	{

	}

	public function delete()
	{

	}
}