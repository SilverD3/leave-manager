<?php
require_once dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'autoload.php';

use App\Controller\PermissionRequestsController;
use Core\FlashMessages\Flash;

(new PermissionRequestsController())->add();

require_once dirname(__DIR__) . DS . 'Elements' . DS . 'header.php';

?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Demander la permission</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Accueil</a></li>
                <li class="breadcrumb-item"><a href="<?= VIEWS . 'PermissionRequests' ?>">Demandes de permission</a></li>
                <li class="breadcrumb-item active">Nouvelle</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
        <div class="row">
            <div class="col-md-12 col-xl-12 col-xxl-10">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Remplissez les champs ci-après pour ajouter un modèle de contrat</h5>

                        <!-- Check if delay has been reached -->
                        <?php if ((int)$next_permission_delay_config->getValue() > 0): ?>
                            <?php if($last_permission_nb_days != null && $last_permission_nb_days < (int) $next_permission_delay_config->getValue()): ?>
                                <div class="alert alert-danger fade show d-flex align-items-center my-2" id="delayAlert" role="alert">
                                    <span class="bi bi-exclamation-triangle flex-shrink-0 me-2" style="font-size: 25px;" role="img" aria-label="Warning:"></span>
                                    <div class="ms-md-2">
                                        Le délai inter permission de <?= $next_permission_delay_config->getValue() ?> jours ne sera pas respecté. 

                                        <?php if (time() < strtotime($employee_last_permission->getEndDate())) : ?>
                                            Votre dernière permission se terminera dans <?= $last_permission_nb_days ?> jours
                                        <?php else: ?>
                                            Votre dernière permission date d'il y a <?= $last_permission_nb_days ?> jours
                                        <?php endif; ?>

                                        <br> <a href="<?= VIEWS . 'PermissionRequests/view.php?id=' . $employee_last_permission->getId() ?>" target="_blank" class="alert-link fw-bold">Voir cette permission</a>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <?= Flash::render() ?>

                        <form class="row g-3 needs-validation" name="permissionForm" action="" method="post" novalidate>
                            <div class="mb-1">
                                <label for="prreason" class="form-label">Motif de la demande <span class="text-danger">*</span></label>
                                <input type="text" maxlength="500" class="form-control" name="reason" id="prreason" value="<?= isset($form_data['reason']) ? $form_data['reason'] : '' ?>" required>
                                <div class="invalid-feedback">Veuillez renseigner le motif de la demande.</div>
                            </div>

                            <div class="col-sm-6">
                                <label for="prstartdate" class="form-label">Date de début <span class="text-danger">*</span></label>
                                <div class="row">
                                    <div class="col-8 pe-0">
                                        <input type="date" min="<?= date('Y-m-d') ?>" class="form-control" name="start_date" id="prstartdate" value="<?= isset($form_data['start_date']) ? $form_data['start_date'] : '' ?>" required>
                                        <div class="invalid-feedback">Veuillez renseigner la date de début.</div>
                                    </div>
                                    <div class="col-4 ps-0">
                                        <input type="time" class="form-control" name="start_date_time" id="prstartdatetime" value="<?= isset($form_data['start_date_time']) ? $form_data['start_date_time'] : '08:00' ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <label for="prenddate" class="form-label">Date de fin <span class="text-danger">*</span></label>
                                <div class="row">
                                    <div class="col-8 pe-0">
                                        <input type="date" min="<?= date('Y-m-d') ?>" class="form-control" name="end_date" id="prenddate" value="<?= isset($form_data['end_date']) ? $form_data['end_date'] : '' ?>" required>
                                        <div class="invalid-feedback">Veuillez renseigner la date de fin.</div>
                                    </div>
                                    <div class="col-4 ps-0">
                                        <input type="time" class="form-control" name="end_date_time" id="prenddatetime" value="<?= isset($form_data['end_date_time']) ? $form_data['end_date_time'] : '18:00' ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="prdesc" class="form-label">Contenu de la demande <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="description" id="prdesc" rows="5" required><?= isset($form_data['description']) ? $form_data['description'] : '' ?></textarea>
                                <div class="invalid-feedback">Veuillez renseigner le contenu de la demande.</div>
                            </div>

                            <div class="text-center">
                                <input type="submit" class="btn btn-primary" name="add_permission_request" value="Enregistrer">
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

var last_permission_nb_days = parseInt("<?= $last_permission_nb_days ?>");
var delay_config = parseInt("<?= $next_permission_delay_config->getValue() ?>");

if(document.getElementById('delayAlert')) {
    document.getElementById('prstartdate').addEventListener('change', function(){
        var start_date = document.forms['permissionForm'].start_date.value + ' ' + document.forms['permissionForm'].start_date_time.value;

        var delay = last_permission_nb_days + dateDiffFromNow(start_date);

        if (delay >= delay_config ) {
            document.getElementById('delayAlert').classList.add('d-none');
        } else {
            document.getElementById('delayAlert').classList.remove('d-none');
        }
    });
}

function dateDiffFromNow(date) {
    var date1 = new Date();
    var date2 = new Date(date);

    var days = Math.abs(date2.getTime() - date1.getTime()) / (1000 * 86400);

    return Math.floor(days);
}

</script>