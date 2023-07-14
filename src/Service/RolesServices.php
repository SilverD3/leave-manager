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
use App\Entity\Role;

/**
 * Roles Services
 */
class RolesServices
{
    /**
     * @var ConnectionManager $connectionManager
     */
    private $connectionManager;

    /**
     * Initializes the Roles Services by setting the connection manager
     */
    function __construct()
    {
        $this->connectionManager = new ConnectionManager();
    }

    /**
     * Get All Roles
     * @return array<Role>  Array of Roles or empty array
     * @throw \Exception When error occurs
     */
    public function getAll()
    {
        $result = [];
        $roles = [];

        $sql = "SELECT * FROM roles";

        try {
            $query = $this->connectionManager->getConnection()->prepare($sql);
            $query->execute();
            $result = $query->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new \Exception("SQL Exception: " . $e->getMessage(), 1);
        }

        if (empty($result)) {
            return [];
        }

        foreach ($result as $row) {
            $role = new Role();
            $role->setId($row['id']);
            $role->setCode($row['code']);
            $role->setName($row['name']);

            $roles[] = $role;
        }

        return $roles;
    }
}
