<?php

/**
 * Leave manager : Simple app for contract and leave management.
 *
 * @copyright Copyright (c) Silevester D. (https://github.com/SilverD3)
 * @link      https://github.com/SilverD3/leave-manager Leave Manager Project
 * @since     1.0 (2022)
 */

require_once dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'autoload.php';

use App\Controller\ContractTypesController;
use App\View\Helpers\DateHelper;
use App\View\Helpers\UtilsHelper;
use Core\FlashMessages\Flash;

(new ContractTypesController())->index();

require_once dirname(__DIR__) . DS . 'Elements' . DS . 'header.php';

?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Les type de contract</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Accueil</a></li>
                <li class="breadcrumb-item active">Types de contrat</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
        <div class="row mt-2 mb-1">
            <div class="col-12">
                <a href="<?= VIEWS . 'ContractTypes/add.php' ?>" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i>
                    Nouveau type</a>
            </div>

            <div class="col-12 mt-2">
                <?= Flash::render() ?>
            </div>
        </div>

        <hr>

        <div class="row mt-1">
            <!-- Contract Types -->
            <div class="col-12">
                <div class="card recent-sales overflow-auto">

                    <div class="card-body">
                        <h5 class="card-title">Tous les types de contract</h5>

                        <?php if (!empty($contract_types)) : ?>
                            <table class="table table-border datatable">
                                <thead>
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">Nom</th>
                                        <th scope="col">Description</th>
                                        <th scope="col">Date d'ajout</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($contract_types as $contractType) : ?>
                                        <tr>
                                            <th scope="row"><?= $contractType->getId() ?></th>
                                            <td><?= $contractType->getName() ?></td>
                                            <td><?= UtilsHelper::troncate($contractType->getDescription(), 150) ?></td>
                                            <td><?= DateHelper::shortDate($contractType->getCreated()) ?></td>
                                            <td>
                                                <a href="<?= VIEWS . 'ContractTypes/update.php?id=' . $contractType->getId() ?>" class="btn btn-small btn-primary">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <button class="btn btn-danger" onclick="deleteContractType(<?= $contractType->getId() ?>)">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach ?>
                                </tbody>
                            </table>
                        <?php else : ?>
                            <div class="alert alert-primary d-flex align-items-center" role="alert">
                                <span class="bi bi-info-circle flex-shrink-0 me-2" role="img" aria-label="Info:"></span>
                                <div>
                                    Aucun type de contrat trouvé
                                </div>
                            </div>

                        <?php endif ?>
                    </div>

                </div>
            </div><!-- End Contract Types -->

        </div>

    </section>

</main>

<?php require_once dirname(__DIR__) . DS . 'Elements' . DS . 'footer.php'; ?>

<script type="text/javascript">
    function deleteContractType(id) {
        if (confirm("Voulez-vous vraiment supprimer ce type ?")) {
            var xmlhttp = new XMLHttpRequest();
            var url = "<?= VIEWS . 'ContractTypes/delete.php?ajax=1&id=' ?>" + id;

            xmlhttp.onreadystatechange = function() {
                if (xmlhttp.readyState == 4) {
                    if (xmlhttp.status == 200) {
                        location.reload();
                    } else {
                        alert("Erreur: " + (JSON.parse(xmlhttp.response)).message);
                    }
                }
            };

            xmlhttp.open("DELETE", url, true);
            xmlhttp.send();
        }
    }
</script>