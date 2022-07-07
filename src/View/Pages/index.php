<?php 
require_once CONTROLLER_PATH . 'PagesController.php';

use App\Controller\PagesController;

(new PagesController())->index();

require_once dirname(__DIR__) . DS . 'Elements' . DS . 'header.php';

?>

<div class="container-sm justify-content-center" style="position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%);"> 
    <div class="row"> 
        <div class="col-md-10 m-auto">
            <div class="card text-center">
                <div class="bg-primary text-white card-header p-2">
                    <h1>Readme !!!</h1>
                </div>
                <div class="card-body">
                    <h5 class="card-title m-2">Leave Manager App Skeleton</h5>
                    <p class="card-text mt-3">
                        The basic structure based on the MVC pattern to facilitate the development of small applications with PHP from Scratch. </br>
                        <a href="https://getbootstrap.com/docs/5.1" class="alert-link link-primary" style="text-decoration: none" target="_blank"> 
                            <img src="https://getbootstrap.com/docs/5.1/assets/img/favicons/favicon-16x16.png" alt=""> 
                            Bootstrap 5.1 
                        </a> 
                        is already included.

                        <?= $dbconnection ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . DS . 'Elements' . DS . 'footer.php'; ?>