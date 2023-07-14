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

use App\Service\ContractTypesServices;
use Core\FlashMessages\Flash;
use Core\Utils\Session;

require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'autoload.php';

class ContractTypesController
{
	/**
	 * @var ContractTypesServices $services Contract type services
	 */
	private $service;

	function __construct()
	{
		$this->service = new ContractTypesServices();
	}

	/**
	 * Index method
	 * @return void
	 */
	public function index()
	{
		$_SESSION['page_title'] = 'Types de contrat';
		unset($_SESSION['subpage_title']);

		$contract_types = $this->service->getAll();

		$GLOBALS['contract_types'] = $contract_types;
	}

	/**
	 * Add contract type method
	 *
	 * @return void
	 */
	public function add()
	{
		if (isset($_POST['add_contract_type'])) {
			$contratType = $this->service->add($_POST);

			if ($contratType) {
				Flash::success("Le type de contrat a été ajouté avec succès.");

				header("Location: " . VIEWS . "ContractTypes");
				exit;
			}
		}

		$_SESSION['page_title'] = 'Types de contrat';
		$_SESSION['subpage_title'] = 'Ajout';

		// Check if form data is cached
		$formdata = Session::consume('__formdata__');

		if (!empty($formdata)) {
			$GLOBALS['form_data'] = json_decode($formdata, true);
		}
	}

	public function update()
	{
		if (!isset($_GET['id'])) {
			Flash::error("Mauvaise requête");
			header('Location: ' . VIEWS . 'ContractTypes');
			exit;
		}

		$contractType = $this->service->get((int)$_GET['id']);

		if (!$contractType) {
			Flash::error("Aucun type de contrat trouvé avec l'id " . $_GET['id']);
			header('Location: ' . VIEWS . 'ContractTypes');
			exit;
		}

		if (isset($_POST['update_contract_type'])) {
			$data = $_POST;
			$data['id'] = $_GET['id'];
			$updated = $this->service->update($data);

			if ($updated) {
				Flash::success("Le type de contrat a été mis à jour avec succès.");

				header("Location: " . VIEWS . "ContractTypes");
				exit;
			}
		}

		$GLOBALS['contract_type'] = $contractType;

		$_SESSION['page_title'] = 'Types de contrat';
		$_SESSION['subpage_title'] = 'Editier';

		// Check if form data is cached
		$formdata = Session::consume('__formdata__');

		if (!empty($formdata)) {
			$GLOBALS['form_data'] = json_decode($formdata, true);
		}
	}

	public function delete()
	{
		if (!isset($_GET['id']) || empty($_GET['id'])) {
			header('Location: ' . VIEWS . 'ContractTypes');
			exit;
		}

		// check if the contract type exists
		$check_contract_type = $this->service->get((int)$_GET['id']);
		if (!$check_contract_type) {
			if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
				header('Content-Type: application/json');
				echo json_encode(['status' => 'success', 'message' => "Aucun type de contrat trouvé avec l'id " . $_GET['id']]);

				exit;
			}

			Flash::error("Aucun type de contrat trouvé avec l'id " . $_GET['id']);

			header('Location: ' . VIEWS . 'ContractTypes');
			exit;
		}


		$deleted = $this->service->delete((int)$_GET['id']);

		if ($deleted) {
			Flash::success("Le type de contrat a été supprimé avec succès.");
		} else {
			Flash::error("Le type de contrat n'a pas été supprimé. Veuillez réessayer !");
		}

		if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
			header('Content-Type: application/json');
			echo json_encode(['status' => 'success', 'message' => 'Type de contrat supprimé avec succès.']);

			exit;
		}

		header('Location: ' . VIEWS . 'ContractTypes');
	}
}
