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
use Core\FlashMessages\Flash;

(new InternshipDocumentsController())->add();

require_once dirname(__DIR__) . DS . 'Elements' . DS . 'header.php';

/**
 * @var array<\App\Entity\InternshipDocumentType> $document_types
 */
?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Enregistrer un document de stage</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Accueil</a></li>
                <li class="breadcrumb-item"><a href="<?= VIEWS . 'Internships' ?>">Stages</a></li>
                <li class="breadcrumb-item active">AJouter un document</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body pt-2">

                        <?= Flash::render() ?>

                        <form class="row g-3 needs-validation" enctype="multipart/form-data" action="" method="post" novalidate>
                            <div class="col-12 mt-4">
                                <label for="itnsup" class="form-label">Type de document <span class="text-danger">*</span></label>
                                <?php if (!empty($document_types)) : ?>
                                    <select name="internship_document_type_id" class="form-control" id="itnsup" required>
                                        <option value="">Choisir un type de document</option>
                                        <?php foreach ($document_types as $type) : ?>
                                            <option value="<?= $type->getId() ?>">
                                                <?= $type->getDescription() ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback">Veuillez choisir un type de document.</div>
                                <?php else : ?>
                                    <div class="alert alert-danger" role="alert">Aucun type de document trouvé</div>
                                <?php endif; ?>
                            </div>

                            <div class="col-12 mt-4">
                                <label for="itndoc" class="form-label">Document</label>
                                <input type="file" multiple name="internship_documents[]" id="itndoc" class="form-control" required>
                                <div class="invalid-feedback">Veuillez choisir un fichier.</div>
                            </div>

                            <!-- Legend -->
                            <div class="alert alert-light alert-dismissible fade show d-flex align-items-center mb-0" role="alert">
                                <span class="bi bi-info-circle flex-shrink-0 me-2" role="img" aria-label="Info:"></span>
                                <div>
                                    Les champs marqués d'une étoile (<span class="text-danger">*</span>) sont obligatoires.
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            </div>

                            <div class="text-center">
                                <button type="button" onclick="javascript:history.back()" class="btn btn-secondary">Annuler</button>
                                <input type="submit" class="btn btn-primary" name="add_internship_document" value="Enregistrer">
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>

    </section>

</main>

<?php require_once dirname(__DIR__) . DS . 'Elements' . DS . 'footer.php'; ?>