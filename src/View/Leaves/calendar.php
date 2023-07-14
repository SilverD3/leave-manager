<?php

/**
 * Leave manager : Simple app for contract and leave management.
 *
 * @copyright Copyright (c) Silevester D. (https://github.com/SilverD3)
 * @link      https://github.com/SilverD3/leave-manager Leave Manager Project
 * @since     1.0 (2022)
 */

require_once dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'autoload.php';

use App\Controller\LeavesController;
use Core\FlashMessages\Flash;

(new LeavesController())->calendar();

require_once dirname(__DIR__) . DS . 'Elements' . DS . 'header.php';

?>

<link rel="stylesheet" href="<?= TEMPLATE_PATH ?>assets/vendor/fullcalendar/lib/main.css">

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Les congés planifiés</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Accueil</a></li>
                <li class="breadcrumb-item"><a href="<?= VIEWS . 'Leaves' ?>">Congés</a></li>
                <li class="breadcrumb-item active">Calendrier</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
        <div class="row mt-1">
            <div class="col-12">

                <div class="card recent-sales overflow-auto">
                    <div class="card-body mt-3">
                        <?= Flash::render() ?>

                        <div class="">
                            <div id='script-warning' class="d-none">
                                <div class="alert alert-danger fade show d-flex align-items-center my-3 py-1" id="delayAlert" role="alert">
                                    <span class="bi bi-exclamation-triangle flex-shrink-0 me-2" style="font-size: 25px;" role="img" aria-label="Warning:"></span>
                                    <div class="ms-md-2">
                                        Une erreur est survenue lors du chargement des données. Veuillez réessayer !
                                    </div>
                                </div>
                            </div>

                            <div id='loading' class="my-3">chargement des données...</div>

                            <div id='calendar'></div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

</main>

<?php require_once dirname(__DIR__) . DS . 'Elements' . DS . 'footer.php'; ?>

<script src='<?= TEMPLATE_PATH ?>assets/vendor/fullcalendar/lib/main.js'></script>
<script src='<?= TEMPLATE_PATH ?>assets/vendor/fullcalendar/lib/locales/fr.js'></script>

<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');

        var calendar = new FullCalendar.Calendar(calendarEl, {
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,listWeek'
            },
            initialDate: '<?= date('Y-m-d') ?>',
            editable: false,
            navLinks: false, // can click day/week names to navigate views
            dayMaxEvents: true, // allow "more" link when too many events
            events: {
                url: '<?= VIEWS . 'Leaves/getbyperiod.php' ?>',
                failure: function() {
                    document.getElementById('script-warning').classList.remove('d-none')
                }
            },
            eventClick: function(info) {
                info.jsEvent.preventDefault();

                if (info.event.url) {
                    window.open(info.event.url, "_blank");
                    return false;
                }
            },
            loading: function(bool) {
                document.getElementById('loading').style.display =
                    bool ? 'block' : 'none';
            }
        });

        calendar.setOption('locale', 'fr');

        calendar.render();

        /**
         * Get working day in select period via Ajax request
         */
        var xmlhttp = new XMLHttpRequest();
        var url = "<?= VIEWS . 'Leaves/getbusinesshours.php?' ?>";

        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4) {
                if (xmlhttp.status == 200) {
                    var response = JSON.parse(xmlhttp.responseText);
                    calendar.setOption('businessHours', {
                        daysOfWeek: response.businessDays,

                        startTime: response.workBeginAt,
                        endTime: response.workEndAt,
                    });
                } else {
                    alert("Une erreur est survenue lors du chargement des heures de travail");
                }
            }
        };

        xmlhttp.open("GET", url, true);
        xmlhttp.send();
    });
</script>