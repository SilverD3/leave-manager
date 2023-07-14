<?php

/**
 * Leave manager : Simple app for contract and leave management.
 *
 * @copyright Copyright (c) Silevester D. (https://github.com/SilverD3)
 * @link      https://github.com/SilverD3/leave-manager Leave Manager Project
 * @since     1.0 (2022)
 */

require_once dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'autoload.php';

use App\Controller\PermissionRequestsController;
use Core\FlashMessages\Flash;

(new PermissionRequestsController())->update();

require_once dirname(__DIR__) . DS . 'Elements' . DS . 'header.php';

?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Editer la demande de permission</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Accueil</a></li>
                <li class="breadcrumb-item"><a href="<?= VIEWS . 'PermissionRequests' ?>">Demandes de permission</a></li>
                <li class="breadcrumb-item active">Mis à jour</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
        <div class="row">
            <div class="col-md-12 col-xl-12 col-xxl-10">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Veuillez completer les champs ci-après</h5>

                        <?= Flash::render() ?>

                        <form class="row g-3 needs-validation" action="" method="post" novalidate>
                            <div class="mb-1">
                                <label for="prreason" class="form-label">Motif de la demande <span class="text-danger">*</span></label>
                                <input type="text" maxlength="500" class="form-control" name="reason" id="prreason" value="<?= isset($form_data['reason']) ? $form_data['reason'] : $permissionRequest->getReason() ?>" required>
                                <div class="invalid-feedback">Veuillez renseigner le motif de la demande.</div>
                            </div>

                            <div class="col-sm-6">
                                <label for="prstartdate" class="form-label">Date de début <span class="text-danger">*</span></label>
                                <div class="row">
                                    <div class="col-8 pe-0">
                                        <input type="date" min="<?= date('Y-m-d') ?>" class="form-control" name="start_date" id="prstartdate" value="<?= isset($form_data['start_date']) ? $form_data['start_date'] : date('Y-m-d', strtotime($permissionRequest->getStartDate())) ?>" required>
                                        <div class="invalid-feedback">Veuillez renseigner la date de début.</div>
                                    </div>
                                    <div class="col-4 ps-0">
                                        <input type="time" class="form-control" name="start_date_time" id="prstartdatetime" value="<?= isset($form_data['start_date_time']) ? $form_data['start_date_time'] : date('H:i', strtotime($permissionRequest->getStartDate())) ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <label for="prenddate" class="form-label">Date de fin <span class="text-danger">*</span></label>
                                <div class="row">
                                    <div class="col-8 pe-0">
                                        <input type="date" min="<?= date('Y-m-d') ?>" class="form-control" name="end_date" id="prenddate" value="<?= isset($form_data['end_date']) ? $form_data['end_date'] : date('Y-m-d', strtotime($permissionRequest->getEndDate())) ?>" required>
                                        <div class="invalid-feedback">Veuillez renseigner la date de fin.</div>
                                    </div>
                                    <div class="col-4 ps-0">
                                        <input type="time" class="form-control" name="end_date_time" id="prenddatetime" value="<?= isset($form_data['end_date_time']) ? $form_data['end_date_time'] : date('H:i', strtotime($permissionRequest->getEndDate())) ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="prdesc" class="form-label">Contenu de la demande <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="description" id="prdesc" rows="5" required><?= isset($form_data['description']) ? $form_data['description'] : $permissionRequest->getDescription() ?></textarea>
                                <div class="invalid-feedback">Veuillez renseigner le contenu de la demande.</div>
                            </div>

                            <div class="text-center">
                                <input type="submit" class="btn btn-primary" name="update_permission_request" value="Enregistrer">
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

<script src="<?= TEMPLATE_PATH ?>assets/vendor/tinymce/tinymce.min.js"></script>
<script type="text/javascript">
    tinymce.init({
        selector: '#prdesc',
        language: 'fr',
        toolbar_sticky: true,
        browser_spellcheck: true,
        height: 500,
    });
</script>