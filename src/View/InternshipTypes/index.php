<?php

/**
 * Leave manager : Simple app for contract and leave management.
 *
 * @copyright Copyright (c) Silevester D. (https://github.com/SilverD3)
 * @link      https://github.com/SilverD3/leave-manager Leave Manager Project
 * @since     2.0 (2024)
 */

require_once dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'autoload.php';

use App\Controller\InternshipTypesController;
use App\View\Helpers\DateHelper;
use App\View\Helpers\UtilsHelper;
use Core\FlashMessages\Flash;

(new InternshipTypesController())->index();

require_once dirname(__DIR__) . DS . 'Elements' . DS . 'header.php';

/**
 * @var array<\App\Entity\InternshipType> $internship_types
 */

?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Les type de stage</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Accueil</a></li>
                <li class="breadcrumb-item active">Types de stage</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
        <div class="row mt-2 mb-1">
            <div class="col-12">
                <a href="<?= VIEWS . 'InternshipTypes/add.php' ?>" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i>
                    Nouveau type</a>
            </div>

            <div class="col-12 mt-2">
                <?= Flash::render() ?>
            </div>
        </div>

        <hr>

        <div class="row mt-1">
            <!-- Internship Types -->
            <div class="col-12">
                <div class="card recent-sales overflow-auto">

                    <div class="card-body">
                        <h5 class="card-title">Tous les types de stage</h5>

                        <?php if (!empty($internship_types)) : ?>
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
                                    <?php foreach ($internship_types as $internshipType) : ?>
                                        <tr>
                                            <th scope="row"><?= $internshipType->getId() ?></th>
                                            <td><?= $internshipType->getTitle() ?></td>
                                            <td><?= UtilsHelper::troncate($internshipType->getDescription(), 150) ?></td>
                                            <td><?= DateHelper::shortDate($internshipType->getCreated()) ?></td>
                                            <td>
                                                <a href="<?= VIEWS . 'InternshipTypes/update.php?id=' . $internshipType->getId() ?>" class="btn btn-small btn-primary">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <button class="btn btn-danger" onclick="deleteInternshipType(<?= $internshipType->getId() ?>)">
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
                                    Aucun type de stage trouvé
                                </div>
                            </div>

                        <?php endif ?>
                    </div>

                </div>
            </div><!-- End Internship Types -->

        </div>

    </section>

</main>

<?php require_once dirname(__DIR__) . DS . 'Elements' . DS . 'footer.php'; ?>

<script type="text/javascript">
    function deleteInternshipType(id) {
        if (confirm("Voulez-vous vraiment supprimer ce type ?")) {
            var xmlhttp = new XMLHttpRequest();
            var url = "<?= VIEWS . 'InternshipTypes/delete.php?ajax=1&id=' ?>" + id;

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