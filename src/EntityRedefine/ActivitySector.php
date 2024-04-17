<?php

namespace Celtic34fr\ContactCore\EntityRedefine;

use Celtic34fr\ContactCore\Entity\Parameter;

class ActivitySector extends Parameter
{
    const CLE = "SysActiSector"; // table système des secteur d'activité
    
    /**
     * description du champ 'valeur'
     * 
     * category|description|parent_id
     * name             : nom du secteur d'activité - de l'activité
     * description      : description de la catégorie
     * parent_id        : identifiant de la catégorie parente (null pour le parent origine)
     */

    private string $name;
    private string $description;
    private ?int $parent_id = null;


    public function __construct(Parameter $parameter)
    {
        $valeur = $parameter->getValeur();
        $firstFieldEnd = strpos($valeur, "|");
        $this->name = substr($valeur, 0, $firstFieldEnd);
        $secondFieldEnd = strpos($valeur, "|", $firstFieldEnd + 1);
        $this->description = substr($valeur, $firstFieldEnd + 1, $secondFieldEnd - $firstFieldEnd - 1);
        $this->parent_id = (int) substr($valeur, $secondFieldEnd + 1);
    }


    /**
     * Get the value of name
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the value of name
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