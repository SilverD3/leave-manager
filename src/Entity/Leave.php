<?php
declare(strict_types=1);

namespace App\Entity;

/**
 * Leave Entity Class
 */
class Leave
{
	private $id;
	private $employee_id;
	private $year;
	private $days;
	private $used_days;
	private $start_date;
	private $created;
	private $modified;
	private $note;
	private $etat;

    /**
     * Validates Leave
     * Check if all required fields are provided
     * 
     * @return array Array of errors
     */
	public function validation(): array
    {
        $errors = [];

        if (empty($this->employee_id)) {
            $errors[] = "L'employé est requis";
        }

        if (empty($this->year)) {
            $errors[] = "L'année du congé est requis";
        }

        if (empty($this->days)) {
            $errors[] = "Le nombre de jour de congé est requis";
        }

        if (empty($this->start_date)) {
            $errors[] = "La date de départ en congé est requise";
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
    public function getEmployeeId()
    {
        return $this->employee_id;
    }

    /**
     * @param mixed $employee_id
     *
     * @return self
     */
    public function setEmployeeId($employee_id)
    {
        $this->employee_id = $employee_id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @param mixed $year
     *
     * @return self
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDays()
    {
        return $this->days;
    }

    /**
     * @param mixed $days
     *
     * @return self
     */
    public function setDays($days)
    {
        $this->days = $days;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUsedDays()
    {
        return $this->used_days;
    }

    /**
     * @param mixed $used_days
     *
     * @return self
     */
    public function setUsedDays($used_days)
    {
        $this->used_days = $used_days;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getStartDate()
    {
        return $this->start_date;
    }

    /**
     * @param mixed $start_date
     *
     * @return self
     */
    public function setStartDate($start_date)
    {
        $this->start_date = $start_date;

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
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param mixed $note
     *
     * @return self
     */
    public function setNote($note)
    {
        $this->note = $note;

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
}