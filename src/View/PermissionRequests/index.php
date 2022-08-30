<?php 
require_once dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'autoload.php';

use App\Controller\PermissionRequestsController;
use App\View\Helpers\DateHelper;
use App\View\Helpers\UtilsHelper;
use Core\FlashMessages\Flash;

(new PermissionRequestsController())->index();

require_once dirname(__DIR__) . DS . 'Elements' . DS . 'header.php';

?>

<main id="main" class="main">
	<div class="pagetitle">
		<h1>Les demandes de permission</h1>
		<nav>
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Accueil</a></li>
				<li class="breadcrumb-item active">Demandes de permission</li>
			</ol>
		</nav>
	</div><!-- End Page Title -->

	<section class="section dashboard">
		<div class="row mt-2 mb-1">
			<div class="col-12">
				<a href="<?= VIEWS . 'PermissionRequests/add.php' ?>" class="btn btn-primary me-3 mt-2"><i class="bi bi-plus-circle me-1"></i> Demander une permission </a>
				<a href="<?= VIEWS . 'PermissionRequests/permissions.php' ?>" class="btn btn-primary mt-2"><i class="bi bi-person-dash me-1"></i> Toutes les permissions </a>
			</div>
		</div>

		<hr>

		<div class="row mt-1">
			<div class="col-12">

                <div class="card recent-sales overflow-auto">

                    <div class="card-body">
                        <h5 class="card-title">Toutes les demandes de permission de l'année <?= $current_year == 'all' ? 'toute année confondue' : 'en ' . $current_year ?></h5>
                       
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

                        <?php if (!empty($permission_requests)): ?>
                            <table class="table table-border datatable">
                                <thead>
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">Employee</th>
                                        <th scope="col">Motif</th>
                                        <th scope="col">Début</th>
                                        <th scope="col">Fin</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($permission_requests as $permissionRequest): ?>
                                        <tr>
                                            <th scope="row"><a href="<?= VIEWS . 'PermissionRequests' . DS . 'view.php?id=' . $permissionRequest->getId() ?>"><?= $permissionRequest->getId() ?></a></th>
                                            <td>
                                                <a class="link" href="<?= VIEWS . 'Employees/view.php?id=' . $permissionRequest->getEmployee()->getId() ?>">
                                                    <?= $permissionRequest->getEmployee()->getFirstName() . ' ' . $permissionRequest->getEmployee()->getLastName() ?>
                                                </a>
                                            </td>
                                            <td><?= UtilsHelper::troncate($permissionRequest->getReason(), 45) ?></td>
                                            <td><?= DateHelper::shortDate($permissionRequest->getStartDate()) ?></td>
                                            <td><?= DateHelper::shortDate($permissionRequest->getEndDate()) ?></td>
                                            <td>
                                                <?php if ($permissionRequest->getStatus() == 'pending'): ?>
                                                    <span class="badge text-bg-primary">En attente</span>
                                                <?php elseif($permissionRequest->getStatus() == 'approved'): ?>
                                                    <span class="badge text-bg-success">Approuvée</span>
                                                <?php elseif($permissionRequest->getStatus() == 'disapproved'): ?>
                                                    <span class="badge text-bg-danger">Rejetée</span>
                                                <?php else: ?>
                                                    <span class="badge text-bg-info"> <?= $permissionRequest->getStatus() ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="<?= VIEWS . 'PermissionRequests/view.php?id=' . $permissionRequest->getId() ?>" class="btn btn-info btn-sm" title="Voir la demande">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <?php if ($permissionRequest->getStatus() == 'pending'): ?>
                                                    <?php if($auth_user->getRole()->getCode() == 'ADM'): ?>
                                                        <button type="button" class="btn btn-success btn-sm" onclick="approveRequest(<?= $permissionRequest->getId() ?>)" title="Approuver la demande">
                                                            <i class="bi bi-check"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-danger btn-sm" onclick="disapproveRequest(<?= $permissionRequest->getId() ?>)" title="Désapprouver la demande">
                                                            <i class="bi bi-x"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                    <?php if($auth_user->getId() == $permissionRequest->getEmployeeId()): ?>
                                                        <a href="<?= VIEWS . 'PermissionRequests/update.php?id=' . $permissionRequest->getId() ?>" class="btn btn-primary btn-sm" title="Editer la demande">
                                                            <i class="bi bi-pencil-square"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                    <button type="button" class="btn btn-danger btn-sm" onclick="deleteRequest(<?= $permissionRequest->getId() ?>)" title="Supprimer la demande">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="alert alert-primary d-flex align-items-center" role="alert">
                                <span class="bi bi-info-circle flex-shrink-0 me-2" role="img" aria-label="Info:"></span>
                                <div>
                                    Aucune demande de permission trouvée
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

<script type="text/javascript">

function deleteRequest(request_id)
{
    if (confirm("Voulez-vous vraiment supprimer cette demande ?")) {
        var xmlhttp = new XMLHttpRequest();
			var url = "<?= VIEWS . 'PermissionRequests/delete.php?ajax=1&id=' ?>" + request_id;

			xmlhttp.onreadystatechange = function() {
				if (xmlhttp.readyState == 4){
                    if(xmlhttp.status == 200) {
					    location.reload();
                    }else {
                        alert("Erreur: " + (JSON.parse(xmlhttp.response)).message);
                    }
				} 
			};
			
			xmlhttp.open("POST", url, true);
			xmlhttp.send();
    }
}

function approveRequest(request_id)
{
    if (confirm("Voulez-vous vraiment approuver cette demande ?")) {
        var xmlhttp = new XMLHttpRequest();
			var url = "<?= VIEWS . 'PermissionRequests/approve.php?ajax=1&id=' ?>" + request_id;

			xmlhttp.onreadystatechange = function() {
				if (xmlhttp.readyState == 4){
                    if(xmlhttp.status == 200) {
					    location.reload();
                    }else {
                        alert("Erreur: " + (JSON.parse(xmlhttp.response)).message);
                    }
				} 
			};
			
			xmlhttp.open("POST", url, true);
			xmlhttp.send();
    }
}

function disapproveRequest(request_id)
{
    if (confirm("Voulez-vous vraiment rejeter cette demande ?")) {
        var xmlhttp = new XMLHttpRequest();
			var url = "<?= VIEWS . 'PermissionRequests/disapprove.php?ajax=1&id=' ?>" + request_id;

			xmlhttp.onreadystatechange = function() {
				if (xmlhttp.readyState == 4){
                    if(xmlhttp.status == 200) {
					    location.reload();
                    }else {
                        alert("Erreur: " + (JSON.parse(xmlhttp.response)).message);
                    }
				} 
			};
			
			xmlhttp.open("POST", url, true);
			xmlhttp.send();
    }
}

</script>