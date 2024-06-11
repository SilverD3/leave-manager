<?php

/**
 * Leave manager : Simple app for contract and leave management.
 *
 * @copyright Copyright (c) Silevester D. (https://github.com/SilverD3)
 * @link      https://github.com/SilverD3/leave-manager Leave Manager Project
 * @since     2.0 (2024)
 */

require_once dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'autoload.php';

use App\Controller\InternshipDocumentsController;
use App\View\Helpers\DateHelper;
use Core\FlashMessages\Flash;

(new InternshipDocumentsController())->index();

require_once dirname(__DIR__) . DS . 'Elements' . DS . 'header.php';

/**
 * @var \App\Entity\Employee $auth_user
 * @var array<\App\Entity\InternshipDocument> $internshipDocuments
 * @var int $internshipId
 */

?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Dossier de stage</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Accueil</a></li>
                <li class="breadcrumb-item active"><a href="<?= VIEWS . 'Internships' ?>">Stages</a></li>
                <li class="breadcrumb-item active">Dossier de stage</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
        <?php if ($auth_user->getRole()->getCode() == 'ADM') : ?>
            <div class="row mt-2 mb-1">
                <div class="col-12">
                    <a href="<?= VIEWS . 'Internships/upload-document.php?id=' . $internshipId ?>" class="btn btn-primary me-3 mt-2">
                        <i class="bi bi-plus-circle me-1"></i> Ajouter un stage
                    </a>
                </div>
            </div>

            <hr>
        <?php endif; ?>

        <div class="row mt-1">
            <div class="col-12">

                <div class="card recent-sales overflow-auto">

                    <div class="card-body">
                        <h5 class="card-title">Les documents de stage </h5>

                        <?= Flash::render() ?>

                        <?php if (!empty($internshipDocuments)) : ?>
                            <table class="table table-border datatable">
                                <thead>
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">Type de document</th>
                                        <th scope="col">Date d'ajout</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($internshipDocuments as $document) : ?>
                                        <tr>
                                            <td scope="row"> <?= $document->getId() ?></td>
                                            <td><?= $document->getInternshipDocumentType()->getDescription() ?></td>
                                            <td><?= DateHelper::dateTime($document->getCreated()) ?></td>
                                            <td>
                                                <a href="<?= UPLOADS . INTERNSHIP_DOCUMENTS_DIR_NAME . '/' . $document->getDocument() ?>" class="btn btn-primary btn-sm" title="Télécharger le document">
                                                    <i class="bi bi-cloud-download"></i>
                                                </a>
                                                <?php if ($auth_user->getRole()->getCode() == 'ADM' || $auth_user->getRole()->getCode() == 'EMP') : ?>
                                                    <a href="<?= VIEWS . 'Internships/edit-document.php?id=' . $document->getId() ?>" class="btn btn-primary btn-sm" title="Modifier le document">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-danger btn-sm" onclick="deleteInternshipDocument(<?= $document->getId() ?>)" title="Supprimer le document">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
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
                                    Aucun document de stage n'a été fourni pour le moment
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
    function deleteInternshipDocument(documentId) {
        if (confirm("Voulez-vous vraiment supprimer ce document de stage ?")) {
            var xmlhttp = new XMLHttpRequest();
            var url = "<?= VIEWS . 'Internships/delete-document.php?ajax=1&id=' ?>" + documentId;

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