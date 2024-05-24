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

(new InternshipDocumentTypesController())->update();

require_once dirname(__DIR__) . DS . 'Elements' . DS . 'header.php';

/**
 * @var \App\Entity\InternshipDocumentType $internship_document_type
 */

?>

<main id="main" class="main">
	<div class="pagetitle">
		<h1>Editer le type de document de stage</h1>
		<nav>
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Accueil</a></li>
				<li class="breadcrumb-item"><a href="<?= VIEWS . 'InternshipDOcumentTypes' ?>">Types de document de stage</a></li>
				<li class="breadcrumb-item active">Editer</li>
			</ol>
		</nav>
	</div><!-- End Page Title -->

	<section class="section dashboard">
		<div class="row">
			<div class="col-md-8 col-xl-8 col-xxl-6">
				<div class="card">
					<div class="card-body">
						<h5 class="card-title">Completez les champs ci-après pour editer le type de document de stage</h5>

						<?= Flash::render() ?>

						<form class="row g-3 needs-validation" action="" method="post" novalidate>
							<div class="mb-1">
								<label for="idtcode" class="form-label">Nom du type</label>
								<input type="text" class="form-control" name="code" id="idtcode" value="<?= isset($form_data['code']) ? $form_data['code'] : $internship_document_type->getCode() ?>" required>
								<div class="invalid-feedback">Veuillez renseigner le nom du type de document de stage.</div>
							</div>

							<div class="mb-3">
								<label for="idtdesc" class="form-label">Description</label>
								<textarea class="form-control" name="description" id="idtdesc" rows="3"><?= isset($form_data['description']) ? $form_data['description'] : $internship_document_type->getDescription() ?></textarea>
							</div>

							<div class="col-sm-6 mb-3">
								<div class="form-check form-check-inline form-switch">
									<input class="form-check-input" role="switch" <?= isset($form_data['is_multiple']) && boolval($form_data['is_multiple']) === true ? ' checked' : ($internship_document_type->isMultipe() ? 'checked' : '') ?> type="checkbox" name="is_multiple" value="1" id="isMultiple">
									<label class="form-check-label" for="isMultiple">Allouer plusieurs exemplaires</label>
								</div>
							</div>

							<div class="col-sm-6 mb-3">
								<div class="form-check form-check-inline form-switch">
									<input class="form-check-input" role="switch" <?= isset($form_data['is_required']) && boolval($form_data['is_required']) === true ? ' checked' : ($internship_document_type->isRequired() ? 'checked' : '') ?> type="checkbox" name="is_required" value="1" id="isRequired">
									<label class="form-check-label" for="isRequired">Obligatoire</label>
								</div>
							</div>

							<div class="text-center">
								<input type="submit" class="btn btn-primary" name="update_internship_document_type" value="Enregistrer">
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