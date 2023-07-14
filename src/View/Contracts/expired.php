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
use Core\FlashMessages\Flash;

(new ContractsController())->expired();

require_once dirname(__DIR__) . DS . 'Elements' . DS . 'header.php';

?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Les contrats expirés</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Accueil</a></li>
                <li class="breadcrumb-item active">Contrats expirés</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
        <div class="row mt-1">
            <div class="col-12">

                <div class="card recent-sales overflow-auto">

                    <div class="card-body">
                        <h5 class="card-title">Toutes les contrats expirés </h5>

                        <?= Flash::render() ?>

                        <?php if (!empty($expiredContracts)) : ?>
                            <table class="table table-border datatable">
                                <thead>
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">Employee</th>
                                        <th scope="col">Type de contrat</th>
                                        <th scope="col">Début</th>
                                        <th scope="col">Fin</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($expiredContracts as $contract) : ?>
                                        <tr>
                                            <th scope="row"><a href="<?= VIEWS . 'Contracts' . DS . 'view.php?id=' . $contract->getId() ?>"><?= $contract->getId() ?></a>
                                            </th>
                                            <td>
                                                <a class="link" href="<?= VIEWS . 'Employees/view.php?id=' . $contract->getEmployee()->getId() ?>">
                                                    <?= $contract->getEmployee()->getFirstName() . ' ' . $contract->getEmployee()->getLastName() ?>
                                                </a>
                                            </td>
                                            <td><?= $contract->getContractType()->getName() ?></td>
                                            <td><?= DateHelper::shortDate($contract->getStartDate()) ?></td>
                                            <td><?= DateHelper::shortDate($contract->getEndDate()) ?></td>
                                            <td><span class="badge text-bg-danger">Expiré</span></td>
                                            <td>
                                                <a href="<?= VIEWS . 'Contracts/view.php?id=' . $contract->getId() ?>" class="btn btn-info btn-sm" title="Voir le contrat">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="<?= VIEWS . 'Contracts/update.php?id=' . $contract->getId() ?>" class="btn btn-primary btn-sm" title="Editer le contrat">
                                                    <i class="bi bi-pencil-square"></i>
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
                                    Aucun contrat expiré trouvé pour le moment
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