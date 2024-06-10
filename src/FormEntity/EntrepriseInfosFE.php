<?php

namespace Celtic34fr\ContactCore\FormEntity;

class EntrepriseInfosFE
{
    private ?string $designation = null;
    private ?string $siren = null;
    private ?string $siret = null;
    private ?string $courriel = null;
    private ?string $telephone = null;
    private ?string $reponse = null;
    private ?string $logoID = null;


    public function __construct(array $parameters = null)
    {
        if ($parameters) {
            $this->setByArray($parameters);
        }
    }

    /**
     * Return all informations contains in object in an array
     *
     * @return array
     */
    public function getInfosArray(): array
    {
        $parameters = [];
        if ($this->designation)     $parameters['designation']   = $this->designation;
        if ($this->siren)           $parameters['siren']         = $this->siren;
        if ($this->siret)           $parameters['siret']         = $this->siret;
        if ($this->courriel)        $parameters['courriel']      = $this->courriel;
        if ($this->telephone)       $parameters['telephone']     = $this->telephone;
        if ($this->reponse)         $parameters['reponse']       = $this->reponse;
        if ($this->logoID)          $parameters['logoID']        = $this->logoID;

        return $parameters;
    }

    /**
     * Set EntrepriseInfosFE attributes by array
     * @param array $parameters
     * @return EntrepriseInfosFE
     */
    public function setByArray(array $parameters): self
    {
        if (array_key_exists('designation', $parameters))   $this->designation  = $parameters['designation'];
        if (array_key_exists('siren', $parameters))         $this->siren        = $parameters['siren'];
        if (array_key_exists('siret', $parameters))         $this->siret        = $parameters['siret'];
        if (array_key_exists('courriel', $parameters))      $this->courriel     = $parameters['courriel'];
        if (array_key_exists('telephone', $parameters))     $this->telephone    = $parameters['telephone'];
        if (array_key_exists('reponse', $parameters))       $this->reponse      = $parameters['reponse'];
        if (array_key_exists('logoID', $parameters))        $this->logoID       = $parameters['logoID'];

        /** 
         * if siren is empty && siret not empty :
         *      siren = 9 first number of siret
         */
        if (!empty($this->siret) && empty($this->siren)) {
            $this->siren = substr($this->siret, 0, 9);
        }

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
     * @return  EntrepriseInfosFE
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
     * @return  EntrepriseInfosFE|false
     */ 
    public function setSiren($siren): mixed
    {
        if (strlen($siren) == 9 && is_numeric($siren)) {
            $this->siren = $siren;
            return $this;
        }
        return false;
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
     * @return  EntrepriseInfosFE|null
     */ 
    public function setSiret($siret): mixed
    {
        if (strLen($siret) == 14 && is_numeric($siret)) {
            $this->siret = $siret;
            return $this;
        }
        return false;
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
     * @return  EntrepriseInfosFE
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
     * @return  EntrepriseInfosFE|false
     */ 
    public function setTelephone($telephone): mixed
    {
        /** string must contains numbers, space, dot and plus character */
        $tempo = str_replace('+', '', $telephone);
        $tempo = str_replace('.', '', $tempo);
        $tempo = str_replace(' ', '', $tempo);
        if (substr($tempo, 0, 2) == '33') $tempo = "0".substr($tempo, 2);
        if (!is_numeric($tempo) || strlen($tempo) != 10) return false;
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * Get the value of reponse
     */ 
    public function getReponse(): ?string
    {
        return $this->reponse;
    }

    /**
     * Set the value of reponse
     *
     * @return  EntrepriseInfosFE
     */ 
    public function setReponse($reponse): self
    {
        $this->reponse = $reponse;

        return $this;
    }

    /**
     * Get the value of logoID
     */ 
    public function getLogoID(): ?string
    {
        return $this->logoID;
    }

    /**
     * Set the value of logoID
     *
     * @param string $logoID
     * @return  EntrepriseInfosFE|false
     */ 
    public function setLogoID(string $logoID): mixed
    {
        if (!is_numeric($logoID) || $logoID < 0) return false;
        
        $this->logoID = $logoID;
        return $this;
    }
}
