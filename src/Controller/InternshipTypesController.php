<?php

declare(strict_types=1);

/**
 * Leave manager : Simple app for contract and leave management.
 *
 * @copyright Copyright (c) Silevester D. (https://github.com/SilverD3)
 * @link      https://github.com/SilverD3/leave-manager Leave Manager Project
 * @since     2.0 (2024)
 */

namespace App\Controller;

use App\Service\InternshipTypesServices;
use Core\FlashMessages\Flash;
use Core\Utils\Session;

require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'autoload.php';

class InternshipTypesController
{
	/**
	 * @var InternshipTypesServices $services Internship type services
	 */
	private $service;

	function __construct()
	{
		$this->service = new InternshipTypesServices();
	}

	/**
	 * Index method
	 * @return void
	 */
	public function index()
	{
		$_SESSION['page_title'] = 'Types de stage';
		unset($_SESSION['subpage_title']);

		$internship_types = $this->service->getAll();

		$GLOBALS['internship_types'] = $internship_types;
	}

	/**
	 * Add internship type method
	 *
	 * @return void
	 */
	public function add()
	{
		if (isset($_POST['add_internship_type'])) {
			$internshipType = $this->service->add($_POST);

			if ($internshipType) {
				Flash::success("Le type de stage a été ajouté avec succès.");

				header("Location: " . VIEWS . "InternshipTypes");
				exit;
			}
		}

		$_SESSION['page_title'] = 'Types de stage';
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
			header('Location: ' . VIEWS . 'InternshipTypes');
			exit;
		}

		$internshipType = $this->service->get((int)$_GET['id']);

		if (!$internshipType) {
			Flash::error("Aucun type de stage trouvé avec l'id " . $_GET['id']);
			header('Location: ' . VIEWS . 'InternshipTypes');
			exit;
		}

		if (isset($_POST['update_internship_type'])) {
			$data = $_POST;
			$data['id'] = $_GET['id'];
			$updated = $this->service->update($data);

			if ($updated) {
				Flash::success("Le type de stage a été mis à jour avec succès.");

				header("Location: " . VIEWS . "InternshipTypes");
				exit;
			}
		}

		$GLOBALS['internship_type'] = $internshipType;

		$_SESSION['page_title'] = 'Types de stage';
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
			header('Location: ' . VIEWS . 'InternshipTypes');
			exit;
		}

		// check if the internship type exists
		$check_internship_type = $this->service->get((int)$_GET['id']);
		if (!$check_internship_type) {
			if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
				header('Content-Type: application/json');
				echo json_encode(['status' => 'success', 'message' => "Aucun type de stage trouvé avec l'id " . $_GET['id']]);

				exit;
			}

			Flash::error("Aucun type de stage trouvé avec l'id " . $_GET['id']);

			header('Location: ' . VIEWS . 'InternshipTypes');
			exit;
		}


		$deleted = $this->service->delete((int)$_GET['id']);

		if ($deleted) {
			Flash::success("Le type de stage a été supprimé avec succès.");
		} else {
			Flash::error("Le type de stage n'a pas été supprimé. Veuillez réessayer !");
		}

		if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
			header('Content-Type: application/json');
			echo json_encode(['status' => 'success', 'message' => 'Type de stage supprimé avec succès.']);

			exit;
		}

		header('Location: ' . VIEWS . 'InternshipTypes');
	}
}
