<?php
declare(strict_types=1);

namespace App\Controller;

require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'autoload.php';

use App\Service\PagesServices;

class PagesController
{
    function index(){
        $_SESSION['subpage_title'] = 'index';
    }
}

// Page title

$_SESSION['page_title'] = 'Pages';

$dbconnection = (new PagesServices())->testDbConnection();