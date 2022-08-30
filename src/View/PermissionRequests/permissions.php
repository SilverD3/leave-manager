<?php 
require_once dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'autoload.php';

use App\Controller\PermissionRequestsController;
use App\View\Helpers\DateHelper;
use App\View\Helpers\UtilsHelper;
use Core\FlashMessages\Flash;

(new PermissionRequestsController())->permissions();

require_once dirname(__DIR__) . DS . 'Elements' . DS . 'header.php';

?>

<main id="main" class="main">
	<div class="pagetitle">
		<h1>Les permissions accordées</h1>
		<nav>
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Accueil</a></li>
				<li class="breadcrumb-item"><a href="<?= VIEWS . 'PermissionRequests' ?>">Demandes de permission</a></li>
				<li class="breadcrumb-item active">Accordées</li>
			</ol>
		</nav>
	</div><!-- End Page Title -->

	<section class="section dashboard">
		<div class="row mt-1">
			<div class="col-12">

                <div class="card recent-sales overflow-auto">

                    <div class="card-body">
                        <h5 class="card-title">Toutes les permissions accordées <?= $current_year == 'all' ? 'toute année confondue' : 'en ' . $current_year ?></h5>

                        <button class="btn btn-secondary ms-sm-2 mb-sm-2 float-sm-end" type="button" data-bs-toggle="modal" data-bs-target="#selectYearModal">
                            <i class="bi bi-calendar"></i> Choisir une autre année
                        </button>

                        <div class="modal fade" id="selectYearModal" tabindex="-1" aria-labelledby="selectYearModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="selectYearModalLabel"><i class="bi bi-calendar"></i> Choisir une autre année</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="" method="get" class="g-3 needs-validation" novalidate>
                                        <div class="modal-body">
                                            <div class="">
                                                <label for="pryear" class="form-label">Année</label>
                                                <select id="pryear" class="form-select" name="year" required>
                                                    <option value="">Choisir une année</option>
                                                    <option value="all">Toutes</option>
                                                    <?php if(empty($years)):?>
                                                        <div class="alert alert-danger">
                                                            Aucune année disponible.
                                                        </div>
                                                    <?php else:?>
                                                        <?php foreach($years as $year): ?>
                                                            <option value="<?= $year['year'] ?>">
                                                                <?= $year['year'] ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    <?php endif;?>
                                                </select>
                                                <div class="invalid-feedback">Veuillez choisir une année.</div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                            <input type="submit" class="btn btn-primary" value="Afficher">
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                       
                        <?= Flash::render() ?>

                        <?php if (!empty($permissions)): ?>
                            <table class="table table-border datatable">
                                <thead>
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">Employee</th>
                                        <th scope="col">Motif</th>
                                        <th scope="col">Début</th>
                                        <th scope="col">Fin</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($permissions as $permission): ?>
                                        <tr>
                                            <th scope="row"><a href="<?= VIEWS . 'PermissionRequests' . DS . 'view.php?id=' . $permission->getId() ?>"><?= $permission->getId() ?></a></th>
                                            <td>
                                                <a class="link" href="<?= VIEWS . 'Employees/view.php?id=' . $permission->getEmployee()->getId() ?>">
                                                    <?= $permission->getEmployee()->getFirstName() . ' ' . $permission->getEmployee()->getLastName() ?>
                                                </a>
                                            </td>
                                            <td><?= UtilsHelper::troncate($permission->getReason(), 45) ?></td>
                                            <td><?= DateHelper::shortDate($permission->getStartDate()) ?></td>
                                            <td><?= DateHelper::shortDate($permission->getEndDate()) ?></td>
                                            <td>
                                                <a href="<?= VIEWS . 'PermissionRequests/view.php?id=' . $permission->getId() ?>" class="btn btn-info btn-sm" title="Voir la demande">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="alert alert-primary d-flex align-items-center" role="alert">
                                <span class="bi bi-info-circle flex-shrink-0 me-2" role="img" aria-label="Info:"></span>
                                <div>
                                    Aucune permission accordée trouvée
                                </div>
                            </div>

                        <?php endif ?>
                    </div>
                </div>
            </div>

		</div>

	</section>

</main>

<?php require_once dirname(__DIR__) . DS . 'Elements' . DS . 'footer.php'; ?>