<?php
declare(strict_types=1);

namespace App\Service;

require_once dirname(dirname(__DIR__)) . DS . DS . 'autoload.php';

use Core\Database\ConnectionManager;
use App\Entity\Contract;

/**
 * Contract Services
 */
class ContractsServices
{
	private $connectionManager;

    function __construct()
    {
        $this->connectionManager = new ConnectionManager();
    }

    public function getAll(string $status = 'all')
    {

    }

    /**
     * Count all contracts
     *
     * @param string $status Status to consider
     * @param int|null $employee_id Employee id
     * @return int Number of contracts
     */
    public function countAll(string $status = 'all', $employee_id = null): int
    {
        $count = 0;
        $join = '';

        $sql = "SELECT COUNT(*) AS count FROM contracts c WHERE c.etat = :etat";
        if ($status != 'all') {
            $sql .= " AND c.status = :status";
        }

        if ($employee_id != null) {
            $sql .= " AND c.employee_id = :employee_id";
        }

        try {
            $query = $this->connectionManager->getConnection()->prepare($sql);

            $query->bindValue(':etat', 1, \PDO::PARAM_BOOL);
            if ($status != 'all') {
                $query->bindValue(':status', $status, \PDO::PARAM_STR);
            }
            if ($employee_id != null) {
                $query->bindValue(':employee_id', $employee_id, \PDO::PARAM_INT);
            }

            $query->execute();
            
            $result = $query->fetch(\PDO::FETCH_ASSOC);

            $count = (int)$result['count'];
        } catch (\PDOException $e) {
            throw new \Exception("SQL Exception: " . $e->getMessage(), 1);
        }
        
        return $count;
    }
}