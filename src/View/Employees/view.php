<?php 
require_once dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'autoload.php';

use App\Controller\EmployeesController;
use App\View\Helpers\DateHelper;
use Core\FlashMessages\Flash;

(new EmployeesController())->view();

require_once dirname(__DIR__) . DS . 'Elements' . DS . 'header.php';

?>

<main id="main" class="main">
	<div class="pagetitle">
		<h1><?= $employee->getFirstName() . ' ' . $employee->getLastName() ?></h1>
		<nav>
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Accueil</a></li>
				<li class="breadcrumb-item"><a href="<?= VIEWS . 'Employees' ?>">Employés</a></li>
				<li class="breadcrumb-item active"><?= $employee->getFirstName() . ' ' . $employee->getLastName() ?></li>
			</ol>
		</nav>
	</div><!-- End Page Title -->

	<section class="section profile">
        <div class="row">

            <?= Flash::render() ?>

            <div class="col-xl-4">

                <div class="card">
                    <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">

                        <img src="<?= IMAGES ?>user_icon.png" alt="User icon" class="rounded-circle w-75">
                        <h2><?= $employee->getFirstName() . ' ' . $employee->getLastName() ?></h2>
                        <h6><?= $employee->getRole()->getName() ?> </h6>

                        <?php if($isInVaccations): ?>
                            <p class="text-center"><span class="badge bg-primary p-2"><i class="bi bi-person-dash me-2"></i> En congé</span></p>
                        <?php endif; ?>
                    </div>
                </div>

            </div>

            <div class="col-xl-8">

                <div class="card">
                    <div class="card-body pt-3">
                        <h5 class="card-title">Détails du profil</h5>

                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <tbody>
                                    <tr>
                                        <th>Nom complet</th>
                                        <td><?= $employee->getFirstName() . ' ' . $employee->getLastName() ?></td>
                                    </tr>
                                    <tr>
                                        <th>Rôle</th>
                                        <td><?= $employee->getRole()->getName() ?></td>
                                    </tr>
                                    <tr>
                                        <th>Adresse e-mail</th>
                                        <td><?= $employee->getEmail() ?></td>
                                    </tr>
                                    <tr>
                                        <th>Nom d'utilisateur</th>
                                        <td><?= $employee->getUsername() ?></td>
                                    </tr>
                                    <tr>
                                        <th>Date d'ajout</th>
                                        <td><?= DateHelper::dateTime($employee->getCreated()) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Dernière modification</th>
                                        <td><?= DateHelper::dateTime($employee->getModified()) ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>

<?php require_once dirname(__DIR__) . DS . 'Elements' . DS . 'footer.php'; ?>