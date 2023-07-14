<?php

/**
 * Leave manager : Simple app for contract and leave management.
 *
 * @copyright Copyright (c) Silevester D. (https://github.com/SilverD3)
 * @link      https://github.com/SilverD3/leave-manager Leave Manager Project
 * @since     1.0 (2022)
 */

require_once dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'autoload.php';

use App\Controller\EmployeesController;
use Core\FlashMessages\Flash;

(new EmployeesController())->index();

require_once dirname(__DIR__) . DS . 'Elements' . DS . 'header.php';

?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Les employés</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Accueil</a></li>
                <li class="breadcrumb-item active">Employés</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
        <div class="row mt-2 mb-1">
            <div class="col-12">
                <a href="<?= VIEWS . 'Employees/add.php' ?>" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i>
                    Nouvel employé</a>
            </div>
        </div>

        <hr>

        <div class="row mt-1">
            <div class="col-12">

                <?= Flash::render() ?>

                <?php if (empty($employees)) : ?>

                    <div class="alert alert-primary d-flex align-items-center" role="alert">
                        <span class="bi bi-info-circle flex-shrink-0 me-2" role="img" aria-label="Info:"></span>
                        <div>
                            Aucun employé trouvé.
                        </div>
                    </div>

                <?php else : ?>
                    <div class="row">
                        <?php foreach ($employees as $employee) : ?>

                            <div class="col-sm-6 col-md-4 col-xxl-3">
                                <div class="card">

                                    <div class="filter">
                                        <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                            <li class="dropdown-header text-start">
                                                <h6>Options</h6>
                                            </li>

                                            <li><a class="dropdown-item" href="<?= VIEWS . 'Employees/view.php?id=' . $employee->getId() ?>"><i class="bi bi-eye"></i> Voir</a></li>
                                            <li><a class="dropdown-item" href="<?= VIEWS . 'Employees/update.php?id=' . $employee->getId() ?>"><i class="bi bi-pencil"></i> Editer</a></li>
                                            <li><a class="dropdown-item text-danger" href="#" onclick="deleteEmployee(<?= $employee->getId() ?>)"><i class="bi bi-trash"></i> Supprimer</a></li>
                                        </ul>
                                    </div>

                                    <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
                                        <img src="<?= IMAGES ?>user_icon.png" alt="User icon" class="rounded-circle w-50">
                                        <h2><?= $employee->getFirstName() . ' ' . $employee->getLastName() ?></h2>
                                        <!-- <hr class="w-100 m-1"> -->
                                        <h6><?= $employee->getRole()->getName() ?> </h6>
                                    </div>
                                </div>

                            </div>

                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

        </div>

    </section>

</main>

<?php require_once dirname(__DIR__) . DS . 'Elements' . DS . 'footer.php'; ?>

<script type="text/javascript">
    function deleteEmployee(id) {
        if (confirm("Voulez-vous vraiment supprimer cet employé")) {
            var xmlhttp = new XMLHttpRequest();
            var url = "<?= VIEWS . 'Employees/delete.php?ajax=1&id=' ?>" + id;

            xmlhttp.onreadystatechange = function() {
                if (xmlhttp.readyState == 4) {
                    if (xmlhttp.status == 200) {
                        location.reload();
                    } else {
                        alert("Erreur: " + (JSON.parse(xmlhttp.response)).message);
                    }
                }
            };

            xmlhttp.open("GET", url, true);
            xmlhttp.send();
        }
    }
</script>