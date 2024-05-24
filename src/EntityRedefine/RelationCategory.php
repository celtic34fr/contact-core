<?php

namespace Celtic34fr\ContactCore\EntityRedefine;

use Celtic34fr\ContactCore\Entity\Parameter;
use DateTimeImmutable;

class RelationCategory extends Parameter
{
    const CLE = "SysRCategory"; // liste des catégories relativement aux types de relation

    /**
     * description du champ 'valeur'
     * 
     * category|description|parent_id
     * category         : nom de la catégorie de la relation
     * description      : description de la catégorie
     */
    
    private string $category;
    private string $description;


    public function __construct($parameter = null)
    {
        if ($parameter) {
            if ($parameter instanceof Parameter) {
                $valeur = $parameter->getValeur();
                $firstFieldEnd = strpos($valeur, "|");
                $this->category = substr($valeur, 0, $firstFieldEnd);
                $this->description = substr($valeur, $firstFieldEnd + 1);
                $this->id = $parameter->getId();
                $this->cle = $parameter->getCle();
                $this->ord = $parameter->getOrd();
                $this->created_at = $parameter->getCreatedAt();
                $this->updated_at = $parameter->getUpdatedAt();
            } elseif (is_array($parameter)) {
                $this->id = $parameter['id'] ?? null;
                $this->cle = $parameter['cle'];
                $this->ord = $parameter['ord'];
                $this->created_at = gettype($parameter['created']) == 'string' ? new DateTimeImmutable($parameter['created']) : $parameter['created'];
                $this->updated_at = gettype($parameter['updated']) == 'string' ? new DateTimeImmutable($parameter['updated']) : $parameter['updated'];
                $this->category = $parameter['category'];
                $this->description = $parameter['description'];
            }
        }
        // if empty valeur : not is initialize
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
     * redefine getValeur to serve string formated
     * @return string|null
     */
    public function getValeur(): mixed
    {
        if (!$this->category && !$this->description) return null;
        return ($this->category ?? "").'|'.($this->description ?? "");
    }

    public function getAsArray(): array
    {
        return [
            'id' => $this->getId(),
            'cle' => $this->getCle(),
            'ord' => $this->getOrd(),
            'created' => $this->getCreatedAt(),
            'updated' => $this->getUpdatedAt(),
            'category' => $this->getCategory(),
            'description' => $this->getDescription(),
        ];
    }

    public function getParameter(): Parameter
    {
        $parameter = new Parameter();
        $parameter->setId($this->getId());
        $parameter->setCle($this->getCle());
        $parameter->setOrd($this->getOrd());
        $parameter->setValeur($this->getValeur());
        $parameter->setCreatedAt($this->getCreatedAt());
        $parameter->setUpdatedAt($this->getUpdatedAt());
        return $parameter;
    }
}