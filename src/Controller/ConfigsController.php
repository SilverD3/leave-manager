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
            $auth_user = (new Auth())->getAuthUser();
            $data = $_POST;
            $data['modified_by'] = $auth_user->getId();

            unset($data['update_config']);
            
            $updated = $this->service->update($data);

            if ($updated) {
                Flash::success("Le paramètre été mis à jour avec succès.");
                header("Location: " . VIEWS . 'Configs');
                exit;
            }
        }

        if (isset($_POST['reset_configs'])) {
            $auth_user = (new Auth())->getAuthUser();
            $reset = $this->service->resetAll($auth_user->getId());
            if ($reset) {
                Flash::success("Tous les paramètres ont été réinitialisé avec succès.");
                header("Location: " . VIEWS . 'Configs');
                exit;
            }
        }

		$_SESSION['page_title'] = 'Paramètres';
        unset($_SESSION['subpage_title']);

        $configs = $this->service->getAll();

        $GLOBALS['configs'] = $configs;
	}
}