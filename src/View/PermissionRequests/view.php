<?php
require_once dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'autoload.php';

use App\Controller\PermissionRequestsController;
use App\View\Helpers\DateHelper;
use Core\FlashMessages\Flash;

(new PermissionRequestsController())->view();

require_once dirname(__DIR__) . DS . 'Elements' . DS . 'header.php';

?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Détails de la demande</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Accueil</a></li>
                <li class="breadcrumb-item"><a href="<?= VIEWS . 'PermissionRequests' ?>">Demandes de permission</a></li>
                <li class="breadcrumb-item active">Détails</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
        <div class="row">
            <div class="col-md-12 col-xl-12 col-xxl-10">
                <div class="card">
                    <div class="card-body">

                        <h2 class="card-title mb-0"><?= $permissionRequest->getReason() ?></h2>

                        <?= Flash::render() ?>

                        <hr class="mt-0">

                        <div class="p-y mb-3">
                            <img class="rounded-circle m-e-sm" style="width: 30px;" src="<?= IMAGES ?>user_icon.png" alt="User Icon"> Redigée par 
                            <a href="<?= VIEWS . 'Employees/view.php?id='.$permissionRequest->getEmployee()->getID() ?>">
                                <?= $permissionRequest->getEmployee()->getFirstName() . ' ' . $permissionRequest->getEmployee()->getLastName() ?>
                            </a> 
                            <span class="text-muted">le <?= DateHelper::dateTime($permissionRequest->getCreated()) ?> </span> 

                            <a class="link" data-bs-toggle="collapse" href="#more-details" role="button" aria-expanded="false" aria-controls="more-details">
                                <i class="bi bi-eye"></i> Plus de détails
                            </a>

                            <div class="collapse mt-2" id="more-details">
                                <div class="card card-body p-0 mb-2 pt-3" style="box-shadow: none;">
                                    <div class="table-responsive">
                                        <table class="table table-sm border">
                                            <tbody>
                                                <tr>
                                                    <th>Date de début</th>
                                                    <td><?= DateHelper::dateTime($permissionRequest->getStartDate()) ?></td>
                                                </tr>
                                                <tr>
                                                    <th>Date de fin</th>
                                                    <td><?= DateHelper::dateTime($permissionRequest->getEndDate()) ?></td>
                                                </tr>
                                                <tr>
                                                    <th>Statut</th>
                                                    <td>
                                                        <?php if ($permissionRequest->getStatus() == 'pending'): ?>
                                                            <span class="badge text-bg-primary">En attente</span>
                                                        <?php elseif($permissionRequest->getStatus() == 'approved'): ?>
                                                            <span class="badge text-bg-success">Approuvée</span>
                                                        <?php elseif($permissionRequest->getStatus() == 'disapproved'): ?>
                                                            <span class="badge text-bg-danger">Rejetée</span>
                                                        <?php else: ?>
                                                            <span class="badge text-bg-info"> <?= $permissionRequest->getStatus() ?></span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                                <?php if (!empty($permissionRequest->getModified())): ?>
                                                    <tr>
                                                        <th>Dernière modification</th>
                                                        <td><?= DateHelper::dateTime($permissionRequest->getModified()) ?></td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div> 
                                </div>
                            </div>

                        </div>

                        <div class="editor-content border p-3">
                            <?= $permissionRequest->getDescription() ?>
                        </div>

                        <?php if ($permissionRequest->getStatus() == 'pending') : ?>

                            <!-- Check if delay has been reached -->
                            <?php if ((int)$next_permission_delay_config->getValue() > 0): ?>
                                <?php if($last_permission_nd_days != null && $last_permission_nd_days < (int) $next_permission_delay_config->getValue()): ?>
                                    <div class="alert alert-danger fade show d-flex align-items-center my-2" role="alert">
                                        <span class="bi bi-exclamation-triangle flex-shrink-0 me-2" style="font-size: 25px;" role="img" aria-label="Warning:"></span>
                                        <div class="ms-md-2">
                                            Le délai inter permission de <?= $next_permission_delay_config->getValue() ?> jours n'est pas respecté. 
                                            <?php if (time() < strtotime($employee_last_permission->getEndDate())) : ?>
                                                La dernière permission se terminera dans <?= $last_permission_nd_days ?> jours
                                            <?php else: ?>
                                                La dernière permission date d'il y a <?= $last_permission_nd_days ?> jours
                                            <?php endif; ?>
                                            <br> <a href="<?= VIEWS . 'PermissionRequests/view.php?id=' . $employee_last_permission->getId() ?>" target="_blank" class="alert-link fw-bold">Voir cette permission</a>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>

                            <!-- Action buttons -->
                            <?php if($auth_user->getRole()->getCode() == 'ADM'): ?>
                                <div class="actions text-center mt-5">
                                    <button type="button" class="btn btn-success px-sm-4" data-bs-toggle="modal" data-bs-target="#approveRequest">
                                        <i class="bi bi-check"></i> Approuver
                                    </button>
                                    <button type="button" class="btn btn-danger px-sm-4" onclick="disapproveRequest(<?= $permissionRequest->getId() ?>)">
                                        <i class="bi bi-x"></i> Rejeter
                                    </button>
                                </div>

                                <div class="modal fade" id="approveRequest" tabindex="-1" aria-labelledby="approveRequestLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="approveRequestLabel"><i class="bi bi-check"></i> Approuver la demande de permission</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form name="approveForm">
                                                <div class="modal-body">
                                                    <div class="alert alert-info fade show d-flex align-items-center" role="alert">
                                                        <span class="bi bi-info-circle flex-shrink-0 me-2" role="img" aria-label="Info:"></span>
                                                        <div>
                                                            L'approbation de la demande est une opération irréversible
                                                        </div>
                                                    </div>
                                                    <?php if ($permission_reduce_leave_config->getValue() == 'OUI'): ?>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" value="1" type="checkbox" name="reduce" id="reduceCheck"> Prendre en compte la durée de cette permission dans le calcul de la durée du congé de l'employé ?
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                    <button type="button" class="btn btn-success" onclick="approveRequest(<?= $permissionRequest->getId() ?>)">Approuver</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>

                    </div>
                </div>
            </div>

        </div>
    </section>
</main>

<?php require_once dirname(__DIR__) . DS . 'Elements' . DS . 'footer.php'; ?>

<script type="text/javascript">

function approveRequest(request_id)
{
    var reduce = 0;
    
    if (document.getElementById('reduceCheck')) {
        var reduceCheck = document.forms['approveForm'].reduce.checked;
        if (reduceCheck) {
            reduce = 1;
        } 
    }

    var xmlhttp = new XMLHttpRequest();
    var url = "<?= VIEWS . 'PermissionRequests/approve.php?ajax=1&id=' ?>" + request_id + "&reduce=" + reduce;

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4){
            if(xmlhttp.status == 200) {
                location.reload();
            }else {
                alert("Erreur: " + (JSON.parse(xmlhttp.response)).message);
            }
        } 
    };
    
    xmlhttp.open("POST", url, true);
    xmlhttp.send();
}

function disapproveRequest(request_id)
{
    if (confirm("Voulez-vous vraiment rejeter cette demande ?")) {
        var xmlhttp = new XMLHttpRequest();
        var url = "<?= VIEWS . 'PermissionRequests/disapprove.php?ajax=1&id=' ?>" + request_id;

        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4){
                if(xmlhttp.status == 200) {
                    location.reload();
                }else {
                    alert("Erreur: " + (JSON.parse(xmlhttp.response)).message);
                }
            } 
        };
        
        xmlhttp.open("POST", url, true);
        xmlhttp.send();
    }
}

</script>