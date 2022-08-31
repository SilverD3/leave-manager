<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\ConfigsServices;
use Core\Auth\Auth;
use Core\FlashMessages\Flash;
use Core\Utils\Session;

require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'autoload.php';

class ConfigsController
{
    /**
     * @var ConfigsServices $services Configs services
     */
    private $service;

    function __construct()
	{
		$this->service = new ConfigsServices();
	}

    /**
     * Index method
     * @return void
     */
    public function index()
	{
        AuthController::require_admin_priv();

        if (isset($_POST['update_config'])) {
            $data = $_POST;
            $auth_user = (new Auth())->getAuthUser();
            $data['modified_by'] = $auth_user->getId();

            unset($data['update_config']);
            
            $updated = $this->service->update($data);

            if ($updated) {
                Flash::success("Le paramètre été mis à jour avec succès.");
            }
        }

		$_SESSION['page_title'] = 'Paramètres';
        unset($_SESSION['subpage_title']);

        $configs = $this->service->getAll();

        $GLOBALS['configs'] = $configs;
	}
}