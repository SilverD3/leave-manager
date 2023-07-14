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
use Core\FlashMessages\Flash;

(new AuthController())->login();

?>

<!DOCTYPE html>
<html lang="fr">

<head>
	<meta charset="utf-8">
	<meta content="width=device-width, initial-scale=1.0" name="viewport">

	<title><?= (new TitleHelper())->getTitle(); ?></title>
	<meta content="Simple Leave Manager Application" name="description">
	<meta content="Leaves, Manager, Contracts" name="keywords">

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

	<!-- Template Main CSS File -->
	<link href="<?= TEMPLATE_PATH ?>assets/css/style.css" rel="stylesheet">
</head>

<body>

	<main>
		<div class="container">

			<section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
				<div class="container">
					<div class="row justify-content-center">
						<div class="col-lg-5 col-md-6 d-flex flex-column align-items-center justify-content-center">

							<div class="d-flex justify-content-center py-4">
								<a href="/" class="logo d-flex align-items-center w-auto">
									<img src="https://getbootstrap.com/docs/5.1/assets/img/favicons/favicon-32x32.png" alt="">
									<span class="d-none d-lg-block">LeaveManager</span>
								</a>
							</div><!-- End Logo -->

							<div class="card mb-3">

								<div class="card-body">

									<div class="pt-4 pb-2">
										<h5 class="card-title text-center pb-0 fs-4">Connectez-vous à votre compte</h5>
										<p class="text-center small">Entrez votre nom d'utilisateur et votre mot de passe</p>
									</div>

									<?= Flash::render() ?>

									<form method="post" action="" class="row g-3 needs-validation" novalidate>

										<div class="col-12">
											<label for="yourUsername" class="form-label">Nom d'utilisateur</label>
											<input type="text" name="username" class="form-control" id="yourUsername" required>
											<div class="invalid-feedback">Veuillez rentrer votre nom d'utilisateur.</div>
										</div>

										<div class="col-12">
											<label for="yourPassword" class="form-label">Mot de passe</label>
											<input type="password" name="password" class="form-control" id="yourPassword" required>
											<div class="invalid-feedback">Veuillez rentrer votre mot de passe!</div>
										</div>

										<div class="col-12">
											<div class="form-check">
												<input class="form-check-input" type="checkbox" name="remember" value="true" id="rememberMe">
												<label class="form-check-label" for="rememberMe">Se souvenir de moi</label>
											</div>
										</div>
										<div class="col-12">
											<button class="btn btn-success w-100" type="submit" name="login">Se connecter</button>
										</div>
										<!-- <div class="col-12">
											<p class="small mb-0">Don't have account? <a href="/">Create an account</a></p>
										</div> -->
									</form>

								</div>
							</div>

							<div class="credits">
								&copy; Copyright <strong><span><a href="https://github.com/SilverD3" target="_blank">Silevester
											D.</a></span>, 2022</strong>. Tout droits reservés
							</div>

						</div>
					</div>
				</div>

			</section>

		</div>
	</main><!-- End #main -->

	<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

	<!-- Vendor JS Files -->
	<script src="<?= TEMPLATE_PATH ?>assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

	<!-- Template Main JS File -->
	<script src="<?= TEMPLATE_PATH ?>assets/js/main.js"></script>
</body>

</html>