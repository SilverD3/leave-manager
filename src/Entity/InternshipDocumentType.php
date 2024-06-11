<?php

declare(strict_types=1);

/**
 * Leave manager : Simple app for contract and leave management.
 *
 * @copyright Copyright (c) Silevester D. (https://github.com/SilverD3)
 * @link      https://github.com/SilverD3/leave-manager Leave Manager Project
 * @since     2.0 (2024)
 */

namespace App\Entity;

/**
 * Internship Document Type's Entity Class
 */
class InternshipDocumentType
{
    private $id;
    private $code;
    private $description;
    private bool $multipe;
    private bool $required;
    private $created;
    private $etat;

    /**
     * Validates Internship Type
     * Check if all required fields are provided
     * 
     * @return array Array of errors
     */
    public function validation(): array
    {
        $errors = [];

        if (empty($this->code)) {
            $errors[] = "Le nom du type de document de stage est requis";
        }

        return $errors;
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string|null $code
     *
     * @return self
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     *
     * @return self
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param string|null $created
     *
     * @return self
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getEtat()
    {
        return $this->etat;
    }

    /**
     * @param bool|null $etat
     *
     * @return self
     */
    public function setEtat($etat)
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * Get the value of required
     */
    public function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * Set the value of required
     */
    public function setRequired(bool $required): self
    {
        $this->required = $required;

        return $this;
    }

    /**
     * Get the value of multipe
     */
    public function isMultipe(): bool
    {
        return $this->multipe;
    }

    /**
     * Set the value of multipe
     */
    public function setMultipe(bool $multipe): self
    {
        $this->multipe = $multipe;

        return $this;
    }
}
