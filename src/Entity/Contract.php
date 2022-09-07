<?php
declare(strict_types=1);

namespace App\Entity;

/**
 * Contract's Entity Class
 */
class Contract
{
	private $id;
	private $employee_id;
	private $title;
	private $contract_type_id;
	private $start_date;
	private $end_date;
	private $job_object;
	private $job_description;
	private $job_salary;
	private $hourly_rate;
	private $pdf;
	private $created;
	private $modified;
    private $status;
	private $etat;

    private $employee;
    private $contract_type;

    /**
     * Validates Contract
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

        if (empty($this->contract_type_id)) {
            $errors[] = "Le type de contrat est requis";
        }

        if (empty($this->start_date)) {
            $errors[] = "La date de début est requise";
        }

        if (empty($this->job_object)) {
            $errors[] = "L'object du contrat est requis";
        }

        if (!empty($this->end_date) && strtotime($this->start_date) > strtotime($this->end_date)) {
            $errors[] = "La date de début du contrat ne peut être postérieure à la date de fin";
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
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     *
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $title;

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
    public function getEndDate()
    {
        return $this->end_date;
    }

    /**
     * @param mixed $end_date
     *
     * @return self
     */
    public function setEndDate($end_date)
    {
        $this->end_date = $end_date;

        return $this;
    }

    /**
     * @return string
     */
    public function getJobObject()
    {
        return $this->job_object;
    }

    /**
     * @param string $job_object
     *
     * @return self
     */
    public function setJobObject($job_object)
    {
        $this->job_object = $job_object;

        return $this;
    }

    /**
     * @return string
     */
    public function getJobDescription()
    {
        return $this->job_description;
    }

    /**
     * @param string|null $job_description
     *
     * @return self
     */
    public function setJobDescription($job_description)
    {
        $this->job_description = $job_description;

        return $this;
    }

    /**
     * @return float|int|null
     */
    public function getJobSalary()
    {
        return $this->job_salary;
    }

    /**
     * @param float|int|null $job_salary
     *
     * @return self
     */
    public function setJobSalary($job_salary)
    {
        $this->job_salary = $job_salary;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getHourlyRate()
    {
        return $this->hourly_rate;
    }

    /**
     * @param string|null $hourly_rate
     *
     * @return self
     */
    public function setHourlyRate($hourly_rate)
    {
        $this->hourly_rate = $hourly_rate;

        return $this;
    }

    /**
	 * Get the value of pdf
	 */ 
	public function getPdf()
	{
		return $this->pdf;
	}

	/**
	 * Set the value of pdf
	 *
	 * @return  self
	 */ 
	public function setPdf($pdf)
	{
		$this->pdf = $pdf;

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
     * @return string|null
     */
    public function getModified()
    {
        return $this->modified;
    }

    /**
     * @param string|null $modified
     *
     * @return self
     */
    public function setModified($modified)
    {
        $this->modified = $modified;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string|null $status
     *
     * @return self
     */
    public function setStatus($status)
    {
        $this->status = $status;

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
     * @param bool $etat
     *
     * @return self
     */
    public function setEtat(bool $etat)
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * Get the value of employee
     */ 
    public function getEmployee(): ?Employee
    {
        return $this->employee;
    }

    /**
     * Set the value of employee
     *
     * @param Employee $employee
     * @return  self
     */ 
    public function setEmployee(Employee $employee)
    {
        $this->employee = $employee;

        return $this;
    }

    /**
     * Get the value of contract_type
     * @return ContractType|null Contract type of contract
     */ 
    public function getContractType(): ?ContractType
    {
        return $this->contract_type;
    }

    /**
     * Set the value of contract_type
     *
     * @param ContractType $contract_type
     * @return  self
     */ 
    public function setContractType(ContractType $contract_type)
    {
        $this->contract_type = $contract_type;

        return $this;
    }
}