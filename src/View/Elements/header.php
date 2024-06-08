<?php

/**
 * Leave manager : Simple app for contract and leave management.
 *
 * @copyright Copyright (c) Silevester D. (https://github.com/SilverD3)
 * @link      https://github.com/SilverD3/leave-manager Leave Manager Project
 * @since     1.0 (2022)
 */

require_once dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'autoload.php';

use App\View\Helpers\TitleHelper;
use App\Controller\AuthController;

AuthController::require_auth();

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title><?= (new TitleHelper())->getTitle(); ?></title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link rel="icon" href="https://getbootstrap.com/docs/5.1/assets/img/favicons/favicon-32x32.png" sizes="32x32" type="image/png">
    <link rel="apple-touch-icon" href="https://getbootstrap.com/docs/5.1/assets/img/favicons/apple-touch-icon.png" sizes="180x180">
    <link rel="icon" href="https://getbootstrap.com/docs/5.1/assets/img/favicons/favicon-16x16.png" sizes="16x16" type="image/png">

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="<?= TEMPLATE_PATH ?>assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= TEMPLATE_PATH ?>assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= TEMPLATE_PATH ?>assets/vendor/simple-datatables/style.css" rel="stylesheet">

    <!-- Template Main CSS File -->
    <link href="<?= TEMPLATE_PATH ?>assets/css/style.css" rel="stylesheet">
    <link href="<?= ASSETS ?>css/override.css" rel="stylesheet">

</head>

<body>

    <!-- ======= Header ======= -->
    <header id="header" class="header fixed-top d-flex align-items-center">

        <div class="d-flex align-items-center justify-content-between">
            <a href="<?= BASE_URL ?>" class="logo d-flex align-items-center">
                <img src="<?= TEMPLATE_PATH ?>assets/img/logo.png" alt="">
                <span class="d-none d-lg-block">LeaveManager</span>
            </a>
            <i class="bi bi-list toggle-sidebar-btn"></i>
        </div><!-- End Logo -->

        <div class="search-bar">
            <form class="search-form d-flex align-items-center" method="POST" action="#">
                <input type="text" name="query" placeholder="Rechercher" title="Entrez les mots clés">
                <button type="submit" title="Rechercher"><i class="bi bi-search"></i></button>
            </form>
        </div><!-- End Search Bar -->

        <nav class="header-nav ms-auto">
            <ul class="d-flex align-items-center">

                <li class="nav-item d-block d-lg-none">
                    <a class="nav-link nav-icon search-bar-toggle " href="#">
                        <i class="bi bi-search"></i>
                    </a>
                </li><!-- End Search Icon-->

                <li class="nav-item dropdown pe-3">

                    <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
                        <img src="<?= IMAGES ?>user_icon.png" alt="Profile" class="rounded-circle">
                        <span class="d-none d-md-block dropdown-toggle ps-2"><?= $auth_user->getUsername() ?></span>
                    </a><!-- End Profile Iamge Icon -->

                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                        <li class="dropdown-header">
                            <h6><?= $auth_user->getFirstName() . ' ' . $auth_user->getLastName() ?></h6>
                            <span><?= $auth_user->getRole()->getName() ?></span>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="<?= VIEWS . 'Employees/profile.php' ?>">
                                <i class="bi bi-person"></i>
                                <span>Mon profile</span>
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <li>
                            <a class="dropdown-item d-flex align-items-center" id="logoutBtn" href="#">
                                <i class="bi bi-box-arrow-right"></i>
                                <span>Se deconnecter</span>
                            </a>
                        </li>

                    </ul><!-- End Profile Dropdown Items -->
                </li><!-- End Profile Nav -->

            </ul>
        </nav><!-- End Icons Navigation -->

    </header><!-- End Header -->

    <!-- ======= Sidebar ======= -->
    <aside id="sidebar" class="sidebar">

        <ul class="sidebar-nav" id="sidebar-nav">

            <li class="nav-item">
                <a class="nav-link <?= (isset($_SESSION['page_title']) && $_SESSION['page_title'] == 'Tableau de bord') ? '' : 'collapsed' ?>" href="<?= BASE_URL ?>">
                    <i class="bi bi-grid"></i>
                    <span>Tableau de bord</span>
                </a>
            </li><!-- End Dashboard Nav -->

            <li class="nav-heading">Application</li>

            <?php if ($auth_user->getRole()->getCode() == 'ADM') : ?>
                <li class="nav-item">
                    <a class="nav-link <?= (isset($_SESSION['page_title']) && $_SESSION['page_title'] == 'Utilisateurs') ? '' : 'collapsed' ?>" href="<?= VIEWS . 'Employees' ?>">
                        <i class="bi bi-people"></i>
                        <span>Utilisateurs</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if ($auth_user->getRole()->getCode() == 'ADM' || $auth_user->getRole()->getCode() == 'EMP') : ?>
                <li class="nav-item">
                    <a class="nav-link <?= (isset($_SESSION['page_title']) && $_SESSION['page_title'] == 'Contrats') ? '' : 'collapsed' ?>" href="<?= VIEWS . 'Contracts' ?>">
                        <i class="bi bi-files-alt"></i>
                        <span>Contrats</span>
                    </a>
                </li>
            <?php endif; ?>

            <li class="nav-item">
                <a class="nav-link <?= (isset($_SESSION['page_title']) && $_SESSION['page_title'] == 'Demandes de permission') ? '' : 'collapsed' ?>" href="<?= VIEWS . 'PermissionRequests' ?>">
                    <i class="bi bi-person-dash"></i>
                    <span>Demandes de permission</span>
                </a>
            </li>

            <?php if ($auth_user->getRole()->getCode() == 'ADM' || $auth_user->getRole()->getCode() == 'EMP') : ?>
                <li class="nav-item">
                    <a class="nav-link <?= (isset($_SESSION['page_title']) && $_SESSION['page_title'] == 'Congés') ? '' : 'collapsed' ?>" href="<?= VIEWS . 'Leaves' ?>">
                        <i class="bi bi-emoji-sunglasses"></i>
                        <span>Congés</span>
                    </a>
                </li>
            <?php endif; ?>

            <li class="nav-heading">Stages</li>

            <?php if ($auth_user->getRole()->getCode() == 'ADM' || $auth_user->getRole()->getCode() == 'EMP') : ?>
                <li class="nav-item">
                    <a class="nav-link <?= (isset($_SESSION['page_title']) && $_SESSION['page_title'] == 'Stages') ? '' : 'collapsed' ?>" href="<?= VIEWS . 'Internships' ?>">
                        <i class="bi bi-person-bounding-box"></i>
                        <span>Stages</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if ($auth_user->getRole()->getCode() == 'INT') : ?>
                <li class="nav-item">
                    <a class="nav-link <?= (isset($_SESSION['page_title']) && $_SESSION['page_title'] == 'Mon stage') ? '' : 'collapsed' ?>" href="<?= VIEWS . 'Internships/my.php' ?>">
                        <i class="bi bi-person-bounding-box"></i>
                        <span>Mon stage</span>
                    </a>
                </li>
            <?php endif; ?>

            <li class="nav-heading">Configurations</li>

            <li class="nav-item">
                <a class="nav-link <?= (isset($_SESSION['page_title']) && $_SESSION['page_title'] == 'Entreprise') ? '' : 'collapsed' ?>" href="<?= VIEWS . 'Company' ?>">
                    <i class="bi bi-building"></i>
                    <span>Entreprise</span>
                </a>
            </li>

            <?php if ($auth_user->getRole()->getCode() == 'ADM') : ?>
                <li class="nav-item">
                    <a class="nav-link <?= (isset($_SESSION['page_title']) && $_SESSION['page_title'] == 'Paramètres') ? '' : 'collapsed' ?>" href="<?= VIEWS . 'Configs' ?>">
                        <i class="bi bi-gear-wide-connected"></i>
                        <span>Paramètres</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?= (isset($_SESSION['page_title']) && $_SESSION['page_title'] == 'Types de contrat') ? '' : 'collapsed' ?>" href="<?= VIEWS . 'ContractTypes' ?>">
                        <i class="bi bi-journal-code"></i>
                        <span>Types de contrat</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?= (isset($_SESSION['page_title']) && $_SESSION['page_title'] == 'Modèles de contrat') ? '' : 'collapsed' ?>" href="<?= VIEWS . 'ContractModels' ?>">
                        <i class="bi bi-code-square"></i>
                        <span>Modèles de contrat</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?= (isset($_SESSION['page_title']) && $_SESSION['page_title'] == 'Types de document de stage') ? '' : 'collapsed' ?>" href="<?= VIEWS . 'InternshipDocumentTypes' ?>">
                        <i class="bi bi-file-earmark-person"></i>
                        <span>Documents de stage</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= (isset($_SESSION['page_title']) && $_SESSION['page_title'] == 'Types de stage') ? '' : 'collapsed' ?>" href="<?= VIEWS . 'InternshipTypes' ?>">
                        <i class="bi bi-tags"></i>
                        <span>Types de stage</span>
                    </a>
                </li>
            <?php endif; ?>

        </ul>

    </aside><!-- End Sidebar-->