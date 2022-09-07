<?php 
require_once dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'autoload.php';

use App\Controller\ContractsController;
use Core\FlashMessages\Flash;

(new ContractsController())->update();

require_once dirname(__DIR__) . DS . 'Elements' . DS . 'header.php';

?>

<main id="main" class="main">
	<div class="pagetitle">
		<h1>Mettre à jour le contrat</h1>
		<nav>
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Accueil</a></li>
				<li class="breadcrumb-item"><a href="<?= VIEWS . 'Contracts' ?>">Contrats</a></li>
				<li class="breadcrumb-item active">Mise à jour</li>
			</ol>
		</nav>
	</div><!-- End Page Title -->

    <section class="section dashboard">
        <div class="row">
            <div class="col-md-12 col-xl-12 col-xxl-10">
                <div class="card">
                    <div class="card-body pt-2">

                        <?= Flash::render() ?>

                        <form class="row g-3 needs-validation" action="" method="post" novalidate>
                            
                            <div class="py-1"><h4 class="card-title py-0">Généralités</h4></div>
                        
                            <div class=" col-md-7">
                                <label for="ctitle" class="form-label"> Intitulé du contrat </label>
                                <input type="text" maxlength="500" placeholder="E.g: CDD de l'employé Jean" class="form-control" name="title" id="ctitle" value="<?= isset($form_data['title']) ? $form_data['title'] : $contract->getTitle() ?>">
                            </div>

                            <div class="col-sm-6 col-md-5">
                                <label for="ceid" class="form-label">Employé <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" disabled value="<?= $contract->getEmployee()->getFirstName() . ' ' . $contract->getEmployee()->getLastName() ?>" >
                                       
                            </div>

                            <div class="col-sm-6 col-md-4">
                                <label for="cctid" class="form-label">Type de contrat <span class="text-danger">*</span></label>
                                <input type="text" disabled class="form-control" value="<?= $contract->getContractType()->getName() ?>">  
                            </div>

                            <div class="col-sm-6 col-md-4">
                                <label for="cstartdate" class="form-label">Date de début <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="start_date" id="cstartdate" value="<?= isset($form_data['start_date']) ? $form_data['start_date'] : date('Y-m-d', strtotime($contract->getStartDate())) ?>" required>
                                <div class="invalid-feedback">Veuillez renseigner la date de début.</div>
                            </div>

                            <div class="col-sm-6 col-md-4">
                                <label for="cenddate" class="form-label">Date de fin </label>
                                <input type="date" class="form-control" name="end_date" id="cenddate" value="<?= isset($form_data['end_date']) ? $form_data['end_date'] : date('Y-m-d', strtotime($contract->getEndDate())) ?>">
                            </div>

                            <div class="mt-4"><h4 class="card-title py-0">Responsabilités et conditions de travail</h4></div>

                            <div class="col-sm-6 col-md-4">
                                <label for="cjobobject" class="form-label">Poste occupé </label>
                                <input type="text" class="form-control" placeholder="E.g: Commercial" name="job_object" id="cjobobject" value="<?= isset($form_data['job_object']) ? $form_data['job_object'] : $contract->getJobObject() ?>">
                            </div>

                            <div class="col-sm-6 col-md-4">
                                <label for="cjobsalary" class="form-label">Salaire </label>
                                <input type="number" step="0.001" class="form-control" placeholder="E.g: 350.000" name="job_salary" id="cjobsalary" value="<?= isset($form_data['job_salary']) ? $form_data['job_salary'] : $contract->getJobSalary() ?>">
                            </div>

                            <div class="col-sm-6 col-md-4">
                                <label for="chrate" class="form-label"> Volume horaire </label>
                                <input type="text" class="form-control" placeholder="E.g: 45 heures / semaine" name="hourly_rate" id="chrate" value="<?= isset($form_data['hourly_rate']) ? $form_data['hourly_rate'] : $contract->getHourlyRate() ?>">
                            </div>

                            <div class="mb-1">
                                <label for="chrate" class="form-label"> Missions / Description des tâches </label>
                                <textarea name="job_description" id="chrate" placeholder="Brève description des missions ici..." class="form-control" rows="4"><?= isset($form_data['job_description']) ? $form_data['job_description'] : $contract->getJobDescription() ?></textarea>
                            </div>

                            <!-- Legend -->
                            <div class="alert alert-light alert-dismissible fade show d-flex align-items-center mb-0" role="alert">
                                <span class="bi bi-info-circle flex-shrink-0 me-2" role="img" aria-label="Info:"></span>
                                <div>
                                    Les champs marqués d'une étoile (<span class="text-danger">*</span>) sont obligatoires.
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            </div>

                            <div class="text-center">
                                <button type="button" onclick="javascript:history.back()" class="btn btn-secondary">Annuler</button>
                                <input type="submit" class="btn btn-primary" name="update_contract" value="Enregistrer">
                            </div>

                        </form>
                    </div>
                </div>
            </div>

        </div>

    </section>

</main>

<?php require_once dirname(__DIR__) . DS . 'Elements' . DS . 'footer.php'; ?>