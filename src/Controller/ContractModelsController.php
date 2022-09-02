<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\ContractModelsServices;
use App\Service\ContractTypesServices;
use Core\FlashMessages\Flash;
use Core\Utils\Session;

require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'autoload.php';

class ContractModelsController
{
    /**
     * @var ContractModelsServices $services Contract models services
     */
    private $service;

    function __construct()
	{
		$this->service = new ContractModelsServices();
	}

    /**
     * Index method
     * @return void
     */
    public function index()
	{
		$_SESSION['page_title'] = 'Modèles de contrat';
        unset($_SESSION['subpage_title']);

        $contract_models = $this->service->getAll(true);

        $GLOBALS['contract_models'] = $contract_models;
	}

    /**
     * Add contract model method
     *
     * @return void
     */
    public function add()
    {
        if (isset($_POST['add_contract_model'])) {
            $data = $_POST;

            if(isset($data['is_current']) && $data['is_current'] == 1) $data['is_current'] = true;
            
            $contratModelId = $this->service->add($data);

            if ($contratModelId) {
                Flash::success("Le modèle de contrat a été ajouté avec succès.");

                header("Location: " . VIEWS . "ContractModels/view?id=" . $contratModelId);
                exit;
            }
        }

        $contractTypesServices = new ContractTypesServices();

        $_SESSION['page_title'] = 'Modèles de contrat';
		$_SESSION['subpage_title'] = 'Ajout';

        $contract_types = $contractTypesServices->getAll();

        $GLOBALS['contract_types'] = $contract_types;

        // Check if form data is cached
		$formdata = Session::consume('__formdata__');
        
		if(!empty($formdata)) {
			$GLOBALS['form_data'] = json_decode($formdata, true);
		}
    }

    /**
     * Update contract model
     *
     * @return void
     */
    public function update()
    {
        if (!isset($_GET['id'])) {
			Flash::error("Mauvaise requête");
			header('Location: '.VIEWS . 'ContractModels');
			exit;
		}

        $contractModel = $this->service->get((int)$_GET['id']);

        if(!$contractModel) {
			Flash::error("Aucun modèle de contrat trouvé avec l'id ". $_GET['id']);
			header('Location: '.VIEWS . 'ContractModels');
			exit;
		}

        if (isset($_POST['update_contract_model'])) {
			$data = $_POST;
			$data['id'] = $_GET['id'];

            if(isset($data['is_current']) && $data['is_current'] == 1) $data['is_current'] = true;
            
			$updated = $this->service->update($data);

			if ($updated) {
				Flash::success("Le modèle de contrat a été mis à jour avec succès.");

                header("Location: " . VIEWS . "ContractModels/view?id=" . $data['id']);
                exit;
			}
		}

        $GLOBALS['contractModel'] = $contractModel;

        $contractTypesServices = new ContractTypesServices();
        $contract_types = $contractTypesServices->getAll();
        $GLOBALS['contract_types'] = $contract_types;

        $_SESSION['page_title'] = 'Modèles de contrat';
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
			header('Location: '.VIEWS . 'ContractModels');
			exit;
		}

        $contractModel = $this->service->get((int)$_GET['id']);

        if(!$contractModel) {
			Flash::error("Aucun modèle de contrat trouvé avec l'id ". $_GET['id']);
			header('Location: '.VIEWS . 'ContractModels');
			exit;
		}

        $_SESSION['page_title'] = 'Modèles de contrat';
		$_SESSION['subpage_title'] = 'Editier';

        $GLOBALS['contractModel'] = $contractModel;
    }

    public function delete()
    {
        if(!isset($_GET['id']) || empty($_GET['id'])) {
            if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
                http_response_code(403);
				header('Content-Type: application/json');
				echo json_encode(['status' => 'error', 'message' => "Mauvaise requête"]);
	
				exit;
			}

			header('Location: ' . VIEWS . 'ContractModels');
			exit;
		}

		// check if the contract model exists
		$check_contract_model = $this->service->get((int)$_GET['id']);
		if(!$check_contract_model) {
			if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
                http_response_code(404);
				header('Content-Type: application/json');
				echo json_encode(['status' => 'error', 'message' => "Aucun modèle de contrat trouvé avec l'id ". $_GET['id']]);
	
				exit;
			}

			Flash::error("Aucun modèle de contrat trouvé avec l'id ". $_GET['id']);

			header('Location: '.VIEWS . 'ContractModels');
			exit;
		}


		$deleted = $this->service->delete((int)$_GET['id']);

		if ($deleted) {
			Flash::success("Le modèle de contrat a été supprimé avec succès.");
		} else {
            if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
                http_response_code(500);
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => "Le modèle de contrat n'a pas été supprimé. Veuillez réessayer !"]);

                exit;
            }

			Flash::error("Le modèle de contrat n'a pas été supprimé. Veuillez réessayer !");
		}

		if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
			header('Content-Type: application/json');
			echo json_encode(['status' => 'success', 'message' => 'Modèle de contrat supprimé avec succès.']);

			exit;
		}

		header('Location: ' . VIEWS . 'ContractModels');
    }
}