<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\CompanyServices;
use App\Service\ContractTypesServices;
use Core\FlashMessages\Flash;
use Core\Utils\Session;

require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'autoload.php';

class CompanyController
{
    /**
     * @var CompanyServices $services Contract models services
     */
    private $service;

    function __construct()
	{
		$this->service = new CompanyServices();
	}

    /**
     * Index method
     * @return void
     */
    public function index()
	{
        if (isset($_POST['update_company'])) {
            $updated = $this->service->update($_POST);

            if ($updated) {
                Flash::success("Les informations de l'entreprise ont été mis à jour avec succès.");
            }
        }


		$_SESSION['page_title'] = 'Entreprise';
        unset($_SESSION['subpage_title']);

        $company = $this->service->getCompany();

        $GLOBALS['company'] = $company;
	}
}