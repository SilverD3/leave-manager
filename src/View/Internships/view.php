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
use App\View\Helpers\DateHelper;
use Core\FlashMessages\Flash;

(new InternshipsController())->view();

require_once dirname(__DIR__) . DS . 'Elements' . DS . 'header.php';

/**
 * @var \App\Entity\Internship $internship
 * @var \App\Entity\Employee $auth_user
 */

?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Détails du stage</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Accueil</a></li>
                <li class="breadcrumb-item"><a href="<?= VIEWS . "Internships" ?>">Stages</a></li>
                <li class="breadcrumb-item active">Détails</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
        <div class="row">
            <div class="col-md-12 col-xl-12 col-xxl-10">

                <?= Flash::render() ?>

                <?php if (
                    $internship->getStatus() == 'active'
                    && (!empty($internship->getEndDate()) && strtotime($internship->getEndDate()) < time())
                    && $auth_user->getRole()->getCode() == 'ADM'
                ) : ?>

                    <div class="alert alert-info" role="alert">
                        <h4 class="alert-heading"><i class="bi bi-exclamation-triangle"></i> Delai du stage expiré </h4>
                        <hr class="m-2">
                        <p>La période de stage est passée. Quelle action souhaitez-vous prendre ?</p>
                        <p class="mb-0 text-center">
                            <button type="button" class="btn btn-secondary mt-1" onclick="completeInternship(<?= $internship->getId() ?>)">
                                <i class="bi bi-x"></i> Marquer le stage comme terminer
                            </button>
                            <button type="button" class="btn btn-primary mt-1" data-bs-toggle="modal" data-bs-target="#extendInternship">
                                <i class="bi bi-arrow-clockwise"></i> Prolonger le stage
                            </button>
                        </p>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body">

                        <h5 class="card-title"><i class="bi bi-info-circle"></i> Informations détaillées sur le stage</h5>

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover">
                                <tbody>
                                    <tr>
                                        <th>Nom du stagiaire</th>
                                        <td>
                                            <?= $internship->getFirstName() . ' ' . $internship->getLastName() ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Type de stage</th>
                                        <td><?= $internship->getInternshipType()->getTitle() ?></td>
                                    </tr>
                                    <tr>
                                        <th>Sex</th>
                                        <td><?= $internship->getSex() == 'M' ? 'Homme' : 'Femme' ?></td>
                                    </tr>
                                    <tr>
                                        <th>Nom de l'école/institut</th>
                                        <td><?= $internship->getSchoolName() ?? '/' ?></td>
                                    </tr>
                                    <tr>
                                        <th>Superviseur</th>
                                        <td>
                                            <?php if (empty($internship->getSupervisor())) : ?>
                                                Non assigné

                                            <?php elseif ($auth_user->getRole()->getCode() == 'ADM' || $auth_user->getRole()->getCode() == 'EMP') : ?>
                                                <a href="<?= VIEWS . 'Employees' . DS . 'view.php?id=' . $internship->getSupervisorId() ?>">
                                                    <?= $internship->getSupervisor()->getLastName() . ' ' . $internship->getSupervisor()->getFirstName() ?>
                                                </a>
                                            <?php else : ?>
                                                <?= $internship->getSupervisor()->getLastName() . ' ' . $internship->getSupervisor()->getFirstName() ?>
                                            <?php endif ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Date de naissance</th>
                                        <td><?= DateHelper::shortDate($internship->getBirthdate()) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Date de début</th>
                                        <td><?= DateHelper::shortDate($internship->getStartDate()) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Date de fin</th>
                                        <td><?= DateHelper::shortDate($internship->getEndDate()) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Compte utilisateur</th>
                                        <td>
                                            <?php if (!empty($internship->getUserId())) : ?>
                                                <a href="<?= VIEWS . 'Employees' . DS . 'view.php?id=' . $internship->getUserId() ?>">
                                                    <?= $internship->getUserId() ?>
                                                </a>
                                            <?php else : ?>
                                                <span class="badge text-bg-secondary">NON</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Statut</th>
                                        <td>
                                            <?php if ($internship->getStatus() == 'pending') : ?>
                                                <span class="badge text-bg-info">En attente</span>
                                            <?php elseif ($internship->getStatus() == 'active') : ?>
                                                <?php if (!empty($internship->getEndDate()) && strtotime($internship->getEndDate()) < time()) : ?>
                                                    <span class="badge text-bg-danger">Passé</span>
                                                <?php else : ?>
                                                    <span class="badge text-bg-primary">En cours</span>
                                                <?php endif; ?>
                                            <?php elseif ($internship->getStatus() == 'terminated') : ?>
                                                <span class="badge text-bg-secondary">Terminé</span>
                                            <?php else : ?>
                                                <span class="badge text-bg-info"> <?= $internship->getStatus() ?></span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Date d'ajout</th>
                                        <td><?= DateHelper::dateTime($internship->getCreated()) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Dernière mise à jour </th>
                                        <td><?= !empty($internship->getModified()) ? DateHelper::dateTime($internship->getModified()) : '/' ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <h5 class="card-title"><i class="bi bi-gear"></i> Actions</h5>

                        <div class="alert alert-light py-3 border">
                            <!-- Extend action -->
                            <?php if (
                                ($auth_user->getRole()->getCode() == 'ADM' || $auth_user->getRole()->getCode() == 'EMP')
                                && ($internship->getStatus() == 'active' || $internship->getStatus() == 'pending')
                                && !empty($internship->getEndDate())
                                && (strtotime($internship->getEndDate()) - time()) < 60 * 60 * 24 * 3
                            ) : ?>
                                <button type="button" class="btn btn-primary mt-1" data-bs-toggle="modal" data-bs-target="#extendInternship">
                                    <i class="bi bi-calendar"></i> Prolonger
                                </button>
                            <?php endif; ?>

                            <!-- Terminate action -->
                            <?php if ($auth_user->getRole()->getCode() == 'ADM' && $internship->getStatus() !== 'terminated') : ?>
                                <button type="button" class="btn btn-secondary mt-1" onclick="completeInternship(<?= $internship->getId() ?>)">
                                    <i class="bi bi-x"></i> Terminer
                                </button>
                            <?php endif; ?>

                            <?php if ($auth_user->getRole()->getCode() == 'ADM' && $internship->getStatus() !== 'terminated') : ?>
                                <a href="<?= VIEWS . 'Internships/assign-supervisor.php?id=' . $internship->getId() ?>" class="btn btn-primary mt-1" title="Assigner un superviseur">
                                    <i class="bi bi-person-badge"></i> Assigner un superviseur
                                </a>
                            <?php endif; ?>

                            <!-- Export to PDF action -->
                            <?php if (!empty($internship->getReportPath())) : ?>
                                <a target="_blank" href="<?= UPLOADS . INTERNSHIP_REPORTS_DIR_NAME . '/' . $internship->getReportPath() ?>" class="btn btn-primary mt-1">
                                    <i class="bi bi-file-pdf"></i> Télécharger le rapport
                                </a>
                            <?php endif ?>

                            <button type="button" class="btn btn-primary mt-1" data-bs-toggle="modal" data-bs-target="#uploadReport">
                                <i class="bi bi-cloud-upload"></i> Téléverser le rapport
                            </button>

                            <!-- Delete action -->
                            <?php if ($auth_user->getRole()->getCode() == 'ADM' && $internship->getStatus() != 'active' && $internship->getStatus() != 'terminated') : ?>
                                <button type="button" class="btn btn-danger mt-1" onclick="deleteInternship(<?= $internship->getId() ?>)">
                                    <i class="bi bi-trash"></i> Supprimer
                                </button>
                            <?php endif; ?>

                        </div>

                        <?php if (!empty($internship->getReportPath()) && str_ends_with(strtolower($internship->getReportPath()), 'pdf')) : ?>
                            <h5 class="card-title"><i class="bi bi-eye"></i> Prévisualisation du rapport de stage</h5>

                            <div class="p3">
                                <object width="100%" height="500" id="pdfPreview" type="application/pdf" data="<?= UPLOADS . INTERNSHIP_REPORTS_DIR_NAME . '/' . $internship->getReportPath() ?>?#zoom=100&scrollbar=1&toolbar=1&navpanes=0">
                                    <p>Une erreur est survenue lors de l'affichage du stage.
                                        <a href="<?= UPLOADS . INTERNSHIP_REPORTS_DIR_NAME . '/' . $internship->getReportPath() ?>" target="_blank">
                                            Essayer d'ouvrir dans une autre page <i class="bi bi-arrow-up-right-square"></i>
                                        </a>
                                    </p>
                                </object>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Internship extend modal -->
    <div class="modal fade" id="extendInternship" tabindex="-1" aria-labelledby="extendInternshipLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="extendInternshipLabel"><i class="bi bi-arrow-clockwise"></i> Prolonger le stage</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="extend.php" method="post" class="g-3 needs-validation" novalidate>
                    <div class="modal-body">
                        <div class="">
                            <label for="cenddate" class="form-label">Nouvelle date de fin</label>
                            <input type="date" name="extend_to" id="cenddate" min="<?= date('Y-m-d', strtotime($internship->getEndDate())) ?>" value="<?= date('Y-m-d', strtotime($internship->getEndDate())) ?>" class="form-control" required>
                            <div class="invalid-feedback">Veuillez choisir une date de fin.</div>
                        </div>
                        <input type="hidden" name="iid" value="<?= $internship->getId() ?>">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                        <input type="submit" class="btn btn-primary" name="extend_internship" value="Enregistrer">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Internship extend modal end -->

    <!-- Upload report modal -->
    <div class="modal fade" id="uploadReport" tabindex="-1" aria-labelledby="uploadReportLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadReportLabel"><i class="bi bi-cloud-upload"></i> Téléverser le rapport de stage</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="upload-report.php" enctype="multipart/form-data" method="post" class="g-3 needs-validation" novalidate>
                    <div class="modal-body">
                        <div class="">
                            <label for="itnreport" class="form-label">Rapport de stage</label>
                            <input type="file" name="internship_report" id="itnreport" class="form-control" required>
                            <div class="invalid-feedback">Veuillez choisir un fichier.</div>
                        </div>
                        <input type="hidden" name="internship_id" value="<?= $internship->getId() ?>">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <input type="submit" class="btn btn-primary" name="upload_internship_report" value="Enregistrer">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Upload report modal end -->
</main>

<?php require_once dirname(__DIR__) . DS . 'Elements' . DS . 'footer.php'; ?>

<script type="text/javascript">
    function completeInternship(id) {
        if (confirm("Voulez-vous vraiment marquer ce stage comme terminé ?")) {
            var xmlhttp = new XMLHttpRequest();
            var url = "<?= VIEWS . 'Internships/complete.php?ajax=1&id=' ?>" + id;

            xmlhttp.onreadystatechange = function() {
                if (xmlhttp.readyState == 4) {
                    if (xmlhttp.status == 200) {
                        window.location.reload();
                    } else {
                        alert("Erreur: " + (JSON.parse(xmlhttp.response)).message);
                    }
                }
            };

            xmlhttp.open("POST", url, true);
            xmlhttp.send();
        }
    }

    function deleteInternship(id) {
        if (confirm("Voulez-vous vraiment supprimer ce stage ?")) {
            var xmlhttp = new XMLHttpRequest();
            var url = "<?= VIEWS . 'Internships/delete.php?ajax=1&id=' ?>" + id;

            xmlhttp.onreadystatechange = function() {
                if (xmlhttp.readyState == 4) {
                    if (xmlhttp.status == 200) {
                        window.location.href = '<?= VIEWS . 'Internships' ?>';
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