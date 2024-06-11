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

(new InternshipsController())->index();

require_once dirname(__DIR__) . DS . 'Elements' . DS . 'header.php';

/**
 * @var \App\Entity\Employee $auth_user
 * @var array<\App\Entity\Internship> $internships
 * @var int $nb_passed
 * @var string $status
 */

?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Les stages</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Accueil</a></li>
                <li class="breadcrumb-item active">Stages</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
        <?php if ($auth_user->getRole()->getCode() == 'ADM') : ?>
            <div class="row mt-2 mb-1">
                <div class="col-12">
                    <a href="<?= VIEWS . 'Internships/add.php' ?>" class="btn btn-primary me-3 mt-2"><i class="bi bi-plus-circle me-1"></i> Ajouter un stage </a>
                    <a href="<?= VIEWS . 'Internships/passed.php' ?>" class="btn btn-secondary mt-2"><i class="bi bi-clock-history me-1"></i> Stages passés <span class="badge bg-primary"><?= $nb_passed ?></span> </a>
                </div>
            </div>

            <hr>
        <?php endif; ?>

        <div class="row mt-1">
            <div class="col-12">

                <div class="card recent-sales overflow-auto">

                    <div class="card-body">
                        <h5 class="card-title">Toutes les stages </h5>

                        <?php if ($auth_user->getRole()->getCode() == 'ADM') : ?>
                            <button class="btn btn-secondary ms-sm-2 mb-sm-2 float-sm-end" type="button" data-bs-toggle="modal" data-bs-target="#selectYearModal">
                                <i class="bi bi-lightning"></i> Choisir un statut
                            </button>

                            <div class="modal fade" id="selectYearModal" tabindex="-1" aria-labelledby="selectYearModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="selectYearModalLabel"><i class="bi bi-lightning"></i> Choisir le statut
                                                des stages à afficher</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="" method="get" class="g-3 needs-validation" novalidate>
                                            <div class="modal-body">
                                                <div class="">
                                                    <label for="cstatus" class="form-label">Statut</label>
                                                    <select id="cstatus" class="form-select" name="status" required>
                                                        <option value="">Choisir un statut</option>
                                                        <option <?= isset($status) && $status === 'all' ? 'selected' : '' ?> value="all">Tous</option>
                                                        <option <?= isset($status) && $status === 'pending' ? 'selected' : '' ?> value="pending">En attente</option>
                                                        <option <?= isset($status) && $status === 'active' ? 'selected' : '' ?> value="active">En cours</option>
                                                    </select>
                                                    <div class="invalid-feedback">Veuillez choisir un statut.</div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                                <input type="submit" class="btn btn-primary" value="Afficher">
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?= Flash::render() ?>

                        <?php if (!empty($internships)) : ?>
                            <table class="table table-border datatable">
                                <thead>
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">Nom complet</th>
                                        <th scope="col">Type de stage</th>
                                        <th scope="col">Superviseur</th>
                                        <th scope="col">Début</th>
                                        <th scope="col">Fin</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Utilisateur</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($internships as $internship) : ?>
                                        <tr>
                                            <td scope="row">
                                                <a href="<?= VIEWS . 'Internships' . DS . 'view.php?id=' . $internship->getId() ?>">
                                                    <?= $internship->getId() ?>
                                                </a>
                                            </td>
                                            <td>
                                                <?= $internship->getFirstName() . ' ' . $internship->getLastName() ?>
                                            </td>
                                            <td><?= $internship->getInternshipType()->getTitle() ?></td>
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
                                            <td><?= DateHelper::shortDate($internship->getStartDate()) ?></td>
                                            <td><?= DateHelper::shortDate($internship->getEndDate()) ?></td>
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
                                            <td>
                                                <?php if (!empty($internship->getUserId())) : ?>
                                                    <a href="<?= VIEWS . 'Employees' . DS . 'view.php?id=' . $internship->getUserId() ?>">
                                                        <?= $internship->getUserId() ?>
                                                    </a>
                                                <?php else : ?>
                                                    <span class="badge text-bg-secondary">NON</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="<?= VIEWS . 'Internships/view.php?id=' . $internship->getId() ?>" class="btn btn-info btn-sm" title="Voir le stage">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="<?= VIEWS . 'Internships/documents.php?id=' . $internship->getId() ?>" class="btn btn-primary btn-sm" title="Dossier de stage">
                                                    <i class="bi bi-stack"></i>
                                                </a>
                                                <?php if ($auth_user->getRole()->getCode() == 'ADM') : ?>
                                                    <a href="<?= VIEWS . 'Internships/edit.php?id=' . $internship->getId() ?>" class="btn btn-primary btn-sm" title="Modifier le stage">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>
                                                    <?php if (DateHelper::isFuture($internship->getEndDate())) : ?>
                                                        <a href="<?= VIEWS . 'Internships/assign-supervisor.php?id=' . $internship->getId() ?>" class="btn btn-primary btn-sm" title="Assigner un superviseur">
                                                            <i class="bi bi-person-badge"></i>
                                                        </a>
                                                    <?php endif; ?>

                                                    <?php if ($internship->getStatus() == 'pending') : ?>
                                                        <button type="button" class="btn btn-danger btn-sm" onclick="deleteInternship(<?= $internship->getId() ?>)" title="Supprimer le stage">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach ?>
                                </tbody>
                            </table>
                        <?php else : ?>
                            <div class="alert alert-primary d-flex align-items-center mt-2" role="alert">
                                <span class="bi bi-info-circle flex-shrink-0 me-2" role="img" aria-label="Info:"></span>
                                <div>
                                    <?= isset($status) && $status !== 'all' ? 'Aucune correspondance pour le status choisi' : 'Aucun stage trouvé pour le moment' ?>
                                </div>
                            </div>
                        <?php endif ?>
                    </div>
                </div>
            </div>

        </div>

    </section>

</main>

<?php require_once dirname(__DIR__) . DS . 'Elements' . DS . 'footer.php'; ?>

<script type="text/javascript">
    function deleteInternship(internship_id) {
        if (confirm("Voulez-vous vraiment supprimer ce stage ?")) {
            var xmlhttp = new XMLHttpRequest();
            var url = "<?= VIEWS . 'Internships/delete.php?ajax=1&id=' ?>" + internship_id;

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
</script>