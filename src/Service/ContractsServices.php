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
     * @return int Number of contracts
     */
    public function countAll(string $status = 'all'): int
    {
        $count = 0;
        $join = '';

        $sql = "SELECT COUNT(*) AS count FROM contracts c WHERE c.etat = ?";
        if ($status != 'all') {
            $sql .= " AND c.status = ?";
        }

        try {
            $query = $this->connectionManager->getConnection()->prepare($sql);

            if ($status == 'all') {
                $query->execute([1]);
            } else {
                $query->execute([1, $status]);
            }

            $result = $query->fetch(\PDO::FETCH_ASSOC);

            $count = (int)$result['count'];
        } catch (\PDOException $e) {
            throw new \Exception("SQL Exception: " . $e->getMessage(), 1);
        }
        
        return $count;
    }
}