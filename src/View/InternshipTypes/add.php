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

(new InternshipTypesController())->add();

require_once dirname(__DIR__) . DS . 'Elements' . DS . 'header.php';

?>

<main id="main" class="main">
	<div class="pagetitle">
		<h1>Ajouter un type de stage</h1>
		<nav>
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Accueil</a></li>
				<li class="breadcrumb-item"><a href="<?= VIEWS . 'InternshipTypes' ?>">Types de stage</a></li>
				<li class="breadcrumb-item active">Nouveau</li>
			</ol>
		</nav>
	</div><!-- End Page Title -->

	<section class="section dashboard">
		<div class="row">
			<div class="col-md-8 col-xl-8 col-xxl-6">
				<div class="card">
					<div class="card-body">
						<h5 class="card-title">Remplissez les champs ci-après pour ajouter un type de stage</h5>
						<?= Flash::render() ?>
						<form class="row g-3 needs-validation" action="" method="post" novalidate>
							<div class="mb-1">
								<label for="ittitle" class="form-label">Nom du type</label>
								<input type="text" class="form-control" name="title" id="ittitle" value="<?= isset($form_data['title']) ? $form_data['title'] : '' ?>" required>
								<div class="invalid-feedback">Veuillez renseigner le nom du type de stage.</div>
							</div>

							<div class="mb-3">
								<label for="itdesc" class="form-label">Description</label>
								<textarea class="form-control" name="description" id="itdesc" rows="3"><?= isset($form_data['description']) ? $form_data['description'] : ''; ?></textarea>
							</div>

							<div class="text-center">
								<input type="submit" class="btn btn-primary" name="add_internship_type" value="Ajouter">
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