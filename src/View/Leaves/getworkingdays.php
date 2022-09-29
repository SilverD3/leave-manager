<?php 
require_once dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'autoload.php';

use App\Controller\LeavesController;

(new LeavesController())->getNbWorkingDays();

?>