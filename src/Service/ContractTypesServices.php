<?php
declare(strict_types=1);

namespace App\Service;

require_once dirname(dirname(__DIR__)) . DS . DS . 'autoload.php';

use Core\Database\ConnectionManager;
use App\Entity\ContractType;

/**
 * Contract Types Services
 */
class ContractTypesServices
{
    private $connectionManager;

    function __construct()
    {
        $this->connectionManager = new ConnectionManager();
    }
    
	/**
     * Count all contract types
     * 
     * @return int Number of contract types
     */
    public function countAll(): int
    {
        $count = 0;
        $join = '';

        $sql = "SELECT COUNT(*) AS count FROM contract_types ct WHERE ct.etat = ?";

        try {
            $query = $this->connectionManager->getConnection()->prepare($sql);

            $query->execute([1]);

            $result = $query->fetch(\PDO::FETCH_ASSOC);

            $count = (int)$result['count'];
        } catch (\PDOException $e) {
            throw new \Exception("SQL Exception: " . $e->getMessage(), 1);
        }
        
        return $count;
    }
}