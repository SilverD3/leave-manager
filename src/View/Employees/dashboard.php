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
use App\View\Helpers\DateHelper;
use App\View\Helpers\UtilsHelper;
use Core\FlashMessages\Flash;

(new EmployeesController())->dashboard();

require_once dirname(__DIR__) . DS . 'Elements' . DS . 'header.php';

?>

<main id="main" class="main">
	<div class="pagetitle">
		<h1>Tableau de bord</h1>
		<nav>
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="/">Accueil</a></li>
				<li class="breadcrumb-item active">Tableau de bord</li>
			</ol>
		</nav>
	</div><!-- End Page Title -->

	<section class="section dashboard">
		<div class="row">

			<div class="col-lg-12">
				<div class="row">
					<div class="col-12 mt-2">
						<?= Flash::render() ?>
					</div>

					<!-- Employees Card -->
					<div class="col-xxl-3 col-md-6">
						<div class="card info-card sales-card">
							<div class="card-body">
								<h5 class="card-title">Employés</h5>

								<div class="d-flex align-items-center">
									<div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
										<i class="bi bi-people"></i>
									</div>
									<div class="ps-3">
										<h6><?= $stats['nb_employees'] ?></h6>
										<span class="text-danger small pt-1 fw-bold"> <?= $stats['nb_current_leaves'] ?> en congés</span>
									</div>
								</div>
							</div>

						</div>
					</div><!-- End Employees Card -->

					<!-- Permission Request Card -->
					<div class="col-xxl-3 col-md-6">
						<div class="card info-card revenue-card">
							<div class="card-body">
								<h5 class="card-title">Demandes de permission</h5>

								<div class="d-flex align-items-center">
									<div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
										<i class="bi bi-person-dash"></i>
									</div>
									<div class="ps-3">
										<h6><?= $stats['nb_permission_requests'] ?></h6>
										<span class="text-success small pt-1 fw-bold"> <?= $stats['nb_approved_permission_requests'] ?>
											approuvées</span> |
										<span class="text-danger small pt-1 fw-bold"> <?= $stats['nb_rejected_permission_requests'] ?>
											rejétées</span>

									</div>
								</div>
							</div>

						</div>
					</div><!-- End Permission Requests Card -->

					<!-- Contracts Card -->
					<div class="col-xxl-3 col-md-6">

						<div class="card info-card customers-card">
							<div class="card-body">
								<h5 class="card-title">Contrats</h5>

								<div class="d-flex align-items-center">
									<div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
										<i class="bi bi-files-alt"></i>
									</div>
									<div class="ps-3">
										<h6><?= $stats['nb_contracts'] ?></h6>
										<span class="text-success small pt-1 fw-bold"> <?= $stats['nb_active_contracts'] ?> actifs </span> |
										<span class="text-danger small pt-1 fw-bold"> <?= $stats['nb_terminated_contracts'] ?>
											resiliés</span>
									</div>
								</div>

							</div>
						</div>

					</div><!-- End Contracts Card -->

					<?php if ($auth_user->getRole()->getCode() == 'ADM') : ?>
						<!-- Contract Types Card -->
						<div class="col-xxl-3 col-md-6">
							<div class="card info-card sales-card">
								<div class="card-body">
									<h5 class="card-title">Types de contrat</h5>

									<div class="d-flex align-items-center">
										<div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
											<i class="bi bi-journal-code"></i>
										</div>
										<div class="ps-3">
											<h6><?= $stats['nb_contract_types'] ?></h6>
										</div>
									</div>

								</div>
							</div>

						</div><!-- End Contract Types Card -->

					<?php endif; ?>

					<!-- Recent permission request -->
					<div class="col-12">
						<div class="card recent-sales overflow-auto">

							<div class="card-body">
								<h5 class="card-title">Récentes demandes de permission</h5>

								<?php if (!empty($recent_permission_requests)) : ?>
									<table class="table table-border datatable">
										<thead>
											<tr>
												<th scope="col">ID</th>
												<th scope="col">Employee</th>
												<th scope="col">Motif</th>
												<th scope="col">Début</th>
												<th scope="col">Fin</th>
												<th scope="col">Status</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach ($recent_permission_requests as $permissionRequest) : ?>
												<tr>
													<th scope="row"><a href="<?= VIEWS . 'PermissionRequests' . DS . 'view.php?id=' . $permissionRequest->getId() ?>"><?= $permissionRequest->getId() ?></a>
													</th>
													<td>
														<?= $permissionRequest->getEmployee()->getFirstName() . ' ' . $permissionRequest->getEmployee()->getLastName() ?>
													</td>
													<td><?= UtilsHelper::troncate($permissionRequest->getReason(), 40) ?></td>
													<td><?= DateHelper::shortDate($permissionRequest->getStartDate()) ?></td>
													<td><?= DateHelper::shortDate($permissionRequest->getEndDate()) ?></td>
													<td>
														<?php if ($permissionRequest->getStatus() == 'pending') : ?>
															<span class="badge text-bg-primary">En attente</span>
														<?php elseif ($permissionRequest->getStatus() == 'approved') : ?>
															<span class="badge text-bg-success">Approuvée</span>
														<?php elseif ($permissionRequest->getStatus() == 'disapproved') : ?>
															<span class="badge text-bg-danger">Rejetée</span>
														<?php else : ?>
															<span class="badge text-bg-info"> <?= $permissionRequest->getStatus() ?></span>
														<?php endif; ?>
													</td>
												</tr>
											<?php endforeach ?>
										</tbody>
									</table>
								<?php else : ?>
									<div class="alert alert-primary d-flex align-items-center" role="alert">
										<span class="bi bi-info-circle flex-shrink-0 me-2" role="img" aria-label="Info:"></span>
										<div>
											Aucune demande de permission récente trouvée
										</div>
									</div>

								<?php endif ?>
							</div>

						</div>
					</div><!-- End Recent Permission Requests -->

				</div>
			</div>

		</div>
	</section>

</main><!-- End #main -->


<?php require_once dirname(__DIR__) . DS . 'Elements' . DS . 'footer.php'; ?>