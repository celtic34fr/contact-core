<?php

namespace Celtic34fr\ContactCore\EntityRedefine;

use Celtic34fr\ContactCore\Entity\Parameter;

class SocialNetwork extends Parameter
{
    const CLE = "SysSocialNetwork"; // liste de définition des réseaux sociaux utilisés dans l'application 'contact'

    private ?string $name = null;
    private ?string $urlPage = null;
    private ?int $logoID = null;

    public function __construct(Parameter $parameter= null)
    {
        if ($parameter) {
            $valeur = $parameter->getValeur();
            if ($valeur) { // if sommething is in valeur
                $first  = strpos($valeur, '|');
                $second = strpos($valeur, '|', $first);
                $this->name = substr($valeur, 0, $first);
                $this->logoID = (int) substr($valeur, $second + 1);
                $this->urlPage = null;
            }
        }
        // if empty valeur : not is initialize
    }

    /**
     * Get the value of name
     */
    public function getName(): ?string
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
     * Get the value of ID logo save in PieceJointe
     */
    public function getLogoID(): ?int
    {
        return $this->logoID;
    }

    /**
     * Set the value of ID logo save in PieceJointe
     */
    public function setLogoID(string $logoID): self
    {
        $this->logoID = $logoID;
        return $this;
    }

    /**
     * Get the value of URL in social network
     */
    public function getUrlPage(): ?string
    {
        return $this->urlPage;
    }

    /**
     * Set the value of URL in social network
     */
    public function setUrlPage(string $urlPage): self
    {
        $this->urlPage = $urlPage;
        return $this;
    }

    /**
     * redefine getValeur to serve string formated
     * @return string|null
     */
    public function getValeur(): mixed
    {
        if (!$this->name && !$this->logoID) return null;
        return ($this->name ?? "").'|'($this->logoID ?? "");
    }
}