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

use Core\Utils\EntityUtilsTrait;

/**
 * Internship's Entity Class
 */
class Internship
{
    use EntityUtilsTrait;

    private ?int $id;

    private ?int $internship_type_id;

    private ?int $supervisorId;

    private ?string $firstName;

    private ?string $lastName;

    private ?string $email;

    private ?string $sex;

    private ?string $birthdate;

    private ?string $schoolName;

    private ?string $startDate;

    private ?string $endDate;

    private ?string $reportPath;

    private ?string $status;

    private ?int $user_id;

    private ?string $created;

    private ?string $modified;

    private ?bool $etat;

    private ?Employee $user;

    private ?Employee $supervisor;

    private ?InternshipType $internshipType;

    /**
     * ## Validates Employee
     * Check if all required fields has been provided
     * 
     * @return array Array of errors
     */
    public function validation(): array
    {
        $errors = [];

        if (empty($this->firstName)) {
            $errors[] = "Le nom du stagiaire est requis";
        }

        if (empty($this->lastName)) {
            $errors[] = "Le prénom du stagiaire est requis";
        }

        if (empty($this->startDate)) {
            $errors[] = "La date de début du stage est requise";
        }

        if (!$this->isValidDate($this->startDate)) {
            $errors[] = "La date de début du stage est incorrecte";
        }

        if (empty($this->endDate)) {
            $errors[] = "La date de fin du stage est requise";
        }

        if (!$this->isValidDate($this->endDate)) {
            $errors[] = "La date de fin du stage est incorrecte";
        }

        if (empty($this->birthdate)) {
            $errors[] = "La date de naissance est requise";
        }

        if (!$this->isValidDate($this->birthdate)) {
            $errors[] = "La date de naissance est incorrecte";
        }

        if (empty($this->sex)) {
            $errors[] = "Le sexe du stagiaire est requis";
        }

        if (empty($this->internship_type_id)) {
            $errors[] = "Le type de stage est requis";
        }

        return $errors;
    }


    /**
     * Get the value of id
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set the value of id
     */
    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of internship_type_id
     */
    public function getInternshipTypeId(): ?int
    {
        return $this->internship_type_id;
    }

    /**
     * Set the value of internship_type_id
     */
    public function setInternshipTypeId(?int $internship_type_id): self
    {
        $this->internship_type_id = $internship_type_id;

        return $this;
    }

    /**
     * Get the value of supervisorId
     */
    public function getSupervisorId(): ?int
    {
        return $this->supervisorId;
    }

    /**
     * Set the value of supervisorId
     */
    public function setSupervisorId(?int $supervisorId): self
    {
        $this->supervisorId = $supervisorId;

        return $this;
    }

    /**
     * Get the value of firstName
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * Set the value of firstName
     */
    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get the value of lastName
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * Set the value of lastName
     */
    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get the value of email
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Set the value of email
     */
    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of sex
     */
    public function getSex(): ?string
    {
        return $this->sex;
    }

    /**
     * Set the value of sex
     */
    public function setSex(?string $sex): self
    {
        $this->sex = $sex;

        return $this;
    }

    /**
     * Get the value of birthdate
     */
    public function getBirthdate(): ?string
    {
        return $this->birthdate;
    }

    /**
     * Set the value of birthdate
     */
    public function setBirthdate(?string $birthdate): self
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    /**
     * Get the value of schoolName
     */
    public function getSchoolName(): ?string
    {
        return $this->schoolName;
    }

    /**
     * Set the value of schoolName
     */
    public function setSchoolName(?string $schoolName): self
    {
        $this->schoolName = $schoolName;

        return $this;
    }

    /**
     * Get the value of startDate
     */
    public function getStartDate(): ?string
    {
        return $this->startDate;
    }

    /**
     * Set the value of startDate
     */
    public function setStartDate(?string $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get the value of endDate
     */
    public function getEndDate(): ?string
    {
        return $this->endDate;
    }

    /**
     * Set the value of endDate
     */
    public function setEndDate(?string $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get the value of reportPath
     */
    public function getReportPath(): ?string
    {
        return $this->reportPath;
    }

    /**
     * Set the value of reportPath
     */
    public function setReportPath(?string $reportPath): self
    {
        $this->reportPath = $reportPath;

        return $this;
    }

    /**
     * Get the value of status
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * Set the value of status
     */
    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get the value of user_id
     */
    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    /**
     * Set the value of user_id
     */
    public function setUserId(?int $user_id): self
    {
        $this->user_id = $user_id;

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
    public function setCreated(?string $created): self
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get the value of modified
     */
    public function getModified(): ?string
    {
        return $this->modified;
    }

    /**
     * Set the value of modified
     */
    public function setModified(?string $modified): self
    {
        $this->modified = $modified;

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
    public function setEtat(?bool $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * Get the value of user
     */
    public function getUser(): ?Employee
    {
        return $this->user;
    }

    /**
     * Set the value of user
     */
    public function setUser(?Employee $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get the value of supervisor
     */
    public function getSupervisor(): ?Employee
    {
        return $this->supervisor;
    }

    /**
     * Set the value of supervisor
     */
    public function setSupervisor(?Employee $supervisor): self
    {
        $this->supervisor = $supervisor;

        return $this;
    }

    /**
     * Get the value of internshipType
     */
    public function getInternshipType(): ?InternshipType
    {
        return $this->internshipType;
    }

    /**
     * Set the value of internshipType
     */
    public function setInternshipType(?InternshipType $internshipType): self
    {
        $this->internshipType = $internshipType;

        return $this;
    }
}
