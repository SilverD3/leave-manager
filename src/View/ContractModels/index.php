<?php 
require_once dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'autoload.php';

use App\Controller\ContractModelsController;
use App\View\Helpers\DateHelper;
use App\View\Helpers\UtilsHelper;
use Core\FlashMessages\Flash;

(new ContractModelsController())->index();

require_once dirname(__DIR__) . DS . 'Elements' . DS . 'header.php';

?>

<main id="main" class="main">
	<div class="pagetitle">
		<h1>Les modèles de contract</h1>
		<nav>
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Accueil</a></li>
				<li class="breadcrumb-item active">Modèles de contrat</li>
			</ol>
		</nav>
	</div><!-- End Page Title -->

	<section class="section dashboard">
		<div class="row mt-2 mb-1">
			<div class="col-12">
				<a href="<?= VIEWS . 'ContractModels/add.php' ?>" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i> Nouveau modèle</a>
			</div>

            <div class="col-12 mt-2" id="flash-feedback">
                <?= Flash::render() ?>
            </div>
		</div>

		<hr>

		<div class="row mt-1">
			<!-- Contract Models -->
            <div class="col-12">
                <div class="card recent-sales overflow-auto">

                    <div class="card-body">
                        <h5 class="card-title">Tous les modèles de contract</h5>
                        
                        <div class="alert alert-info alert-dismissible fade show d-flex align-items-center" role="alert">
                            <span class="bi bi-info-circle flex-shrink-0 me-2" role="img" aria-label="Info:"></span>
                            <div>
                                <strong>N.B</strong> : Les modèles en surbrillance sont les modèles par défaut
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        </div>

                        <?php if (!empty($contract_models)): ?>
                            <table class="table table-border datatable">
                                <thead>
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">Nom</th>
                                        <th scope="col">Type de contrat</th>
                                        <th scope="col">Date d'ajout</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($contract_models as $contractModel): ?>
                                        <tr <?= $contractModel->getIsCurrent() ? 'class="bg-success-light"' : '' ?>>
                                            <th scope="row"><?= $contractModel->getId() ?></th>
                                            <td><?= $contractModel->getName() ?></td>
                                            <td><?= $contractModel->getContractType()->getName() ?></td>
                                            <td><?= DateHelper::shortDate($contractModel->getCreated()) ?></td>
                                            <td>
                                                <a href="<?= VIEWS . 'ContractModels/view.php?id=' . $contractModel->getId() ?>" class="btn btn-small btn-primary">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="<?= VIEWS . 'ContractModels/update.php?id=' . $contractModel->getId() ?>" class="btn btn-small btn-primary">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <button class="btn btn-danger" onclick="deleteContractModel(<?= $contractModel->getId() ?>)">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="alert alert-primary d-flex align-items-center" role="alert">
                                <span class="bi bi-info-circle flex-shrink-0 me-2" role="img" aria-label="Info:"></span>
                                <div>
                                    Aucun modèle de contrat trouvé
                                </div>
                            </div>

                        <?php endif ?>
                    </div>

                </div>
            </div><!-- End Contract Models -->

		</div>

	</section>

</main>

<?php require_once dirname(__DIR__) . DS . 'Elements' . DS . 'footer.php'; ?>

<script type="text/javascript">
	
	function deleteContractModel(id)
	{
		if (confirm("Voulez-vous vraiment supprimer ce modèle ?")) {
			var xmlhttp = new XMLHttpRequest();
			var url = "<?= VIEWS . 'ContractModels/delete.php?ajax=1&id=' ?>" + id;

			xmlhttp.onreadystatechange = function() {
				if (xmlhttp.readyState == 4){
                    if(xmlhttp.status == 200) {
					    location.reload();
                    }else {
                        alert("Erreur: " + (JSON.parse(xmlhttp.response)).message);
                    }
				} 
			};

			xmlhttp.open("DELETE", url, true);
			xmlhttp.send();
		}
	}
	
</script>