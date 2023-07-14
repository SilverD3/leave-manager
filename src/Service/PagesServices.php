<?php

declare(strict_types=1);

/**
 * Leave manager : Simple app for contract and leave management.
 *
 * @copyright Copyright (c) Silevester D. (https://github.com/SilverD3)
 * @link      https://github.com/SilverD3/leave-manager Leave Manager Project
 * @since     1.0 (2022)
 */

namespace App\Service;

require_once dirname(dirname(__DIR__)) . DS . DS . 'autoload.php';

use Core\Database\ConnectionManager;

class PagesServices
{
    public function testDbConnection(): string
    {
        $connectionManager = new ConnectionManager();
        $connection = $connectionManager->getConnection();

        if ($connection) {
            return "<div class='alert alert-success'> Connexion to database <span class='fw-bold'>" . $connectionManager->getDatabase() . " </span> is established.</div>";
        } else {
            return "<div class='alert alert-danger'> Cannot connect to database. Reason : <span class='fw-bold'> " . $connectionManager->getError() . "</div>";
        }
    }
}
