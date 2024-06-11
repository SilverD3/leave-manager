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
 * Internship Document's Entity Class
 */
class InternshipDocument
{
    private $id;
    private $internshipDocumentTypeId;
    private $internshipId;
    private $document;
    private $created;
    private $etat;

    private $internship;
    private $internshipDocumentType;

    /**
     * Validates Internship DOcument
     * Check if all required fields are provided
     * 
     * @return array Array of errors
     */
    public function validation(): array
    {
        $errors = [];

        if (empty($this->internshipId)) {
            $errors[] = "Le stage impliqué est requis";
        }

        if (empty($this->document)) {
            $errors[] = "Le document de stage est requis";
        }

        if (empty($this->internshipDocumentTypeId)) {
            $errors[] = "Le type de document de stage est requis";
        }

        return $errors;
    }


    /**
     * Get the value of id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     */
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of internshipDocumentTypeId
     */
    public function getInternshipDocumentTypeId(): ?int
    {
        return $this->internshipDocumentTypeId;
    }

    /**
     * Set the value of internshipDocumentTypeId
     */
    public function setInternshipDocumentTypeId(int $internshipDocumentTypeId): self
    {
        $this->internshipDocumentTypeId = $internshipDocumentTypeId;

        return $this;
    }

    /**
     * Get the value of internshipId
     */
    public function getInternshipId(): ?int
    {
        return $this->internshipId;
    }

    /**
     * Set the value of internshipId
     */
    public function setInternshipId(int $internshipId): self
    {
        $this->internshipId = $internshipId;

        return $this;
    }

    /**
     * Get the value of document
     */
    public function getDocument(): ?string
    {
        return $this->document;
    }

    /**
     * Set the value of document
     */
    public function setDocument(string $document): self
    {
        $this->document = $document;

        return $this;
    }

    /**
     * Get the value of created
     */
    public function getCreated(): ?string
    {
        return $this->created;
    }

    /**
     * Set the value of created
     */
    public function setCreated(string $created): self
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get the value of etat
     */
    public function getEtat(): ?bool
    {
        return $this->etat;
    }

    /**
     * Set the value of etat
     */
    public function setEtat(bool $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * Get the value of internship
     */
    public function getInternship(): ?Internship
    {
        return $this->internship;
    }

    /**
     * Set the value of internship
     */
    public function setInternship(Internship $internship): self
    {
        $this->internship = $internship;

        return $this;
    }

    /**
     * Get the value of internshipDocumentType
     */
    public function getInternshipDocumentType(): ?InternshipDocumentType
    {
        return $this->internshipDocumentType;
    }

    /**
     * Set the value of internshipDocumentType
     */
    public function setInternshipDocumentType(InternshipDocumentType $internshipDocumentType): self
    {
        $this->internshipDocumentType = $internshipDocumentType;

        return $this;
    }
}
