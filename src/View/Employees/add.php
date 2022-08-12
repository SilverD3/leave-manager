<?php 
require_once dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'autoload.php';

use App\Controller\EmployeesController;
use App\View\Helpers\DateHelper;
use App\View\Helpers\UtilsHelper;

(new EmployeesController())->index();

require_once dirname(__DIR__) . DS . 'Elements' . DS . 'header.php';

?>

<main id="main" class="main">
	<div class="pagetitle">
		<h1>Ajouter un employé</h1>
		<nav>
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="/">Accueil</a></li>
				<li class="breadcrumb-item"><a href="<?= VIEWS . 'Employees' ?>">Employés</a></li>
				<li class="breadcrumb-item active">Nouveau</li>
			</ol>
		</nav>
	</div><!-- End Page Title -->

	<section class="section dashboard">
		<div class="row">
			<div class="col-12">
				
			</div>

		</div>

	</section>

</main>

<?php require_once dirname(__DIR__) . DS . 'Elements' . DS . 'footer.php'; ?>