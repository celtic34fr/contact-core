<?php

namespace Celtic34fr\ContactCore\FormEntity;

class SysActivitySector
{
    private string $name;
    private ?string $description = null;
    private ?int $parent_id = null;

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;        
    }

    /**
     * @param string $name
     * @return self
     */
    public function setName(string $name) : self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription() : ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return self
     */
    public function setDescription(string $description) : self
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return integer|null
     */
    public function getParentId(): ?int
    {
        return $this->parent_id;
    }

    /**
     * @param integer|null $parent_id
     * @return self
     */
    public function setParentId(?int $parent_id): self
    {
        $this->parent_id = $parent_id;
        return $this;
    }
}