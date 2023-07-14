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

use App\Service\CompanyServices;
use Core\Auth\Auth;
use Core\FlashMessages\Flash;

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
        $auth_user = (new Auth())->getAuthUser();
        if (empty($auth_user) || $auth_user->getRole()->getCode() != 'ADM') {
            header('Location: ' . VIEWS . 'Company/view.php');
            exit;
        }

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

    /**
     * Index method
     * @return void
     */
    public function view()
    {
        $_SESSION['page_title'] = 'Entreprise';
        unset($_SESSION['subpage_title']);

        $company = $this->service->getCompany();

        $GLOBALS['company'] = $company;
    }
}
