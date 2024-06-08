<?php

/**
 * Leave manager : Simple app for contract and leave management.
 *
 * @copyright Copyright (c) Silevester D. (https://github.com/SilverD3)
 * @link      https://github.com/SilverD3/leave-manager Leave Manager Project
 * @since     2.0 (2024)
 */

require_once dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'autoload.php';

use App\Controller\InternshipsController;
use Core\FlashMessages\Flash;

(new InternshipsController())->update();

require_once dirname(__DIR__) . DS . 'Elements' . DS . 'header.php';

/**
 * @var array<\App\Entity\InternshipType> $internship_types
 * @var \App\Entity\Internship $internship
 */
?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Modifier le stage</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Accueil</a></li>
                <li class="breadcrumb-item"><a href="<?= VIEWS . 'Internships' ?>">Stages</a></li>
                <li class="breadcrumb-item active">Mettre à jour</li>
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

                            <div class="py-1">
                                <h4 class="card-title py-0">A propos du stagiaire</h4>
                            </div>

                            <div class="col-md-6">
                                <label for="itnfname" class="form-label"> Nom <span class="text-danger">*</span></label>
                                <input type="text" maxlength="50" required placeholder="Nom du stagiaire" class="form-control" name="first_name" id="itnfname" value="<?= isset($form_data['first_name']) ? $form_data['first_name'] : $internship->getFirstName() ?>">
                                <div class="invalid-feedback">Veuillez entrer le nom du stagiaire.</div>
                            </div>

                            <div class="col-md-6">
                                <label for="itnlname" class="form-label"> Prénom <span class="text-danger">*</span></label>
                                <input type="text" maxlength="50" required placeholder="Prénom du stagiaire" class="form-control" name="last_name" id="itnlname" value="<?= isset($form_data['last_name']) ? $form_data['last_name'] : $internship->getLastName() ?>">
                                <div class="invalid-feedback">Veuillez entrer le prénom du stagiaire</div>
                            </div>

                            <div class="col-md-4">
                                <label for="itnsex" class="form-label"> Sexe <span class="text-danger">*</span></label>
                                <select name="sex" class="form-control" id="itnsex" required>
                                    <option value="">Choisir un sexe</option>
                                    <option <?= $internship->getSex() === 'M' ? 'selected' : '' ?> value="M">Homme</option>
                                    <option <?= $internship->getSex() === 'F' ? 'selected' : '' ?> value="F">Femme</option>
                                </select>
                                <div class="invalid-feedback">Veuillez choisir un sexe</div>
                            </div>

                            <div class="col-md-4">
                                <label for="itnbirthdate" class="form-label"> Date de naissance <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" required name="birthdate" id="itnbirthdate" value="<?= isset($form_data['birthdate']) ? $form_data['birthdate'] : $internship->getBirthdate() ?>">
                                <div class="invalid-feedback">Veuillez choisir une date de naissance</div>
                            </div>

                            <div class="col-md-4">
                                <label for="itnschname" class="form-label"> Nom de l'école </label>
                                <input type="text" maxlength="100" placeholder="Nom de l'école" class="form-control" name="school_name" id="itnschname" value="<?= isset($form_data['school_name']) ? $form_data['school_name'] : $internship->getSchoolName() ?>">
                                <div class="invalid-feedback">Veuillez entrer le nom de l'école</div>
                            </div>

                            <div class="mt-4">
                                <h4 class="card-title py-0">Termes du stage</h4>
                            </div>

                            <div class="col-sm-6 col-md-4">
                                <label for="cctid" class="form-label">Type de stage <span class="text-danger">*</span></label>
                                <?php if (!empty($internship_types)) : ?>
                                    <select name="internship_type_id" class="form-control" id="cctid" required>
                                        <option value="">Choisir un type de stage</option>
                                        <?php foreach ($internship_types as $internship_type) : ?>
                                            <option <?= $internship->getInternshipTypeId() == $internship_type->getId() ? 'selected' : '' ?> value="<?= $internship_type->getId() ?>"> <?= $internship_type->getTitle() ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback">Veuillez choisir un type de stage.</div>
                                <?php else : ?>
                                    <div class="alert alert-danger" role="alert">Aucun type de stage trouvé</div>
                                <?php endif; ?>
                            </div>

                            <div class="col-sm-6 col-md-4">
                                <label for="cstartdate" class="form-label">Date de début <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="start_date" id="cstartdate" value="<?= isset($form_data['start_date']) ? $form_data['start_date'] : $internship->getStartDate() ?>" required>
                                <div class="invalid-feedback">Veuillez renseigner la date de début.</div>
                            </div>

                            <div class="col-sm-6 col-md-4">
                                <label for="cenddate" class="form-label">Date de fin </label>
                                <input type="date" class="form-control" name="end_date" id="cenddate" value="<?= isset($form_data['end_date']) ? $form_data['end_date'] : $internship->getEndDate() ?>">
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
                                <input type="submit" class="btn btn-primary" name="update_internship" value="Enregistrer">
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>

    </section>

</main>

<?php require_once dirname(__DIR__) . DS . 'Elements' . DS . 'footer.php'; ?>