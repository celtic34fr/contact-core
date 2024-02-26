<?php

namespace Celtic34fr\ContactCore\EntityRedefine;

use Celtic34fr\ContactCore\Entity\Parameter;

class RelationCategory extends Parameter
{
    const CLE = "relationCategory";

    /**
     * description du champ 'valeur'
     * 
     * relation_type|category|description|parent_id
     * relation_type    : type de relation à qualifier cf entité Clientele, champ 'type'
     * category         : nom de la catégorie de la relation
     * description      : description de la catégorie
     * parent_id        : identifiant de la catégorie parente (null pour le parent origine)
     */
    
    private string $relation_type;
    private string $category;
    private string $description;
    private ?int $parent_id = null;


    public function __construct(Parameter $parameter)
    {
        $valeur = $parameter->getValeur();
        $firstFieldEnd = strpos($valeur, "|");
        $this->relation_type = substr($valeur, 0, $firstFieldEnd);
        $secondFieldEnd = strpos($valeur, "|", $firstFieldEnd + 1);
        $this->category = substr($valeur, $firstFieldEnd + 1, $secondFieldEnd - $firstFieldEnd - 1);
        $thirdFieldEnd = strpos($valeur, "|", $secondFieldEnd + 1);
        $this->description = substr($valeur, $secondFieldEnd + 1, $thirdFieldEnd - $secondFieldEnd - 1);
        $this->parent_id = (int) substr($valeur, $thirdFieldEnd + 1);
    }


    /**
     * Get the value of relation_type
     */
    public function getRelationType(): string
    {
        return $this->relation_type;
    }

    /**
     * Set the value of relation_type
     */
    public function setRelationType(string $relation_type): self
    {
        $this->relation_type = $relation_type;
        return $this;
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