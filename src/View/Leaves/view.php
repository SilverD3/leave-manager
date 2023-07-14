<?php

/**
 * Leave manager : Simple app for contract and leave management.
 *
 * @copyright Copyright (c) Silevester D. (https://github.com/SilverD3)
 * @link      https://github.com/SilverD3/leave-manager Leave Manager Project
 * @since     1.0 (2022)
 */

require_once dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'autoload.php';

use App\Controller\LeavesController;
use App\View\Helpers\DateHelper;
use Core\FlashMessages\Flash;

(new LeavesController())->view();

require_once dirname(__DIR__) . DS . 'Elements' . DS . 'header.php';

?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Détails du congé</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Accueil</a></li>
                <li class="breadcrumb-item"><a href="<?= VIEWS . 'Leaves' ?>">Congés</a></li>
                <li class="breadcrumb-item active">Détails</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
        <div class="row">
            <div class="col-md-12 col-xl-12 col-xxl-10">
                <div class="card">
                    <div class="card-body pt-2">

                        <?= Flash::render() ?>

                        <h5 class="card-title"><i class="bi bi-info-circle"></i> Informations détaillées sur le congé</h5>

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover">
                                <tbody>
                                    <tr>
                                        <th>Employé</th>
                                        <td>
                                            <?= $leave->getEmployee()->getFirstName() . ' ' . $leave->getEmployee()->getLastName() ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Année</th>
                                        <td><?= $leave->getYear() ?></td>
                                    </tr>
                                    <tr>
                                        <th>Date de départ</th>
                                        <td><?= DateHelper::shortDate($leave->getStartDate()) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Date de retour</th>
                                        <td><?= DateHelper::shortDate($leave->getEndDate()) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Nombre de jours ouvrables</th>
                                        <td><?= $leave->getDays() . ' jours' ?></td>
                                    </tr>
                                    <tr>
                                        <th>Statut</th>
                                        <td>
                                            <?php if (DateHelper::periodStatus($leave->getStartDate(), $leave->getEndDate()) == 'past') : ?>
                                                <span class="badge text-bg-secondary">Passé</span>
                                            <?php elseif (DateHelper::periodStatus($leave->getStartDate(), $leave->getEndDate()) == 'present') : ?>
                                                <span class="badge text-bg-success">En cours</span>
                                            <?php elseif (DateHelper::periodStatus($leave->getStartDate(), $leave->getEndDate()) == 'future') : ?>
                                                <span class="badge text-bg-primary">Prochainement</span>
                                            <?php else : ?>
                                                <span class="badge text-bg-info"> <?= $leave->getStatus() ?></span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Date de planification</th>
                                        <td><?= DateHelper::dateTime($leave->getCreated()) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Dernière mise à jour </th>
                                        <td><?= !empty($leave->getModified()) ? DateHelper::dateTime($leave->getModified()) : '/' ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <h5 class="card-title"><i class="bi bi-person-lines-fill"></i> Récapitulatif des congés pour l'année
                            <?= $leave->getYear() ?></h5>

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th>Total des jours de congés</th>
                                        <td><?= $leave_nb_days . ' jours' ?></td>
                                    </tr>
                                    <?php if (isset($spent_days_in_permissions)) : ?>
                                        <tr>
                                            <th>Jours dépensés dans les permissions</th>
                                            <td><?= $spent_days_in_permissions . ' jour(s)' ?></td>
                                        </tr>
                                    <?php endif; ?>
                                    <tr>
                                        <th>Nombre de jours restant</th>
                                        <td><?= $nb_remaining_days . ' jour(s)' ?></td>
                                    </tr>

                                    <?php if (!empty($other_leaves)) : ?>
                                        <tr>
                                            <th>Autres congés pour la même année</th>
                                            <td>
                                                <?php foreach ($other_leaves as $oth_leave) : ?>
                                                    <p><a href="<?= VIEWS . 'Leaves/view.php?id=' . $oth_leave->getId() ?>">
                                                            <?= DateHelper::shortDate($oth_leave->getStartDate()) . ' -> ' . DateHelper::shortDate($oth_leave->getEndDate()) ?>
                                                        </a></p>
                                                <?php endforeach; ?>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require_once dirname(__DIR__) . DS . 'Elements' . DS . 'footer.php'; ?>