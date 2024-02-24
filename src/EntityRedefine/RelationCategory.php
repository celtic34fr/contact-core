<?php

namespace Celtic34fr\ContactCore\EntityRedefine;

use Celtic34fr\ContactCore\Entity\Parameter;

class RelationCategory extends Parameter
{
    const CLE = "relationCategory";
    
    private string $category;
    private string $description;
    private ?int $parent_id = null;


    public function __construct(Parameter $parameter)
    {
        $valeur = $parameter->getValeur();
        $firstFieldEnd = strpos($valeur, "|");
        $this->category = substr($valeur, 0, $firstFieldEnd);
        $secondFieldEnd = strpos($valeur, "|", $firstFieldEnd + 1);
        $this->description = substr($valeur, $firstFieldEnd + 1, $secondFieldEnd - $firstFieldEnd - 1);
        $this->parent_id = (int) substr($valeur, $secondFieldEnd + 1);
    }


    /**
     * Get the value of category
     */
    public function getCategory(): string
    {
        return $this->category;
    }

    /**
     * Set the value of category
     */
    public function setCategory(string $category): self
    {
        $this->category = $category;
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
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get the value of parent_id
     */
    public function getParentId(): ?int
    {
        return $this->parent_id;
    }

    /**
     * Set the value of parent_id
     */
    public function setParentId(?int $parent_id): self
    {
        $this->parent_id = $parent_id;
        return $this;
    }
}