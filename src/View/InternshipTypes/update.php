<?php

/**
 * Leave manager : Simple app for contract and leave management.
 *
 * @copyright Copyright (c) Silevester D. (https://github.com/SilverD3)
 * @link      https://github.com/SilverD3/leave-manager Leave Manager Project
 * @since     2.0 (2024)
 */

require_once dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'autoload.php';

use App\Controller\InternshipTypesController;
use Core\FlashMessages\Flash;

(new InternshipTypesController())->update();

require_once dirname(__DIR__) . DS . 'Elements' . DS . 'header.php';

/**
 * @var \App\Entity\InternshipType $internship_type
 */

?>

<main id="main" class="main">
	<div class="pagetitle">
		<h1>Editer le type de stage</h1>
		<nav>
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Accueil</a></li>
				<li class="breadcrumb-item"><a href="<?= VIEWS . 'InternshipTypes' ?>">Types de stage</a></li>
				<li class="breadcrumb-item active">Editer</li>
			</ol>
		</nav>
	</div><!-- End Page Title -->

	<section class="section dashboard">
		<div class="row">
			<div class="col-md-8 col-xl-8 col-xxl-6">
				<div class="card">
					<div class="card-body">
						<h5 class="card-title">Completez les champs ci-après pour editer le type de stage</h5>

						<?= Flash::render() ?>

						<form class="row g-3 needs-validation" action="" method="post" novalidate>
							<div class="mb-1">
								<label for="ittitle" class="form-label">Nom du type</label>
								<input type="text" class="form-control" name="title" id="ittitle" value="<?= isset($form_data['title']) ? $form_data['title'] : $internship_type->getTitle() ?>" required>
								<div class="invalid-feedback">Veuillez renseigner le nom du type de stage.</div>
							</div>

							<div class="mb-3">
								<label for="itdesc" class="form-label">Description</label>
								<textarea class="form-control" name="description" id="itdesc" rows="3"><?= isset($form_data['description']) ? $form_data['description'] : $internship_type->getDescription() ?></textarea>
							</div>

							<div class="text-center">
								<input type="submit" class="btn btn-primary" name="update_internship_type" value="Enregistrer">
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