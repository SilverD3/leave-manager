<?php 
require_once dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'autoload.php';

use App\Controller\EmployeesController;
use App\View\Helpers\DateHelper;
use Core\FlashMessages\Flash;

(new EmployeesController())->profile();

require_once dirname(__DIR__) . DS . 'Elements' . DS . 'header.php';

?>

<main id="main" class="main">
	<div class="pagetitle">
		<h1><?= $employee->getFirstName() . ' ' . $employee->getLastName() ?></h1>
		<nav>
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Accueil</a></li>
				<li class="breadcrumb-item"><a href="<?= VIEWS . 'Employees' ?>">Employés</a></li>
				<li class="breadcrumb-item active"><?= $employee->getFirstName() . ' ' . $employee->getLastName() ?></li>
			</ol>
		</nav>
	</div><!-- End Page Title -->

	<section class="section profile">
        <div class="row">

            <?= Flash::render() ?>

            <div class="col-xl-4">

                <div class="card">
                    <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">

                        <img src="<?= IMAGES ?>user_icon.png" alt="User icon" class="rounded-circle w-75">
                        <h2><?= $employee->getFirstName() . ' ' . $employee->getLastName() ?></h2>
                        <h6><?= $employee->getRole()->getName() ?> </h6>
                    </div>
                </div>

            </div>

            <div class="col-xl-8">

                <div class="card">
                    <div class="card-body pt-3">
                        <ul class="nav nav-tabs nav-tabs-bordered">

                            <li class="nav-item">
                                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-overview">Détails</button>
                            </li>

                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-edit">Editer le profil</button>
                            </li>
                        </ul>

                        <div class="tab-content pt-2">

                            <div class="tab-pane fade show active profile-overview" id="profile-overview">
                                <h5 class="card-title">Détails du profil</h5>

                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <tbody>
                                            <tr>
                                                <th>Nom complet</th>
                                                <td><?= $employee->getFirstName() . ' ' . $employee->getLastName() ?></td>
                                            </tr>
                                            <tr>
                                                <th>Rôle</th>
                                                <td><?= $employee->getRole()->getName() ?></td>
                                            </tr>
                                            <tr>
                                                <th>Adresse e-mail</th>
                                                <td><?= $employee->getEmail() ?></td>
                                            </tr>
                                            <tr>
                                                <th>Nom d'utilisateur</th>
                                                <td><?= $employee->getUsername() ?></td>
                                            </tr>
                                            <tr>
                                                <th>Date d'ajout</th>
                                                <td><?= DateHelper::dateTime($employee->getCreated()) ?></td>
                                            </tr>
                                            <tr>
                                                <th>Dernière modification</th>
                                                <td><?= DateHelper::dateTime($employee->getModified()) ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        
                            <div class="tab-pane fade profile-edit pt-3" id="profile-edit">
                                <h5 class="card-title">Editer le profil</h5>

                                <form class="row g-3 needs-validation" name="profile_edit_form" id="profile-edit-form" action="" method="post" novalidate>

                                    <div class="col-sm-6">
                                        <label for="ufname" class="form-label">Nom</label>
                                        <input type="text" class="form-control" name="first_name" id="ufname" value="<?= $employee->getFirstName() ?>" required>
                                        <div class="invalid-feedback">Veuillez renseigner le nom de l'employé.</div>
                                    </div>

                                    <div class="col-sm-6">
                                        <label for="ulname" class="form-label">Prénom</label>
                                        <input type="text" class="form-control" name="last_name" id="ulname" value="<?= $employee->getLastName() ?>" required>
                                        <div class="invalid-feedback">Veuillez renseigner le prénom de l'employé.</div>
                                    </div>

                                    <div class="col-sm-6">
                                        <label for="uemail" class="form-label">Adresse e-mail</label>
                                        <input type="email" disabled class="form-control" id="uemail" value="<?= $employee->getEmail() ?>" required>
                                        <div class="invalid-feedback">Veuillez renseigner l'adresse e-mail de l'employé.</div>
                                    </div>

                                    <div class="col-sm-6">
                                        <label for="uname" class="form-label">Nom d'utilisateur</label>
                                        <input type="text" class="form-control" name="username" id="uname" value="<?= $employee->getUsername() ?>" required>
                                        <div class="invalid-feedback">Veuillez renseigner le nom d'utilisateur de l'employé.</div>
                                    </div>

                                    <hr class="w-100 m-2">
                                    <h5 class="card-title p-0 m-0">Changer le mot de passe</h5>

                                    <div class="col-sm-6">
                                        <label for="upwd" class="form-label">Nouveau mot de passe</label>
                                        <input type="password" class="form-control" name="password" id="upwd">
                                    </div>

                                    <div class="col-sm-6">
                                        <label for="cfmpwd" class="form-label">Confirmer le mot de passe</label>
                                        <input type="password" class="form-control" name="cfmpwd" id="cfmpwd">
                                        <div class="invalid-feedback" id="cfmpwd-error"></div>
                                    </div>

                                    <hr class="w-100 m-2">
                                    <h5 class="card-title p-0 m-0">Entrez votre mot de passe actuel pour confirmer les modifications</h5>
                                    <div class="col-sm-6">
                                        <label for="upwd" class="form-label">Mot de passe</label>
                                        <input type="password" class="form-control" name="upwd" id="upwd" required>
                                        <div class="invalid-feedback">Mot de passe requis</div>
                                    </div>

                                    <div class="text-center">
                                        <input type="submit" class="btn btn-primary" value="Enregistrer" name="edit_profile">
                                        <a href="<?= VIEWS . 'Employees' ?>" class="btn btn-secondary">Retour</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>

<?php require_once dirname(__DIR__) . DS . 'Elements' . DS . 'footer.php'; ?>

<script type="text/javascript">

var $form = document.getElementById('profile-edit-form');

$form.addEventListener('submit', function($e) {
    var $pwd = document.forms.profile_edit_form.elements.password;
    var $cfmpwd = document.forms.profile_edit_form.elements.cfmpwd;

    console.log($pwd.value);
    console.log($cfmpwd.value);

    $form.classList.remove('was-validated');

    if ($pwd.value.length > 0) {
        $cfmpwd.setAttribute('required', 'required');

        if ($pwd.value != $cfmpwd.value) {
            
            document.getElementById('cfmpwd-error').innerHTML = 'Les mots de passe ne correspondent pas';
            $cfmpwd.classList.remove('is-valid');
            $cfmpwd.classList.add('is-invalid');
            $cfmpwd.setCustomValidity("Invalid field.");

            $form.classList.add('was-validated');
            $e.preventDefault();
            return;
        } else {
            document.getElementById('cfmpwd-error').innerHTML = '';
            $cfmpwd.classList.remove('is-invalid');
            $cfmpwd.classList.add('is-valid');
            $cfmpwd.setCustomValidity("");

            $form.classList.add('was-validated');
        }
    } else {
        document.getElementById('cfmpwd-error').innerHTML = '';
        $cfmpwd.removeAttribute('required');
        $cfmpwd.classList.remove('is-invalid');
        $cfmpwd.classList.add('is-valid');
        $cfmpwd.setCustomValidity("");

        $form.classList.add('was-validated');
    }
});

</script>