<?php
declare(strict_types=1);

namespace App\Entity;

/**
 * Company's Entity Class
 */
class Company
{
    private $id;
    private $name;
    private $director_name;
    private $address;
    private $tel1;
    private $tel2;
    private $email;
    private $logo;
    private $about;
    private $created;
    private $modified;
    private $modified_by;
    private $modifier;

    /**
     * Validates Company Entity
     * Check if all required fields are provided
     * 
     * @return array<string> Array of errors
     */
    public function validation(): array
    {
        $errors = [];

        if (empty($this->name)) {
            $errors[] = "Le nom de l'entreprise est requis";
        }

        if (empty($this->director_name)) {
            $errors[] = "Le nom du directeur de l'entreprise est requis";
        }

        if (empty($this->address)) {
            $errors[] = "L'adresse de l'entreprise est requise";
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
     *
     * @return  self
     */ 
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of name
     */ 
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @return  self
     */ 
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of director_name
     */ 
    public function getDirectorName()
    {
        return $this->director_name;
    }

    /**
     * Set the value of director_name
     *
     * @return  self
     */ 
    public function setDirectorName($director_name)
    {
        $this->director_name = $director_name;

        return $this;
    }

    /**
     * Get the value of address
     */ 
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set the value of address
     *
     * @return  self
     */ 
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get the value of tel1
     */ 
    public function getTel1()
    {
        return $this->tel1;
    }

    /**
     * Set the value of tel1
     *
     * @return  self
     */ 
    public function setTel1($tel1)
    {
        $this->tel1 = $tel1;

        return $this;
    }

    /**
     * Get the value of tel2
     */ 
    public function getTel2()
    {
        return $this->tel2;
    }

    /**
     * Set the value of tel2
     *
     * @return  self
     */ 
    public function setTel2($tel2)
    {
        $this->tel2 = $tel2;

        return $this;
    }

    /**
     * Get the value of email
     */ 
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the value of email
     *
     * @return  self
     */ 
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of logo
     */ 
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Set the value of logo
     *
     * @return  self
     */ 
    public function setLogo($logo)
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * Get the value of about
     */ 
    public function getAbout()
    {
        return $this->about;
    }

    /**
     * Set the value of about
     *
     * @return  self
     */ 
    public function setAbout($about)
    {
        $this->about = $about;

        return $this;
    }

    /**
     * Get the value of created
     */ 
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set the value of created
     *
     * @return  self
     */ 
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get the value of modified
     */ 
    public function getModified()
    {
        return $this->modified;
    }

    /**
     * Set the value of modified
     *
     * @return  self
     */ 
    public function setModified($modified)
    {
        $this->modified = $modified;

        return $this;
    }

    /**
     * Get the value of modified_by
     */ 
    public function getModifiedBy()
    {
        return $this->modified_by;
    }

    /**
     * Set the value of modified_by
     *
     * @return  self
     */ 
    public function setModifiedBy($modified_by)
    {
        $this->modified_by = $modified_by;

        return $this;
    }

    /**
     * Get the value of modifier
     * @return Employee 
     */ 
    public function getModifier()
    {
        return $this->modifier;
    }

    /**
     * Set the value of modifier
     *
     * @param Employee $modifier
     * @return  self
     */ 
    public function setModifier(Employee $modifier)
    {
        $this->modifier = $modifier;

        return $this;
    }
}