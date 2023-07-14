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

require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'autoload.php';

use App\Service\PagesServices;

class PagesController
{
    function index()
    {
        $_SESSION['subpage_title'] = 'index';
    }
}

// Page title

$_SESSION['page_title'] = 'Pages';

$dbconnection = (new PagesServices())->testDbConnection();
