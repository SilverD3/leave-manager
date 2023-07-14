<?php

declare(strict_types=1);

/**
 * Leave manager : Simple app for contract and leave management.
 *
 * @copyright Copyright (c) Silevester D. (https://github.com/SilverD3)
 * @link      https://github.com/SilverD3/leave-manager Leave Manager Project
 * @since     1.0 (2022)
 */

namespace App\Entity;

/**
 * Contract Model's Entity Class
 */
class ContractModel
{
    private $id;
    private $contract_type_id;
    private $name;
    private $content;
    private $is_current;
    private $status;
    private $created;
    private $modified;
    private $etat;

    private $contractType;

    /**
     * Validates Contract Model
     * Check if all required fields are provided
     * 
     * @return array Array of errors
     */
    public function validation(): array
    {
        $errors = [];

        if (empty($this->name)) {
            $errors[] = "Le nom du modèle de contrat est requis";
        }

        if (empty($this->content)) {
            $errors[] = "Le contenu du modèle de contrat est requis";
        }

        if (strlen($this->content) > 65000) {
            $errors[] = "Le contenu du modèle de contrat dépasse le nombre de caractères autorisés ";
        }

        if (empty($this->contract_type_id)) {
            $errors[] = "Le type de contrat est requis";
        }

        return $errors;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getContractTypeId()
    {
        return $this->contract_type_id;
    }

    /**
     * @param mixed $contract_type_id
     *
     * @return self
     */
    public function setContractTypeId($contract_type_id)
    {
        $this->contract_type_id = $contract_type_id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     *
     * @return self
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getIsCurrent()
    {
        return $this->is_current;
    }

    /**
     * @param mixed $is_current
     *
     * @return self
     */
    public function setIsCurrent($is_current)
    {
        $this->is_current = $is_current;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     *
     * @return self
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param mixed $created
     *
     * @return self
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getModified()
    {
        return $this->modified;
    }

    /**
     * @param mixed $modified
     *
     * @return self
     */
    public function setModified($modified)
    {
        $this->modified = $modified;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEtat()
    {
        return $this->etat;
    }

    /**
     * @param mixed $etat
     *
     * @return self
     */
    public function setEtat($etat)
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * @return ContractType|null
     */
    public function getContractType()
    {
        return $this->contractType;
    }

    /**
     * @param mixed $etat
     *
     * @return self
     */
    public function setContractType(ContractType $contractType)
    {
        $this->contractType = $contractType;

        return $this;
    }
}
