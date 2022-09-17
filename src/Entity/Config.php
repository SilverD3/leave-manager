<?php
declare(strict_types=1);

namespace App\Entity;

/**
 * Config's Entity Class
 */
class Config
{
    private $id;
    private $code;
    private $description;
    private $default_value;
    private $value;
    private $value_type;
    private $modified;
    private $modified_by;

    /**
     * Validates Company Entity
     * Check if all required fields are provided
     * 
     * @return array<string> Array of errors
     */
    public function validation(): array
    {
        $errors = [];

        if (empty($this->code)) {
            $errors[] = "Le nom du paramètre est requis";
        }

        if (empty($this->description)) {
            $errors[] = "La description du paramètre est requise";
        }

        if (is_null($this->default_value)) {
            $errors[] = "La valeur par défaut du paramètre est requise";
        }

        if (is_null($this->value)) {
            $errors[] = "La valeur du paramètre est requise";
        }

        if (empty($this->value_type)) {
            $errors[] = "Le type de valeur du paramètre est requis";
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
     * Get the value of code
     */ 
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set the value of code
     *
     * @return  self
     */ 
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get the value of description
     */ 
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the value of description
     *
     * @return  self
     */ 
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the value of default_value
     */ 
    public function getDefaultValue()
    {
        return $this->default_value;
    }

    /**
     * Set the value of default_value
     *
     * @return  self
     */ 
    public function setDefaultValue($default_value)
    {
        $this->default_value = $default_value;

        return $this;
    }

    /**
     * Get the value of value
     */ 
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set the value of value
     *
     * @return  self
     */ 
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get the value of value_type
     */ 
    public function getValueType()
    {
        return $this->value_type;
    }

    /**
     * Set the value of value_type
     *
     * @return  self
     */ 
    public function setValueType($value_type)
    {
        $this->value_type = $value_type;

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
}