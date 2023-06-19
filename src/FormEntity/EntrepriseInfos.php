<?php

namespace Celtic34fr\ContactCore\FormEntity;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

class EntrepriseInfos
{
    private ?string $designation = null;
    private ?string $siren = null;
    private ?string $siret = null;
    private ?string $courriel = null;
    private ?string $telephone = null;
    private ?string $reponse = null;
    #[Assert\File(
        maxSize: '4096k',
        extensions: ['png', 'gif', 'jpg', 'jpeg', 'svg'],
        extensionsMessage: 'Veuillez choisir une image comme logo'
    )]
    private ?File $logo = null;


    public function __construct(array $parameters = null)
    {
        if ($parameters) {
            $this->setByArray($parameters);
        }
    }

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
        $this->reponse = $parameters['reponse'] ?? "";

        /** Traitement de la question du logo */
        if (array_key_exists('logo', $parameters) && is_string($parameters['logo'])) {
            $filesystem = new Filesystem();
            $projectDir = $this->getContainer()->getParameter('kernel.project_dir');
        } else {
            $this->logo = null;
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
     * Get the value of reponse
     */ 
    public function getReponse(): ?string
    {
        return $this->reponse;
    }

    /**
     * Set the value of reponse
     *
     * @return  EntrepriseInfos
     */ 
    public function setReponse($reponse): self
    {
        $this->reponse = $reponse;

        return $this;
    }

    /**
     * Get the value of logo
     */ 
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Set the value of logo
     *
     * @return  self
     */ 
    public function setLogo($logo)
    {
        $this->logo = $logo;

        return $this;
    }
}
