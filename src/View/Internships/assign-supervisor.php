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

(new InternshipsController())->assignSupervisor();

require_once dirname(__DIR__) . DS . 'Elements' . DS . 'header.php';

/**
 * @var array<\App\Entity\Employee> $employees
 * @var \App\Entity\Internship $internship
 */
?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Assigner un superviseur au stagiaire</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Accueil</a></li>
                <li class="breadcrumb-item"><a href="<?= VIEWS . 'Internships' ?>">Stages</a></li>
                <li class="breadcrumb-item active">Assigner le superviseur</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
        <div class="row">
            <div class="col-md-6 m-auto">
                <div class="card">
                    <div class="card-body pt-2">

                        <?= Flash::render() ?>

                        <form class="row g-3 needs-validation" action="" method="post" novalidate>
                            <div class="col-12 mt-4">
                                <label for="itnsup" class="form-label">Superviseur <span class="text-danger">*</span></label>
                                <?php if (!empty($employees)) : ?>
                                    <select name="supervisor_id" class="form-control" id="itnsup" required>
                                        <option value="">Choisir un superviseur</option>
                                        <?php foreach ($employees as $employee) : ?>
                                            <option <?= !empty($internship->getSupervisorId()) && $internship->getSupervisorId() === $employee->getId() ? 'selected' : '' ?> value="<?= $employee->getId() ?>">
                                                <?= $employee->getFirstName() . ' ' . $employee->getLastName() ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback">Veuillez choisir un superviseur.</div>
                                <?php else : ?>
                                    <div class="alert alert-danger" role="alert">Aucun superviseur trouvé</div>
                                <?php endif; ?>

                                <input type="hidden" name="internship_id" value="<?= $internship->getId() ?>">
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
                                <input type="submit" class="btn btn-primary" name="assign_supervisor" value="Enregistrer">
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>

    </section>

</main>

<?php require_once dirname(__DIR__) . DS . 'Elements' . DS . 'footer.php'; ?>