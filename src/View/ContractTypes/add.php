<?php 
require_once dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'autoload.php';

use App\Controller\ContractTypesController;
use Core\FlashMessages\Flash;

(new ContractTypesController())->add();

require_once dirname(__DIR__) . DS . 'Elements' . DS . 'header.php';

?>

<main id="main" class="main">
	<div class="pagetitle">
		<h1>Ajouter un type de contrat</h1>
		<nav>
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Accueil</a></li>
				<li class="breadcrumb-item"><a href="<?= VIEWS . 'ContractTypes' ?>">Types de contrat</a></li>
				<li class="breadcrumb-item active">Nouveau</li>
			</ol>
		</nav>
	</div><!-- End Page Title -->

	<section class="section dashboard">
		<div class="row">
			<div class="col-md-8 col-xl-8 col-xxl-6">
				<div class="card">
					<div class="card-body">
			  			<h5 class="card-title">Remplissez les champs ci-après pour ajouter un type de contrat</h5>
                        <?= Flash::render() ?>
			  			<form class="row g-3 needs-validation" action="" method="post" novalidate>
                            <div class="mb-1">
                                <label for="ctname" class="form-label">Nom du type</label>
                                <input type="text" class="form-control" name="name" id="ctname" value="<?= isset($form_data['name']) ? $form_data['name'] : '' ?>" required>
                                <div class="invalid-feedback">Veuillez renseigner le nom du type de contrat.</div>
                            </div>

                            <div class="mb-3">
                                <label for="ctdesc" class="form-label">Description</label>
                                <textarea class="form-control" name="description" id="ctdesc" rows="3"><?= isset($form_data['description']) ? $form_data['description'] : ''; ?></textarea>
                            </div>

                            <div class="text-center">
                                <input type="submit" class="btn btn-primary" name="add_contract_type" value="Ajouter">
                                <button type="reset" class="btn btn-secondary">Réinitiliser</button>
                            </div>

						</form>

					</div>
		  		</div>
			</div>

		</div>

	</section>

</main>

<?php require_once dirname(__DIR__) . DS . 'Elements' . DS . 'footer.php'; ?>