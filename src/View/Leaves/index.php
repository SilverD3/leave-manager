<?php 
require_once dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'autoload.php';

use App\Controller\LeavesController;
use App\View\Helpers\DateHelper;
use Core\FlashMessages\Flash;

(new LeavesController())->index();

require_once dirname(__DIR__) . DS . 'Elements' . DS . 'header.php';

?>

<main id="main" class="main">
	<div class="pagetitle">
		<h1>Les congés planifiés</h1>
		<nav>
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Accueil</a></li>
				<li class="breadcrumb-item active">Congés</li>
			</ol>
		</nav>
	</div><!-- End Page Title -->

	<section class="section dashboard">
		<div class="row mt-2 mb-1">
			<div class="col-12">
				<a href="<?= VIEWS . 'Leaves/add.php' ?>" class="btn btn-primary me-3 mt-2"><i class="bi bi-plus-circle me-1"></i> Planifier </a>
				<a href="<?= VIEWS . 'Leaves/calendar.php' ?>" class="btn btn-primary mt-2"><i class="bi bi-calendar me-1"></i> Calendrier </a>
			</div>
		</div>

		<hr>

		<div class="row mt-1">
			<div class="col-12">

                <div class="card recent-sales overflow-auto">

                    <div class="card-body">
                        <h5 class="card-title">Planification des congés pour <?= $current_year == 'all' ? 'toutes les années' : "l'année " . $current_year ?></h5>
                       
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
                                                
                                                <?php if(empty($years)):?>
                                                    <div class="alert alert-danger">
                                                        Aucune année disponible.
                                                    </div>
                                                <?php else:?>
                                                    <select id="pryear" class="form-select" name="year" required>
                                                    <option value="">Choisir une année</option>
                                                    <option value="all">Toutes</option>
                                                        <?php foreach($years as $year): ?>
                                                            <option value="<?= $year['year'] ?>">
                                                                <?= $year['year'] ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                <?php endif;?>
                                                
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

                        <?php if (!empty($leaves)): ?>
                            <table class="table table-border datatable">
                                <thead>
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">Employee</th>
                                        <th scope="col">Début</th>
                                        <th scope="col">Fin</th>
                                        <th scope="col">Jours <span class="badge bg-info rounded p-0" title="Nombre de jours ouvrables"><i class="bi bi-info"></i></span></th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($leaves as $leave): ?>
                                        <tr>
                                            <th scope="row"><a href="<?= VIEWS . 'Leaves' . DS . 'view.php?id=' . $leave->getId() ?>"><?= $leave->getId() ?></a></th>
                                            <td>
                                                <a class="link" href="<?= VIEWS . 'Employees/view.php?id=' . $leave->getEmployee()->getId() ?>">
                                                    <?= $leave->getEmployee()->getFirstName() . ' ' . $leave->getEmployee()->getLastName() ?>
                                                </a>
                                            </td>
                                            <td><?= DateHelper::shortDate($leave->getStartDate()) ?></td>
                                            <td><?= DateHelper::shortDate($leave->getEndDate()) ?></td>
                                            <td><?= $leave->getDays() ?></td>
                                            <td>
                                                <?php if (DateHelper::periodStatus($leave->getStartDate(), $leave->getEndDate()) == 'past') : $leaveStatus = 'past'; ?>
                                                    <span class="badge text-bg-info">Passé</span>
                                                <?php elseif(DateHelper::periodStatus($leave->getStartDate(), $leave->getEndDate()) == 'present'): $leaveStatus = 'present'; ?>
                                                    <span class="badge text-bg-success">En cours</span>
                                                <?php elseif(DateHelper::periodStatus($leave->getStartDate(), $leave->getEndDate()) == 'future'): $leaveStatus = 'future'; ?>
                                                    <span class="badge text-bg-primary">Prochainement</span>
                                                <?php else: $leaveStatus = 'unknown'; ?>
                                                    <span class="badge text-bg-secondary"> Inconnu </span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="<?= VIEWS . 'Leaves/view.php?id=' . $leave->getId() ?>" class="btn btn-info btn-sm" title="Plus de détails sur le congé">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <?php if($auth_user->getRole()->getCode() == 'ADM'): ?>
                                                    <?php if ($leaveStatus == 'future'): ?>
                                                        <button type="button" class="btn btn-danger btn-sm" onclick="deleteLeave(<?= $leave->getId() ?>)" title="Supprimer le congé">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                    <?php if ($leaveStatus == 'future' || $leaveStatus == 'present'): ?>
                                                        <a href="<?= VIEWS . 'Leaves/update.php?id=' . $leave->getId() ?>" class="btn btn-primary btn-sm" title="Modifier le congé">
                                                            <i class="bi bi-pencil-square"></i>
                                                        </a>
                                                    <?php endif; ?>
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
                                    Aucun congé planifié trouvé pour le moment
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

function deleteLeave(request_id)
{
    if (confirm("Voulez-vous vraiment supprimer ce congés ?")) {
        var xmlhttp = new XMLHttpRequest();
        var url = "<?= VIEWS . 'Leaves/delete.php?ajax=1&id=' ?>" + request_id;

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