<?php

/**
 * Leave manager : Simple app for contract and leave management.
 *
 * @copyright Copyright (c) Silevester D. (https://github.com/SilverD3)
 * @link      https://github.com/SilverD3/leave-manager Leave Manager Project
 * @since     1.0 (2022)
 */

require_once dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'autoload.php';

use App\Controller\ConfigsController;
use App\View\Helpers\DateHelper;
use Core\FlashMessages\Flash;

(new ConfigsController())->index();

require_once dirname(__DIR__) . DS . 'Elements' . DS . 'header.php';

?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Les paramètres</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Accueil</a></li>
                <li class="breadcrumb-item active">Paramètres</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">

        <div class="row mt-1">
            <!-- Configs -->
            <div class="col-12">
                <div class="card recent-sales overflow-auto">
                    <div class="card-body table-responsive">
                        <h5 class="card-title">Liste des paramètres d'application</h5>

                        <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#resetConfigsModal"><i class="bi bi-clock-history"></i> Tout réinitialiser</button>

                        <div class="modal fade" id="resetConfigsModal" tabindex="-1" aria-labelledby="resetConfigsModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="resetConfigsModalLabel"><i class="bi bi-lightning"></i> Réinitialiser
                                            tous les paramètres</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>

                                    <div class="modal-body">
                                        <div class="alert alert-info alert-dismissible fade show d-flex align-items-center" role="alert">
                                            <span class="bi bi-exclamation-triangle flex-shrink-0 me-2" role="img" aria-label="Alerte:"></span>
                                            <div class="fw-bold">
                                                La réinitialisation des paramètres va restaurer les paramètres par défaut. Cliquez sur
                                                réinitialiser pour continuer
                                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                        <form action="" method="post">
                                            <input type="submit" name="reset_configs" class="btn btn-primary" value="Réinitialiser">
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?= Flash::render() ?>

                        <div class="alert alert-info alert-dismissible fade show d-flex align-items-center" role="alert">
                            <span class="bi bi-info-circle flex-shrink-0 me-2" role="img" aria-label="Info:"></span>
                            <div>
                                <strong>N.B</strong> : Les paramètres influencent de très près le fonctionnement de l'application.
                                <strong> A manipuler avec le plus grand soin </strong>.
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        </div>

                        <?php if (!empty($configs)) : ?>
                            <table class="table table-border table-striped">
                                <thead class="">
                                    <tr>
                                        <th scope="col">Nom</th>
                                        <th scope="col">Description</th>
                                        <th scope="col">Valeur</th>
                                        <th scope="col">Par défaut</th>
                                        <!-- <th scope="col">Mis à jour</th> -->
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($configs as $config) : ?>
                                        <tr>
                                            <th scope="row"><?= $config->getCode() ?></th>
                                            <td class="text-break"><?= $config->getDescription() ?></td>
                                            <td class="text-break"><?= $config->getValue() ?></td>
                                            <td class="text-break"><?= $config->getDefaultValue() ?></td>
                                            <!-- <td><?= DateHelper::shortDate($config->getModified()) ?></td> -->
                                            <td>
                                                <button class="btn btn-primary btn-sm" title="Modifier la valeur du paramètre" type="button" data-bs-toggle="modal" data-bs-target="#updateConfig-<?= $config->getId() ?>">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>
                                            </td>
                                        </tr>

                                        <div class="modal fade" id="updateConfig-<?= $config->getId() ?>" tabindex="-1" aria-labelledby="updateConfigLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="updateConfigLabel"><i class="bi bi-calendar"></i> Modifier le
                                                            paramètre</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <form action="" method="post" class="g-3 needs-validation" novalidate>
                                                        <div class="modal-body">
                                                            <div class="mb-1">
                                                                <label for="cfname-<?= $config->getId() ?>" class="form-label">Nom du paramètre</label>
                                                                <input type="text" disabled class="form-control" id="cfname-<?= $config->getId() ?>" value="<?= $config->getCode() ?>">
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="cfdesc-<?= $config->getId() ?>" class="form-label">Description</label>
                                                                <textarea class="form-control" disabled id="cfdesc-<?= $config->getId() ?>" rows="3"><?= $config->getDescription() ?></textarea>
                                                            </div>

                                                            <div class="mb-1">
                                                                <label for="cfdvalue1-<?= $config->getId() ?>" class="form-label">Valeur par défaut</label>

                                                                <?php if ($config->getValueType() == 'bool') : ?>
                                                                    <br>
                                                                    <div class="form-check form-check-inline form-switch">
                                                                        <input class="form-check-input" role="switch" disabled <?= $config->getDefaultValue() == 'OUI' ? ' checked' : '' ?> type="radio" id="dyesval-<?= $config->getId() ?>">
                                                                        <label class="form-check-label" for="dyesval-<?= $config->getId() ?>">OUI</label>
                                                                    </div>
                                                                    <div class="form-check form-check-inline form-switch">
                                                                        <input class="form-check-input" role="switch" disabled <?= $config->getDefaultValue() == 'NON' ? ' checked' : '' ?> type="radio" id="noval1-<?= $config->getId() ?>">
                                                                        <label class="form-check-label" for="noval1-<?= $config->getId() ?>">NON</label>
                                                                    </div>
                                                                <?php else : ?>
                                                                    <input type="text" disabled class="form-control" id="cfdvalue1-<?= $config->getId() ?>" value="<?= $config->getDefaultValue() ?>">
                                                                <?php endif; ?>

                                                            </div>

                                                            <div class="mb-1">
                                                                <label for="cfvalue-<?= $config->getId() ?>" class="form-label">Valeur</label>
                                                                <?php if ($config->getValueType() == 'bool') : ?>
                                                                    <br>
                                                                    <div class="form-check form-check-inline form-switch">
                                                                        <input class="form-check-input" role="switch" <?= $config->getValue() == 'OUI' ? ' checked' : '' ?> type="radio" name="value" value="OUI" id="yesval-<?= $config->getId() ?>">
                                                                        <label class="form-check-label" for="yesval-<?= $config->getId() ?>">OUI</label>
                                                                    </div>
                                                                    <div class="form-check form-check-inline form-switch">
                                                                        <input class="form-check-input" role="switch" <?= $config->getValue() == 'NON' ? ' checked' : '' ?> type="radio" name="value" value="NON" id="noval-<?= $config->getId() ?>">
                                                                        <label class="form-check-label" for="noval-<?= $config->getId() ?>">NON</label>
                                                                    </div>
                                                                <?php else : ?>
                                                                    <input type="text" name="value" class="form-control" id="cfvalue-<?= $config->getId() ?>" value="<?= $config->getValue() ?>">
                                                                <?php endif; ?>
                                                            </div>

                                                            <input type="hidden" name="id" value="<?= $config->getId() ?>">
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                            <input type="submit" class="btn btn-primary" name="update_config" value="Enregistrer">
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                    <?php endforeach ?>
                                </tbody>
                            </table>
                        <?php else : ?>
                            <div class="alert alert-primary d-flex align-items-center" role="alert">
                                <span class="bi bi-info-circle flex-shrink-0 me-2" role="img" aria-label="Info:"></span>
                                <div>
                                    Aucun paramètre trouvé
                                </div>
                            </div>

                        <?php endif ?>
                    </div>

                </div>
            </div><!-- End Configs -->

        </div>

    </section>

</main>

<?php require_once dirname(__DIR__) . DS . 'Elements' . DS . 'footer.php'; ?>