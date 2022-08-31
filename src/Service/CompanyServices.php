<?php
declare(strict_types=1);

namespace App\Service;

require_once dirname(dirname(__DIR__)) . DS . DS . 'autoload.php';

use Core\Database\ConnectionManager;
use App\Entity\Company;
use Core\FlashMessages\Flash;
use Core\Utils\Session;

/**
 * Comapny Services
 */
class CompanyServices
{
    private $connectionManager;

    function __construct()
    {
        $this->connectionManager = new ConnectionManager();
    }

    /**
     * Get company infos
     * 
     * @return Company|null Return the company or null if not found
     */
    public function getCompany(): ?Company
    {
        $sql = "SELECT *  FROM company";

        try {
            $query = $this->connectionManager->getConnection()->prepare($sql);

            $query->execute();

            $result = $query->fetch(\PDO::FETCH_ASSOC);

            if (empty($result)) {
                return new Company();
            }

            $company = new Company();
            $company->setId($result['id']);
            $company->setName($result['name']);
            $company->setDirectorName($result['director_name']);
            $company->setAddress($result['address']);
            $company->setTel1($result['tel1']);
            $company->setTel2($result['tel2']);
            $company->setEmail($result['email']);
            $company->setLogo($result['logo']);
            $company->setAbout($result['about']);
            $company->setCreated($result['created']);
            $company->setModified($result['modified']);
            $company->setModifiedBy($result['modified_by']);


            return $company;
        } catch (\PDOException $e) {
            throw new \Exception("SQL Exception: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    /**
     * Add or update company informations
     *
     * @param array|Company $company Company informations
     * @return boolean Return true if success, false otherwise.
     */
    public function update(array|Company $company): bool
    {
        if (is_array($company)) {
            $company = $this->toEntity($company);
        }

        $existedCompany = $this->getCompany();

        if (empty($existedCompany->getId())) {
            $company->setCreated(date('Y-m-d H:i:s'));
        }

        $company->setModified(date('Y-m-d H:i:s'));

        $errors = $company->validation();

		if (!empty($errors)) {
			foreach ($errors as $error) {
				Flash::error($error);
			}

			Session::write('__formdata__', json_encode($_POST));

			return false;
		}

        if (empty($existedCompany->getId())) {
            $sql = "INSERT INTO company(name, director_name, address, tel1, tel2, email, logo, about, created, modified, modified_by) VALUES(?,?,?,?,?,?,?,?,?,?,?)";
        } else {
            $sql = "UPDATE company SET name = ?, director_name = ?, address = ?, tel1 = ?, tel2 = ?, email = ?, logo = ?, about = ?, modified = ?, modified_by = ? WHERE id = ?";
        }

        try {
			$query = $this->connectionManager->getConnection()->prepare($sql);

            if (empty($existedCompany->getId())) {
                $query->bindValue(1, $company->getName(), \PDO::PARAM_STR);
                $query->bindValue(2, $company->getDirectorName(), \PDO::PARAM_STR);
                $query->bindValue(3, $company->getAddress(), \PDO::PARAM_STR);
                $query->bindValue(4, $company->getTel1(), \PDO::PARAM_STR);
                $query->bindValue(5, $company->getTel2(), \PDO::PARAM_STR);
                $query->bindValue(6, $company->getEmail(), \PDO::PARAM_STR);
                $query->bindValue(7, $company->getLogo(), \PDO::PARAM_STR);
                $query->bindValue(8, $company->getAbout(), \PDO::PARAM_STR);
                $query->bindValue(9, $company->getCreated(), \PDO::PARAM_STR);
                $query->bindValue(10, $company->getModified(), \PDO::PARAM_STR);
                $query->bindValue(11, $company->getModifiedBy(), \PDO::PARAM_STR);
            } else {
                $query->bindValue(1, $company->getName(), \PDO::PARAM_STR);
                $query->bindValue(2, $company->getDirectorName(), \PDO::PARAM_STR);
                $query->bindValue(3, $company->getAddress(), \PDO::PARAM_STR);
                $query->bindValue(4, $company->getTel1(), \PDO::PARAM_STR);
                $query->bindValue(5, $company->getTel2(), \PDO::PARAM_STR);
                $query->bindValue(6, $company->getEmail(), \PDO::PARAM_STR);
                $query->bindValue(7, $company->getLogo(), \PDO::PARAM_STR);
                $query->bindValue(8, $company->getAbout(), \PDO::PARAM_STR);
                $query->bindValue(9, $company->getModified(), \PDO::PARAM_STR);
                $query->bindValue(10, $company->getModifiedBy(), \PDO::PARAM_STR);
                $query->bindValue(11, $existedCompany->getId(), \PDO::PARAM_INT);
            }
            

            $executed = $query->execute();

            return $executed;
        } catch (\PDOException $e) {
			throw new \Exception("SQL Exception: " . $e->getMessage(), 1);
		}
    }

    /**
     * Parse array to Company object
     *
     * @param array $data Data to parse
     * @return Company|null Returns parsed object or null
     */
    public function toEntity(array $data): ?Company
    {
        $id = !empty($data['id']) ? (int)$data['id'] : null;
        $name = !empty($data['name']) ? $data['name'] : null;
        $director_name = !empty($data['director_name']) ? $data['director_name'] : null;
        $address = !empty($data['address']) ? $data['address'] : null;
        $tel1 = !empty($data['tel1']) ? $data['tel1'] : null;
        $tel2 = !empty($data['tel2']) ? $data['tel2'] : null;
        $email = !empty($data['email']) ? $data['email'] : null;
        $logo = !empty($data['logo']) ? $data['logo'] : null;
        $about = !empty($data['about']) ? $data['about'] : null;
        $modified_by = !empty($data['modified_by']) ? $data['modified_by'] : null;
        $created = !empty($data['created']) ? $data['created'] : null;
        $modified = !empty($data['modified']) ? $data['modified'] : null;

        $company = new Company();
        $company->setId($id);
        $company->setName($name);
        $company->setDirectorName($director_name);
        $company->setAddress($address);
        $company->setTel1($tel1);
        $company->setTel2($tel2);
        $company->setEmail($email);
        $company->setLogo($logo);
        $company->setAbout($about);
        $company->setCreated($created);
        $company->setModified($modified);
        $company->setModifiedBy($modified_by);

        return $company;
    }

}