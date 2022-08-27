<?php 
require_once dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'autoload.php';

use App\Controller\CompanyController;
use App\View\Helpers\DateHelper;
use Core\FlashMessages\Flash;

(new CompanyController())->index();

require_once dirname(__DIR__) . DS . 'Elements' . DS . 'header.php';

?>

<main id="main" class="main">
	<div class="pagetitle">
		<h1> Informations de l'entreprise </h1>
		<nav>
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="<?= BASE_URL ?>"> Accueil </a></li>
				<li class="breadcrumb-item active"> L'entreprise </li>
			</ol>
		</nav>
	</div><!-- End Page Title -->

	<section class="section profile">
		<div class="row">
            
            <?= Flash::render() ?>

            <div class="col-xl-4">

                <div class="card">
                    <div class="card-body pt-4 d-flex flex-column align-items-center">
                        <img src="<?= IMAGES ?>company-illustration.png" alt="Company Image" class="w-100">
                        <h2 class="mt-2"><?= $company->getName() ?></h2>
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
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-edit">Mettre à jour</button>
                            </li>
                        </ul>

                        <div class="tab-content pt-2">

                            <div class="tab-pane fade show active profile-overview" id="profile-overview">
                                <h5 class="card-title">Informations détaillées sur l'entreprise</h5>

                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <tbody>
                                            <tr>
                                                <th>Raison sociale</th>
                                                <td><?= $company->getName() ?: 'Non défini' ?></td>
                                            </tr>
                                            <tr>
                                                <th>Nom du responsable</th>
                                                <td><?= $company->getDirectorName() ?: 'Non défini'  ?></td>
                                            </tr>
                                            <tr>
                                                <th>Adresse de localisation</th>
                                                <td><?= $company->getAddress() ?: 'Non défini'  ?></td>
                                            </tr>
                                            <tr>
                                                <th>Adresse e-mail</th>
                                                <td><?= $company->getEmail() ?: 'Non défini'  ?></td>
                                            </tr>
                                            <tr>
                                                <th>Téléphone 1</th>
                                                <td><?= $company->getTel1() ?: 'Non défini'  ?></td>
                                            </tr>
                                            <tr>
                                                <th>Téléphone 2</th>
                                                <td><?= $company->getTel2() ?: 'Non défini'  ?></td>
                                            </tr>
                                            <tr>
                                                <th>Dernière modification</th>
                                                <td><?= $company->getModified() ? DateHelper::dateTime($company->getModified()) : '/' ?></td>
                                            </tr>
                                            <?php if($company->getAbout()): ?>
                                                <tr>
                                                    <td colspan="2">
                                                        <div class="text-justify" style="text-align:justify;">
                                                            <h3 class="card-title">A propos de l'entreprise</h3>
                                                            <?= $company->getAbout() ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        
                            <div class="tab-pane fade pt-3" id="profile-edit">
                                <h5 class="card-title">Mettre à jour les informations de l'entreprise</h5>

                                <form class="row g-3 needs-validation" name="company_edit_form" id="company-edit-form" action="" method="post" novalidate>
                                    <div class="col-sm-6">
                                        <label for="cpname" class="form-label">Raison sociale <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="name" id="cpname" value="<?= isset($form_data['name']) ? $form_data['name'] : $company->getName() ?>" required>
                                        <div class="invalid-feedback">Veuillez renseigner le nom de l'entreprise.</div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="drname" class="form-label"> Nom du responsable <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="director_name" id="drname" value="<?= isset($form_data['director_name']) ? $form_data['director_name'] : $company->getDirectorName() ?>" required>
                                        <div class="invalid-feedback">Veuillez renseigner le nom du directeur de l'entreprise.</div>
                                    </div>

                                    <div class="col-sm-6">
                                        <label for="address" class="form-label">Adresse de localisation <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="address" id="address" value="<?= isset($form_data['address']) ? $form_data['address'] : $company->getAddress() ?>" placeholder="example@gmail.com" required>
                                        <div class="invalid-feedback">Veuillez renseigner l'adresse de l'entreprise.</div>
                                    </div>

                                    <div class="col-sm-6">
                                        <label for="cpemail" class="form-label">Adresse e-mail</label>
                                        <input type="email" class="form-control" name="email" id="cpemail" value="<?= isset($form_data['email']) ? $form_data['email'] : $company->getEmail() ?>" placeholder="example@gmail.com">
                                        <div class="invalid-feedback">Veuillez renseigner une adresse e-mail valide pour l'entreprise.</div>
                                    </div>

                                    <div class="col-sm-6">
                                        <label for="cptel1" class="form-label">Telephone 1</label>
                                        <input type="text" class="form-control" name="tel1" id="cptel1" value="<?= isset($form_data['tel1']) ? $form_data['tel1'] : $company->getTel1() ?>">
                                    </div>

                                    <div class="col-sm-6">
                                        <label for="cptel2" class="form-label">Telephone 2</label>
                                        <input type="text" class="form-control" name="tel2" id="cptel2" value="<?= isset($form_data['tel2']) ? $form_data['tel2'] : $company->getTel2() ?>">
                                    </div>

                                    <div class="col-sm-12">
                                        <label for="cpabout" class="form-label"> A propos de l'entreprise </label>
                                        <textarea name="about" id="cpabout" class="form-control" rows="7"><?= isset($form_data['about']) ? $form_data['about'] : $company->getAbout() ?></textarea>
                                    </div>

                                    <div class="text-center">
                                        <input type="submit" class="btn btn-primary col-sm-12 col-md-6" value="Enregistrer" name="update_company">
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