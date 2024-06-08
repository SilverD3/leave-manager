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

(new InternshipsController())->myInternships();

require_once dirname(__DIR__) . DS . 'Elements' . DS . 'header.php';

/**
 * @var \App\Entity\Employee $auth_user
 * @var array<\App\Entity\Internship> $internships
 */

?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Mes stages</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Accueil</a></li>
                <li class="breadcrumb-item"><a href="<?= VIEWS . 'Internships' ?>">Stages</a></li>
                <li class="breadcrumb-item active">Mes Stages</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
        <div class="row mt-1">
            <div class="col-12">

                <div class="card recent-sales overflow-auto">

                    <div class="card-body">
                        <h5 class="card-title">Toutes mes stages </h5>

                        <?= Flash::render() ?>

                        <?php if (!empty($internships)) : ?>
                            <table class="table table-border datatable">
                                <thead>
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">Nom complet</th>
                                        <th scope="col">Type de stage</th>
                                        <th scope="col">Début</th>
                                        <th scope="col">Fin</th>
                                        <th scope="col">Status</th>
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
                                            <td><?= DateHelper::shortDate($internship->getStartDate()) ?></td>
                                            <td><?= DateHelper::shortDate($internship->getEndDate()) ?></td>
                                            <td>
                                                <?php if ($internship->getStatus() == 'pending') : ?>
                                                    <span class="badge text-bg-info">En attente</span>
                                                <?php elseif ($internship->getStatus() == 'active') : ?>
                                                    <?php if (!empty($internship->getEndDate()) && strtotime($internship->getEndDate()) < time()) : ?>
                                                        <span class="badge text-bg-danger">Expiré</span>
                                                    <?php else : ?>
                                                        <span class="badge text-bg-primary">En cours</span>
                                                    <?php endif; ?>
                                                <?php elseif ($internship->getStatus() == 'terminated') : ?>
                                                    <span class="badge text-bg-secondary">Résilié</span>
                                                <?php else : ?>
                                                    <span class="badge text-bg-info"> <?= $internship->getStatus() ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="<?= VIEWS . 'Internships/view.php?id=' . $internship->getId() ?>" class="btn btn-info btn-sm" title="Voir le stage">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach ?>
                                </tbody>
                            </table>
                        <?php else : ?>
                            <div class="alert alert-primary d-flex align-items-center mt-2" role="alert">
                                <span class="bi bi-info-circle flex-shrink-0 me-2" role="img" aria-label="Info:"></span>
                                <div>
                                    Aucun stage trouvé pour le moment
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