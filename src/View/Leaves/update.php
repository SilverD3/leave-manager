<?php 
require_once dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'autoload.php';

use App\Controller\LeavesController;
use Core\FlashMessages\Flash;

(new LeavesController())->update();

require_once dirname(__DIR__) . DS . 'Elements' . DS . 'header.php';

?>

<main id="main" class="main">
	<div class="pagetitle">
		<h1>Modifier le congé</h1>
		<nav>
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Accueil</a></li>
				<li class="breadcrumb-item"><a href="<?= VIEWS . 'Leaves' ?>">Congés</a></li>
				<li class="breadcrumb-item active">Modifier</li>
			</ol>
		</nav>
	</div><!-- End Page Title -->

    <section class="section dashboard">
        <div class="row">
            <div class="col-md-12 col-xl-12 col-xxl-10">
                <div class="card">
                    <div class="card-body pt-2">

                        <?= Flash::render() ?>

                        <form class="row g-3 needs-validation" name="editLeaveForm" action="" method="post" novalidate>
                            
                            <div class="py-1"><h4 class="card-title py-0">Remplissez les champs ci-après pour modifier le congé</h4></div>

                            <div class="col-sm-8 col-md-9">
                                <label for="lvid" class="form-label">Employé <span class="text-danger">*</span></label>
                                <select class="form-control" id="lvid" disabled>
                                    <option value=""><?= $leave->getEmployee()->getFirstName() . ' ' . $leave->getEmployee()->getLastName() ?></option>
                                </select>
                                <div class="invalid-feedback">Veuillez choisir un employé.</div>
                            </div>

                            <div class="col-sm-4 col-md-3">
                                <label for="lvyear" class="form-label"> Année </label>
                                <input type="number" class="form-control" disabled id="lvyear" value="<?= $leave->getYear() ?>">
                                <div class="invalid-feedback">Veuillez renseigner l'année.</div>
                            </div>

                            <div class="alert alert-info d-none" id="spentDaysInfo" role="alert">
                                Nombre de jours de congé : <span class="fw-bold" id="nbLeaveDays"> <?= $leave_nb_days ?></span> ; &nbsp;
                                Nombre de jours restant : <span class="fw-bold" id="nbSpentDays"></span>
                            </div>

                            <div class="col-sm-6">
                                <label for="lvstartdate" class="form-label">Date de départ <span class="text-danger">*</span></label>
                                <input type="date" class="form-control leavePeriod" name="start_date" id="lvstartdate" value="<?= isset($form_data['start_date']) ? $form_data['start_date'] : date('Y-m-d', strtotime($leave->getStartDate())) ?>" required>
                                <div class="invalid-feedback">Veuillez renseigner la date de départ en congé.</div>
                            </div>
                            
                            <div class="col-sm-6">
                                <label for="lvenddate" class="form-label"> Date de retour <span class="text-danger">*</span></label>
                                <input type="date" class="form-control leavePeriod" name="end_date" id="lvenddate" value="<?= isset($form_data['end_date']) ? $form_data['end_date'] : date('Y-m-d', strtotime($leave->getEndDate())) ?>" required>
                                <div class="invalid-feedback">Veuillez renseigner la date de retour du congé.</div>
                            </div>

                            <div class="alert alert-info d-none" id="workingDaysInfo" role="alert">
                                La période choisie compte <span class="fw-bold" id="nbWorkingDays"></span> 
                            </div>

                            <!-- Leave Period Feedback -->
                            <div class="alert alert-danger d-none" id="leavePeriodFeedback" role="alert"> </div>

                            <div class="mb-1">
                                <label for="lvnote" class="form-label"> Notes </label>
                                <textarea name="note" id="lvnote" placeholder="Observations, remarques, mémo, ..." class="form-control" rows="4"><?= isset($form_data['note']) ? $form_data['note'] : $leave->getNote() ?></textarea>
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
                                <input type="submit" class="btn btn-primary" name="update_leave" value="Enregistrer">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require_once dirname(__DIR__) . DS . 'Elements' . DS . 'footer.php'; ?>

<script type="text/javascript">

var leaveNbDays = parseInt('<?= $leave_nb_days ?>');
var leaveSpentDays = parseInt('<?= $leave->getDays() ?>');

getNbSpentDays(<?= $leave->getEmployeeId() ?>, <?= $leave->getYear() ?>);

document.querySelector('#lvstartdate').addEventListener('change', handlePeriodChange);
document.querySelector('#lvenddate').addEventListener('change', handlePeriodChange);

function handlePeriodChange(event)
{
    var start_date = document.forms.editLeaveForm.elements.start_date.value;
    var end_date = document.forms.editLeaveForm.elements.end_date.value;
    var year = <?= $leave->getYear() ?>;

    var workingDaysInfo = document.getElementById('workingDaysInfo');
    var leavePeriodFeedback = document.getElementById('leavePeriodFeedback');

    if (start_date.length > 0 && end_date.length > 0) {
        
        var startDate = Date.parse(start_date);
        var endDate = Date.parse(end_date);

        if (startDate > endDate) {
            workingDaysInfo.classList.add('d-none');
            leavePeriodFeedback.innerText = "La date de retour du congé ne peut être supérieure à la date de départ";
            leavePeriodFeedback.classList.remove('d-none');

            return;
        } else {
            leavePeriodFeedback.classList.add('d-none');
        }

        getNbWorkingDays(start_date, end_date, year);
    }
}

function fillNbWorkingDays(nb_working_days)
{
    var workingDaysInfoEl = document.getElementById('workingDaysInfo');
    var nbWorkingDaysEl = document.getElementById('nbWorkingDays');

    var workingDaysText = '';
    if (nb_working_days > 1) {
        workingDaysText = nb_working_days + ' jours ouvrables';
    } else {
        workingDaysText = nb_working_days + ' jour ouvrable';
    }

    nbWorkingDaysEl.innerText = workingDaysText;

    workingDaysInfoEl.classList.remove('d-none');
}

function fillNbSpentDays(nb_spent_days)
{
    var spentDaysInfoEl = document.getElementById('spentDaysInfo');
    var nbSpentDaysEl = document.getElementById('nbSpentDays');

    if (nb_spent_days < leaveNbDays) {
        nbSpentDaysEl.innerText = leaveNbDays - nb_spent_days;
    } else {
        nbSpentDaysEl.innerText = 0;
    }

    spentDaysInfoEl.classList.remove('d-none');
}

/**
 * Get working day in select period via Ajax request
 */
function getNbWorkingDays(dateFrom, dateTo, year)
{
    var nb_working_days = 0;
    var xmlhttp = new XMLHttpRequest();
    var url = "<?= VIEWS . 'Leaves/getworkingdays.php?' ?>from=" + dateFrom + "&to=" + dateTo + "&year=" + year;

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4){
            if(xmlhttp.status == 200) {
                var response = JSON.parse(xmlhttp.responseText);
                if (response.status == 'success') {
                    fillNbWorkingDays(response.nb_working_days);
                } else {
                    alert("Erreur: " + response.message);
                }
            }else {
                alert("Erreur: " + (JSON.parse(xmlhttp.response)).message);
            }
        } 
    };
    
    xmlhttp.open("GET", url, true);
    xmlhttp.send();
}

/**
 * Get spent day in select period via Ajax request
 */
function getNbSpentDays(employeeId, year)
{
    var nb_spent_days = 0;
    var xmlhttp = new XMLHttpRequest();
    var url = "<?= VIEWS . 'Leaves/getspentdays.php?' ?>eid=" + employeeId + "&year=" + year;

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4){
            if(xmlhttp.status == 200) {
                var response = JSON.parse(xmlhttp.responseText);
                if (response.status == 'success') {
                    fillNbSpentDays(response.nb_spent_days - leaveSpentDays);
                } else {
                    alert("Erreur: " + response.message);
                }
            }else {
                alert("Erreur: " + (JSON.parse(xmlhttp.response)).message);
            }
        } 
    };
    
    xmlhttp.open("GET", url, true);
    xmlhttp.send();
}

</script>