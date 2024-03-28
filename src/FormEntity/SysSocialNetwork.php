<?php

namespace Celtic34fr\ContactCore\FormEntity;

class SysSocialNetwork
{
    private string $name;
    private ?int $logoID = null;
    

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
     * Get the value of logoID
     */ 
    public function getLogoID(): ?int
    {
        return $this->logoID;
    }

    /**
     * Set the value of logoID
     *
     * @return  EntrepriseInfosFE|false
     */ 
    public function setLogoID(int $logoID): mixed
    {
        if (!is_numeric($logoID) || $logoID < 0) return false;
        
        $this->logoID = $logoID;
        return $this;
    }

    /**
     * get the valeur from name & urlFavicon to serve string formated
     * @return string|null
     */
    public function getValeur(): mixed
    {
        if (!$this->name && !$this->logoID) return null;
        return ($this->name ?? "").'|'.($this->logoID ?? "");
    }
}