<?php 
require_once dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'autoload.php';

use App\Controller\EmployeesController;
use Core\FlashMessages\Flash;

(new EmployeesController())->update();

require_once dirname(__DIR__) . DS . 'Elements' . DS . 'header.php';

?>

<main id="main" class="main">
	<div class="pagetitle">
		<h1>Mettre à jour un employé</h1>
		<nav>
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Accueil</a></li>
				<li class="breadcrumb-item"><a href="<?= VIEWS . 'Employees' ?>">Employés</a></li>
				<li class="breadcrumb-item active">Edition</li>
			</ol>
		</nav>
	</div><!-- End Page Title -->

	<section class="section dashboard">
		<div class="row">
			<div class="col-md-12 col-xl-8">
				<div class="card">
					<div class="card-body">
			  			<h5 class="card-title">Remplissez les champs ci-après pour mettre à jour l'employé</h5>

			  			<form class="row g-3 needs-validation" action="" method="post" novalidate>

							<?= Flash::render() ?>

							<div class="col-sm-6">
								<label for="ufname" class="form-label">Nom</label>
								<input type="text" class="form-control" name="first_name" id="ufname" value="<?= isset($form_data['first_name']) ? $form_data['first_name'] : $employee->getFirstName() ?>" required>
								<div class="invalid-feedback">Veuillez renseigner le nom de l'employé.</div>
							</div>
							<div class="col-sm-6">
								<label for="ulname" class="form-label">Prénom</label>
								<input type="text" class="form-control" name="last_name" id="ulname" value="<?= isset($form_data['last_name']) ? $form_data['last_name'] : $employee->getLastName() ?>" required>
								<div class="invalid-feedback">Veuillez renseigner le prénom de l'employé.</div>
							</div>

							<div class="col-sm-6">
								<label for="uemail" class="form-label">Adresse e-mail</label>
								<input type="email" disabled class="form-control" name="email" id="uemail" value="<?= $employee->getEmail() ?>" placeholder="example@gmail.com" required>
								<div class="invalid-feedback">Veuillez renseigner l'adresse e-mail de l'employé.</div>
							</div>
							<div class="col-sm-6">
								<label for="urole" class="form-label">Rôle</label>
								<select id="urole" class="form-select" name="role_id" required>
									<option selected value="">Choisir un rôle</option>
									<?php if(empty($roles)):?>
										<div class="alert alert-danger">
											Aucun rôle trouvé.
										</div>
									<?php else:?>
										<?php foreach($roles as $role): ?>
											<option <?= (isset($form_data['role_id']) && $form_data['role_id'] == $role->getId()) ? 'selected': (($employee->getRoleId() == $role->getId()) ? 'selected' : '') ?> value="<?= $role->getId() ?>">
												<?= $role->getName();?>
											</option>
										<?php endforeach; ?>
									<?php endif;?>
								</select>
								<div class="invalid-feedback">Veuillez choisir le rôle de l'employé.</div>
							</div>

							<div class="col-sm-6">
								<label for="uname" class="form-label">Nom d'utilisateur</label>
								<input type="text" class="form-control" name="username" id="uname" value="<?= isset($form_data['username']) ? $form_data['username'] : $employee->getUsername() ?>" required>
								<div class="invalid-feedback">Veuillez renseigner le nom d'utilisateur de l'employé.</div>
							</div>
							<div class="col-sm-6">
								<label for="upwd" class="form-label">Mot de passe</label>
								<input type="password" class="form-control" name="password" id="upwd">
								<div class="invalid-feedback">Veuillez renseigner le mot de passe de l'employé.</div>
							</div>

							<div class="text-center">
								<input type="submit" class="btn btn-primary" value="Enregistrer" name="update_employee">
								<a href="<?= VIEWS . 'Employees' ?>" class="btn btn-secondary">Retour</a>
							</div>
						</form>

					</div>
		  		</div>
			</div>

		</div>

	</section>

</main>

<?php require_once dirname(__DIR__) . DS . 'Elements' . DS . 'footer.php'; ?>