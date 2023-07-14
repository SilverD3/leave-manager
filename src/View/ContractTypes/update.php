<?php

/**
 * Leave manager : Simple app for contract and leave management.
 *
 * @copyright Copyright (c) Silevester D. (https://github.com/SilverD3)
 * @link      https://github.com/SilverD3/leave-manager Leave Manager Project
 * @since     1.0 (2022)
 */

require_once dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'autoload.php';

use App\Controller\ContractTypesController;
use Core\FlashMessages\Flash;

(new ContractTypesController())->update();

require_once dirname(__DIR__) . DS . 'Elements' . DS . 'header.php';

?>

<main id="main" class="main">
	<div class="pagetitle">
		<h1>Editer le type de contrat</h1>
		<nav>
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Accueil</a></li>
				<li class="breadcrumb-item"><a href="<?= VIEWS . 'ContractTypes' ?>">Types de contrat</a></li>
				<li class="breadcrumb-item active">Editer</li>
			</ol>
		</nav>
	</div><!-- End Page Title -->

	<section class="section dashboard">
		<div class="row">
			<div class="col-md-8 col-xl-8 col-xxl-6">
				<div class="card">
					<div class="card-body">
						<h5 class="card-title">Completez les champs ci-après pour editer le type de contrat</h5>

						<?= Flash::render() ?>

						<form class="row g-3 needs-validation" action="" method="post" novalidate>
							<div class="mb-1">
								<label for="ctname" class="form-label">Nom du type</label>
								<input type="text" class="form-control" name="name" id="ctname" value="<?= isset($form_data['name']) ? $form_data['name'] : $contract_type->getName() ?>" required>
								<div class="invalid-feedback">Veuillez renseigner le nom du type de contrat.</div>
							</div>

							<div class="mb-3">
								<label for="ctdesc" class="form-label">Description</label>
								<textarea class="form-control" name="description" id="ctdesc" rows="3"><?= isset($form_data['description']) ? $form_data['description'] : $contract_type->getDescription() ?></textarea>
							</div>

							<div class="text-center">
								<input type="submit" class="btn btn-primary" name="update_contract_type" value="Enregistrer">
								<button type="button" onclick="javascript:history.back()" class="btn btn-secondary">Annuler</button>
							</div>

						</form>

					</div>
				</div>
			</div>

		</div>

	</section>

</main>

<?php require_once dirname(__DIR__) . DS . 'Elements' . DS . 'footer.php'; ?>