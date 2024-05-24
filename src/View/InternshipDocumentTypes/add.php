<?php

/**
 * Leave manager : Simple app for contract and leave management.
 *
 * @copyright Copyright (c) Silevester D. (https://github.com/SilverD3)
 * @link      https://github.com/SilverD3/leave-manager Leave Manager Project
 * @since     2.0 (2024)
 */

require_once dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'autoload.php';

use App\Controller\InternshipDocumentTypesController;
use Core\FlashMessages\Flash;

(new InternshipDocumentTypesController())->add();

require_once dirname(__DIR__) . DS . 'Elements' . DS . 'header.php';

?>

<main id="main" class="main">
	<div class="pagetitle">
		<h1>Ajouter un type de document de stage</h1>
		<nav>
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Accueil</a></li>
				<li class="breadcrumb-item"><a href="<?= VIEWS . 'InternshipDocumentTypes' ?>">Types de document de stage</a></li>
				<li class="breadcrumb-item active">Nouveau</li>
			</ol>
		</nav>
	</div><!-- End Page Title -->

	<section class="section dashboard">
		<div class="row">
			<div class="col-md-8 col-xl-8 col-xxl-6">
				<div class="card">
					<div class="card-body">
						<h5 class="card-title">Remplissez les champs ci-après pour ajouter un type de document de stage</h5>
						<?= Flash::render() ?>
						<form class="row g-3 needs-validation" action="" method="post" novalidate>
							<div class="mb-1">
								<label for="idtcode" class="form-label">Nom du type</label>
								<input type="text" class="form-control" name="code" id="idtcode" value="<?= isset($form_data['code']) ? $form_data['code'] : '' ?>" required>
								<div class="invalid-feedback">Veuillez renseigner le nom du type de document de stage.</div>
							</div>

							<div class="mb-3">
								<label for="idtdesc" class="form-label">Description</label>
								<textarea class="form-control" name="description" id="idtdesc" rows="3"><?= isset($form_data['description']) ? $form_data['description'] : ''; ?></textarea>
							</div>

							<div class="col-sm-6 mb-3">
								<div class="form-check form-check-inline form-switch">
									<input class="form-check-input" role="switch" type="checkbox" name="is_multiple" value="1" id="isMultiple">
									<label class="form-check-label" for="isMultiple">Allouer plusieurs exemplaires</label>
								</div>
							</div>

							<div class="col-sm-6 mb-3">
								<div class="form-check form-check-inline form-switch">
									<input class="form-check-input" role="switch" type="checkbox" name="is_required" value="1" id="isRequired">
									<label class="form-check-label" for="isRequired">Obligatoire</label>
								</div>
							</div>

							<div class="text-center">
								<input type="submit" class="btn btn-primary" name="add_internship_document_type" value="Ajouter">
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