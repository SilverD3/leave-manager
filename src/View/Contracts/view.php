<?php

/**
 * Leave manager : Simple app for contract and leave management.
 *
 * @copyright Copyright (c) Silevester D. (https://github.com/SilverD3)
 * @link      https://github.com/SilverD3/leave-manager Leave Manager Project
 * @since     1.0 (2022)
 */

require_once dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'autoload.php';

use App\Controller\ContractsController;
use App\View\Helpers\DateHelper;
use App\View\Helpers\UtilsHelper;
use Core\FlashMessages\Flash;

(new ContractsController())->view();

require_once dirname(__DIR__) . DS . 'Elements' . DS . 'header.php';

?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Détails du contrat</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Accueil</a></li>
                <li class="breadcrumb-item"><a href="<?= VIEWS . "Contracts" ?>">Contrats</a></li>
                <li class="breadcrumb-item active">Détails</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
        <div class="row">
            <div class="col-md-12 col-xl-12 col-xxl-10">

                <?= Flash::render() ?>

                <?php if (
                    $contract->getStatus() == 'active'
                    && (!empty($contract->getEndDate()) && strtotime($contract->getEndDate()) < time())
                    && $auth_user->getRole()->getCode() == 'ADM'
                ) : ?>

                    <div class="alert alert-info" role="alert">
                        <h4 class="alert-heading"><i class="bi bi-exclamation-triangle"></i> Contrat expiré </h4>
                        <hr class="m-2">
                        <p>Ce contrat est arrivé à expiration. Quelle action souhaitez-vous prendre ?</p>
                        <p class="mb-0 text-center">
                            <button type="button" class="btn btn-secondary mt-1"><i class="bi bi-x"></i> Resilier le contrat </button>
                            <button type="button" class="btn btn-primary mt-1" data-bs-toggle="modal" data-bs-target="#extendContract">
                                <i class="bi bi-arrow-clockwise"></i> Prolonger le contrat
                            </button>
                        </p>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body">

                        <h5 class="card-title"><i class="bi bi-info-circle"></i> Informations détaillées sur le contrat</h5>

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover">
                                <tbody>
                                    <tr>
                                        <th>Intitulé</th>
                                        <td>
                                            <?php if (!empty($contract->getTitle())) : echo $contract->getTitle();
                                            else : ?>
                                                Contrat de l'employé
                                                <?= $contract->getEmployee()->getFirstName() . ' ' . $contract->getEmployee()->getLastName() ?>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Type de contract</th>
                                        <td><?= $contract->getContractType()->getName() ?></td>
                                    </tr>
                                    <tr>
                                        <th>Date de début</th>
                                        <td><?= DateHelper::shortDate($contract->getStartDate()) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Date de fin</th>
                                        <td><?= !empty($contract->getEndDate()) ? DateHelper::shortDate($contract->getEndDate()) : '/' ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Poste occupé</th>
                                        <td><?= $contract->getJobObject() ?></td>
                                    </tr>
                                    <tr>
                                        <th>Missions / Description des tâches</th>
                                        <td><?= !empty($contract->getJobDescription()) ? $contract->getJobDescription() : '/' ?></td>
                                    </tr>
                                    <tr>
                                        <th>Salaire</th>
                                        <td>
                                            <?= !empty($contract->getJobSalary()) ? UtilsHelper::currency((float)$contract->getJobSalary()) . ' XAF' : '/' ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Volume horaire</th>
                                        <td><?= !empty($contract->getHourlyRate()) ? $contract->getHourlyRate() : '/' ?></td>
                                    </tr>
                                    <tr>
                                        <th>Statut</th>
                                        <td>
                                            <?php if ($contract->getStatus() == 'pending') : ?>
                                                <span class="badge text-bg-info">En attente</span>
                                            <?php elseif ($contract->getStatus() == 'active') : ?>
                                                <?php if (!empty($contract->getEndDate()) && strtotime($contract->getEndDate()) < time()) : ?>
                                                    <span class="badge text-bg-danger">Expiré</span>
                                                <?php else : ?>
                                                    <span class="badge text-bg-primary">En cours</span>
                                                <?php endif; ?>
                                            <?php elseif ($contract->getStatus() == 'terminated') : ?>
                                                <span class="badge text-bg-secondary">Résilié</span>
                                            <?php else : ?>
                                                <span class="badge text-bg-info"> <?= $contract->getStatus() ?></span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Date d'ajout</th>
                                        <td><?= DateHelper::dateTime($contract->getCreated()) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Dernière mise à jour </th>
                                        <td><?= !empty($contract->getModified()) ? DateHelper::dateTime($contract->getModified()) : '/' ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <?php if ($auth_user->getRole()->getCode() == 'ADM') : ?>
                            <h5 class="card-title"><i class="bi bi-gear"></i> Actions</h5>

                            <div class="alert alert-light py-3 border">
                                <!-- Extend action -->
                                <?php if ($contract->getStatus() == 'active' && !empty($contract->getEndDate()) && (strtotime($contract->getEndDate()) - time()) < 60 * 60 * 24 * 7) : ?>
                                    <button type="button" class="btn btn-primary mt-1" data-bs-toggle="modal" data-bs-target="#extendContract">
                                        <i class="bi bi-arrow-clockwise"></i> Prolonger
                                    </button>
                                <?php endif; ?>

                                <!-- Terminate action -->
                                <?php if ($contract->getStatus() == 'active') : ?>
                                    <button type="button" class="btn btn-danger mt-1" onclick="terminateContract(<?= $contract->getId() ?>)">
                                        <i class="bi bi-x"></i> Resilier
                                    </button>
                                <?php endif; ?>

                                <!-- Export to PDF action -->
                                <a href="<?= VIEWS . "Contracts/preview.php?id=" . $contract->getId() ?>" class="btn btn-primary mt-1">
                                    <i class="bi bi-file-pdf"></i> Exporter en PDF
                                </a>

                                <!-- Delete action -->
                                <?php if ($contract->getStatus() != 'active' && $contract->getStatus() != 'terminated') : ?>
                                    <button type="button" class="btn btn-danger mt-1" onclick="deleteContract(<?= $contract->getId() ?>)">
                                        <i class="bi bi-trash"></i> Supprimer
                                    </button>
                                <?php endif; ?>

                            </div>

                        <?php endif; ?>

                        <h5 class="card-title"><i class="bi bi-eye"></i> Prévisualisation</h5>

                        <?php if (empty($contract->getPdf())) : ?>
                            <div class="" style="max-height: 500px; overflow-y: auto;">
                                <div class="editor-content border p-3">
                                    <?= $preview ?>
                                </div>
                            </div>
                        <?php else : ?>
                            <div class="p3">
                                <object width="100%" height="500" id="pdfPreview" type="application/pdf" data="<?= ASSETS . 'pdf/' . $contract->getPdf() ?>?#zoom=100&scrollbar=1&toolbar=1&navpanes=0">
                                    <p>Une erreur est survenue lors de l'affichage du contrat. <a href="<?= ASSETS . 'pdf/' . $contract->getPdf() ?>" target="_blank">Essayer d'ouvrir dans une autre
                                            page <i class="bi bi-arrow-up-right-square"></i></a></p>
                                </object>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contract extend modal -->
    <div class="modal fade" id="extendContract" tabindex="-1" aria-labelledby="extendContractLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="extendContractLabel"><i class="bi bi-arrow-clockwise"></i> Choisir le statut des
                        contrat à afficher</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="extend.php" method="post" class="g-3 needs-validation" novalidate>
                    <div class="modal-body">
                        <div class="">
                            <label for="cenddate" class="form-label">Nouvelle date de fin</label>
                            <input type="date" name="extend_to" id="cenddate" value="<?= date('Y-m-d', strtotime($contract->getEndDate())) ?>" class="form-control" required>
                            <div class="invalid-feedback">Veuillez choisir une date de fin.</div>
                        </div>
                        <input type="hidden" name="cid" value="<?= $contract->getId() ?>">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                        <input type="submit" class="btn btn-primary" name="extend_contract" value="Enregistrer">
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<?php require_once dirname(__DIR__) . DS . 'Elements' . DS . 'footer.php'; ?>

<script type="text/javascript">
    function terminateContract(id) {
        if (confirm("Voulez-vous vraiment resilier ce contrat ?")) {
            var xmlhttp = new XMLHttpRequest();
            var url = "<?= VIEWS . 'Contracts/terminate.php?ajax=1&id=' ?>" + id;

            xmlhttp.onreadystatechange = function() {
                if (xmlhttp.readyState == 4) {
                    if (xmlhttp.status == 200) {
                        location.reload();
                    } else {
                        alert("Erreur: " + (JSON.parse(xmlhttp.response)).message);
                    }
                }
            };

            xmlhttp.open("POST", url, true);
            xmlhttp.send();
        }
    }

    function deleteContract(id) {
        if (confirm("Voulez-vous vraiment supprimer ce contrat ?")) {
            var xmlhttp = new XMLHttpRequest();
            var url = "<?= VIEWS . 'Contracts/delete.php?ajax=1&id=' ?>" + id;

            xmlhttp.onreadystatechange = function() {
                if (xmlhttp.readyState == 4) {
                    if (xmlhttp.status == 200) {
                        location.href('<?= VIEWS . 'Contracts' ?>');
                    } else {
                        alert("Erreur: " + (JSON.parse(xmlhttp.response)).message);
                    }
                }
            };

            xmlhttp.open("POST", url, true);
            xmlhttp.send();
        }
    }
</script>