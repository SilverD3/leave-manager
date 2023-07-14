<?php

/**
 * Leave manager : Simple app for contract and leave management.
 *
 * @copyright Copyright (c) Silevester D. (https://github.com/SilverD3)
 * @link      https://github.com/SilverD3/leave-manager Leave Manager Project
 * @since     1.0 (2022)
 */

require_once dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'autoload.php';

use App\Controller\CompanyController;
use App\View\Helpers\DateHelper;
use Core\FlashMessages\Flash;

(new CompanyController())->view();

require_once dirname(__DIR__) . DS . 'Elements' . DS . 'header.php';

?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1> Informations de l'entreprise </h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>"> Accueil </a></li>
                <li class="breadcrumb-item active"> L'entreprise </li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section profile">
        <div class="row mb-3">

            <?= Flash::render() ?>

            <div class="col-xl-4">

                <div class="card h-100">
                    <div class="card-body pt-4 d-flex flex-column align-items-center">
                        <img src="<?= IMAGES ?>company-illustration.png" alt="Company Image" class="w-100">
                        <h2 class="mt-2"><?= $company->getName() ?></h2>
                    </div>
                </div>

            </div>

            <div class="col-xl-8">

                <div class="card h-100">
                    <div class="card-body pt-3">
                        <h5 class="card-title">Informations détaillées sur l'entreprise</h5>

                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <tbody>
                                    <tr>
                                        <th>Raison sociale</th>
                                        <td><?= $company->getName() ?: 'Non défini' ?></td>
                                    </tr>
                                    <tr>
                                        <th>Nom du responsable</th>
                                        <td><?= $company->getDirectorName() ?: 'Non défini'  ?></td>
                                    </tr>
                                    <tr>
                                        <th>Adresse de localisation</th>
                                        <td><?= $company->getAddress() ?: 'Non défini'  ?></td>
                                    </tr>
                                    <tr>
                                        <th>Adresse e-mail</th>
                                        <td><?= $company->getEmail() ?: 'Non défini'  ?></td>
                                    </tr>
                                    <tr>
                                        <th>Téléphone 1</th>
                                        <td><?= $company->getTel1() ?: 'Non défini'  ?></td>
                                    </tr>
                                    <tr>
                                        <th>Téléphone 2</th>
                                        <td><?= $company->getTel2() ?: 'Non défini'  ?></td>
                                    </tr>
                                    <tr>
                                        <th>Dernière modification</th>
                                        <td><?= $company->getModified() ? DateHelper::dateTime($company->getModified()) : '/' ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($company->getAbout()) : ?>
            <div class="row">
                <div class="col-12">
                    <div class="card card-body">
                        <h3 class="card-title">A propos de l'entreprise</h3>
                        <div class="text-justify" style="text-align:justify;">
                            <?= $company->getAbout() ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </section>
</main>

<?php require_once dirname(__DIR__) . DS . 'Elements' . DS . 'footer.php'; ?>