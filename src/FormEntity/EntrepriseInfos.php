<?php

namespace Celtic34fr\ContactCore\FormEntity;

class EntrepriseInfos
{
    private string $designation = null;
    private string $siren = null;
    private string $siret = null;
    private string $courriel = null;
    private string $telephone = null;
    private string $courriel_reponse = null;              



    /**
     * Set EntrepriseInfos attributes by array
     * @param array $parameters
     * @return EntrepriseInfos
     */
    public function setByArray(array $parameters): self
    {
        $this->designation = $parameters['designation'] ?? "";
        $this->siren = $parameters['siren'] ?? "";
        $this->siret = $parameters['siret'] ?? "";
        $this->courriel = $parameters['courriel'] ?? "";
        $this->telephone = $parameters['telephone'] ?? "";
        $this->courriel_reponse = $parameters['courriel_reponse'] ?? "";
        return $this;
    }


    /**
     * Get the value of designation
     */ 
    public function getDesignation(): ?string
    {
        return $this->designation;
    }

    /**
     * Set the value of designation
     *
     * @return  EntrepriseInfos
     */ 
    public function setDesignation($designation): self
    {
        $this->designation = $designation;

        return $this;
    }

    /**
     * Get the value of siren
     */ 
    public function getSiren(): ?string
    {
        return $this->siren;
    }

    /**
     * Set the value of siren
     *
     * @return  EntrepriseInfos
     */ 
    public function setSiren($siren): self
    {
        $this->siren = $siren;

        return $this;
    }

    /**
     * Get the value of siret
     */ 
    public function getSiret(): ?string
    {
        return $this->siret;
    }

    /**
     * Set the value of siret
     *
     * @return  EntrepriseInfos
     */ 
    public function setSiret($siret): self
    {
        $this->siret = $siret;

        return $this;
    }

    /**
     * Get the value of courriel
     */ 
    public function getCourriel(): ?string
    {
        return $this->courriel;
    }

    /**
     * Set the value of courriel
     *
     * @return  EntrepriseInfos
     */ 
    public function setCourriel($courriel): self
    {
        $this->courriel = $courriel;

        return $this;
    }

    /**
     * Get the value of telephone
     */ 
    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    /**
     * Set the value of telephone
     *
     * @return  EntrepriseInfos
     */ 
    public function setTelephone($telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * Get the value of courriel_reponse
     */ 
    public function getCourriel_reponse(): ?string
    {
        return $this->courriel_reponse;
    }

    /**
     * Set the value of courriel_reponse
     *
     * @return  EntrepriseInfos
     */ 
    public function setCourriel_reponse($courriel_reponse): self
    {
        $this->courriel_reponse = $courriel_reponse;

        return $this;
    }
}
