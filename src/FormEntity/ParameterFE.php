<?php

namespace Celtic34fr\ContactCore\FormEntity;

use Symfony\Component\Validator\Constraints as Assert;

class ParameterFE
{
    #[Assert\NotBlank()]
    private string $name;
    #[Assert\NotBlank()]
    private string $description;

    #[Assert\NotBlank()]
    private array $values = [];

    /**
     * Get the value of name
     */ 
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the value of name
     * @return  self
     */ 
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get the value of description
     */ 
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Set the value of description
     * @return  self
     */ 
    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get the value of values
     */ 
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * Set the value of values
     * @return  self
     */ 
    public function setValues(array $values): self
    {
        $this->values = $values;
        return $this;
    }

    public function addValue(string $value): self
    {
        $this->values[] = $value;
        return $this;
    }

    public function removeValue(string $value): mixed
    {
        $values = $this->values;
        if (!in_array($value, $values)) {
            return false;
        }
        $idx = array_search($value, $values);
        if (!$idx) {
            return false;
        }
        unset($values[$idx]);
        $this->values = $values;
        return $this;
    }
}
