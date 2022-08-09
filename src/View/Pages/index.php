<?php

require_once dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'autoload.php';

use App\View\Helpers\TitleHelper;

require_once CONTROLLER_PATH . 'PagesController.php';

use App\Controller\PagesController;

(new PagesController())->index();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?=(new TitleHelper())->getTitle();?>
    </title>

    <link rel="icon" href="https://getbootstrap.com/docs/5.1/assets/img/favicons/favicon-32x32.png" sizes="32x32" type="image/png">
    <link rel="apple-touch-icon" href="https://getbootstrap.com/docs/5.1/assets/img/favicons/apple-touch-icon.png" sizes="180x180">
    <link rel="icon" href="https://getbootstrap.com/docs/5.1/assets/img/favicons/favicon-16x16.png" sizes="16x16" type="image/png">

    <link rel="stylesheet" href="<?= TEMPLATE_PATH ?>assets/vendor/bootstrap/css/bootstrap.min.css">
</head>
<body>
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

    <script src="<?= TEMPLATE_PATH ?>assets/vendor/bootstrap/js/bootstrap.min.js"></script>
</body>
</html>