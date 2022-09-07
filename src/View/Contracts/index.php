<?php 
require_once dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'autoload.php';

use App\Controller\ContractsController;
use App\View\Helpers\DateHelper;
use Core\FlashMessages\Flash;

(new ContractsController())->index();

require_once dirname(__DIR__) . DS . 'Elements' . DS . 'header.php';

?>

<main id="main" class="main">
	<div class="pagetitle">
		<h1>Les contrats</h1>
		<nav>
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Accueil</a></li>
				<li class="breadcrumb-item active">Contrats</li>
			</ol>
		</nav>
	</div><!-- End Page Title -->

	<section class="section dashboard">
        <?php if ($auth_user->getRole()->getCode() == 'ADM'): ?>
            <div class="row mt-2 mb-1">
                <div class="col-12">
                    <a href="<?= VIEWS . 'Contracts/add.php' ?>" class="btn btn-primary me-3 mt-2"><i class="bi bi-plus-circle me-1"></i> Ajouter un contrat </a>
                    <a href="<?= VIEWS . 'Contracts/expired.php' ?>" class="btn btn-secondary mt-2"><i class="bi bi-clock-history me-1"></i> Contrats expirés <span class="badge bg-primary"><?= $nb_expired ?></span> </a>
                </div>
            </div>

            <?php if ($nb_expired > 0): ?>
                <div class="alert alert-primary d-flex align-items-center" role="alert">
                    <span class="bi bi-info-circle flex-shrink-0 me-2" role="img" aria-label="Info:"></span>
                    <div class="fw-bold">
                        <?php if ($nb_expired > 1): echo $nb_expired . " sont arrivés à expiration"; else: echo $nb_expired . "est arrivé à expiration"; endif;?>
                    </div>
                </div>
            <?php endif; ?>

            <hr>
        <?php endif; ?>

		<div class="row mt-1">
			<div class="col-12">

                <div class="card recent-sales overflow-auto">

                    <div class="card-body">
                        <h5 class="card-title">Toutes les contrats </h5>
                       
                        <?php if ($auth_user->getRole()->getCode() == 'ADM'): ?>
                            <button class="btn btn-secondary ms-sm-2 mb-sm-2 float-sm-end" type="button" data-bs-toggle="modal" data-bs-target="#selectYearModal">
                                <i class="bi bi-lightning"></i> Choisir un statut
                            </button>

                            <div class="modal fade" id="selectYearModal" tabindex="-1" aria-labelledby="selectYearModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="selectYearModalLabel"><i class="bi bi-lightning"></i> Choisir le statut des contrat à afficher</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="" method="get" class="g-3 needs-validation" novalidate>
                                            <div class="modal-body">
                                                <div class="">
                                                    <label for="cstatus" class="form-label">Statut</label>
                                                    <select id="cstatus" class="form-select" name="status" required>
                                                        <option value="">Choisir un statut</option>
                                                        <option value="all">Tous</option>
                                                        <option value="pending">En attente</option>
                                                        <option value="active">En cours</option>
                                                        <option value="terminated">Resilié</option>
                                                    </select>
                                                    <div class="invalid-feedback">Veuillez choisir un statut.</div>
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
                        <?php endif; ?>

                        <?= Flash::render() ?>

                        <?php if (!empty($contracts)): ?>
                            <table class="table table-border datatable">
                                <thead>
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">Employee</th>
                                        <th scope="col">Type de contrat</th>
                                        <th scope="col">Début</th>
                                        <th scope="col">Fin</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($contracts as $contract): ?>
                                        <tr>
                                            <th scope="row"><a href="<?= VIEWS . 'Contracts' . DS . 'view.php?id=' . $contract->getId() ?>"><?= $contract->getId() ?></a></th>
                                            <td>
                                                <a class="link" href="<?= VIEWS . 'Employees/view.php?id=' . $contract->getEmployee()->getId() ?>">
                                                    <?= $contract->getEmployee()->getFirstName() . ' ' . $contract->getEmployee()->getLastName() ?>
                                                </a>
                                            </td>
                                            <td><?= $contract->getContractType()->getName() ?></td>
                                            <td><?= DateHelper::shortDate($contract->getStartDate()) ?></td>
                                            <td><?= DateHelper::shortDate($contract->getEndDate()) ?></td>
                                            <td>
                                                <?php if ($contract->getStatus() == 'pending'): ?>
                                                    <span class="badge text-bg-info">En attente</span>
                                                <?php elseif($contract->getStatus() == 'active'): ?>
                                                    <?php if (!empty($contract->getEndDate()) && strtotime($contract->getEndDate()) < time()) : ?>
                                                        <span class="badge text-bg-danger">Expiré</span>
                                                    <?php else: ?>
                                                        <span class="badge text-bg-primary">En cours</span>
                                                    <?php endif; ?>
                                                <?php elseif($contract->getStatus() == 'terminated'): ?>
                                                    <span class="badge text-bg-secondary">Résilié</span>
                                                <?php else: ?>
                                                    <span class="badge text-bg-info"> <?= $contract->getStatus() ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="<?= VIEWS . 'Contracts/view.php?id=' . $contract->getId() ?>" class="btn btn-info btn-sm" title="Voir le contrat">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <?php if($auth_user->getRole()->getCode() == 'ADM'): ?>
                                                    <?php if ($contract->getStatus() != 'terminated'): ?>
                                                        <a href="<?= VIEWS . 'Contracts/update.php?id=' . $contract->getId() ?>" class="btn btn-primary btn-sm" title="Editer le contrat">
                                                            <i class="bi bi-pencil-square"></i>
                                                        </a>
                                                    <?php endif; ?>

                                                    <?php if ($contract->getStatus() == 'pending'): ?>
                                                        <button type="button" class="btn btn-danger btn-sm" onclick="deleteContract(<?= $contract->getId() ?>)" title="Supprimer le contrat">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="alert alert-primary d-flex align-items-center mt-2" role="alert">
                                <span class="bi bi-info-circle flex-shrink-0 me-2" role="img" aria-label="Info:"></span>
                                <div>
                                    Aucun contrat trouvé pour le moment
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

function deleteContract(contract_id)
{
    if (confirm("Voulez-vous vraiment supprimer ce contrat ?")) {
        var xmlhttp = new XMLHttpRequest();
        var url = "<?= VIEWS . 'Contracts/delete.php?ajax=1&id=' ?>" + contract_id;

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