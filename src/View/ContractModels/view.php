<?php 
require_once dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'autoload.php';

use App\Controller\ContractModelsController;
use App\View\Helpers\DateHelper;
use Core\FlashMessages\Flash;

(new ContractModelsController())->view();

require_once dirname(__DIR__) . DS . 'Elements' . DS . 'header.php';

?>

<main id="main" class="main">
	<div class="pagetitle">
		<h1>Détails du modèle de contrat</h1>
		<nav>
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Accueil</a></li>
				<li class="breadcrumb-item"><a href="<?= VIEWS . 'ContractModels' ?>">Modèles de contrat</a></li>
				<li class="breadcrumb-item active">Détails</li>
			</ol>
		</nav>
	</div><!-- End Page Title -->

	<section class="section dashboard">
		<div class="row">
			<div class="col-md-12 col-xl-12 col-xxl-10">
				<div class="card">
					<div class="card-body">

                        <?= Flash::render() ?>

			  			<h5 class="card-title"><i class="bi bi-info-circle"></i> Informations détaillées</h5>
                        
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <tbody>
                                    <tr>
                                        <th>Nom du modèle</th>
                                        <td><?= $contractModel->getName() ?></td>
                                    </tr>
                                    <tr>
                                        <th>Nom du type de cpntrat</th>
                                        <td><?= $contractModel->getContractType()->getName() ?></td>
                                    </tr>
                                    <tr>
                                        <th>Modèle par defaut</th>
                                        <td>
                                            <?php if ($contractModel->getIsCurrent()): ?>
                                                <span class="text-success"><i class="bi bi-check-lg"></i></span>
                                            <?php else: ?>
                                                <span class="text-danger"><i class="bi bi-x-lg"></i></span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Date d'ajout</th>
                                        <td><?= DateHelper::dateTime($contractModel->getCreated()) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Date dernière modification</th>
                                        <td><?= DateHelper::dateTime($contractModel->getModified()) ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                          
			  			<h5 class="card-title my-3"><i class="bi bi-file-earmark-text"></i> Contenu du modèle</h5>

                        <div class="editior-wrapper border p-5">
                            <?= $contractModel->getContent() ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require_once dirname(__DIR__) . DS . 'Elements' . DS . 'footer.php'; ?>

<script src="<?= TEMPLATE_PATH ?>assets/vendor/tinymce/tinymce.min.js"></script>
<script type="text/javascript">

// tinymce.init({
//     selector: '#cmcontent',
//     readonly : 1
// });

</script>