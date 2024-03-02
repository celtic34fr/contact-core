<?php

namespace Celtic34fr\ContactCore\EntityRedefine;

use Celtic34fr\ContactCore\Entity\Parameter;

class SocialNetwork extends Parameter
{
    const CLE = "SysSocialNetwork"; // liste de définition des réseaux sociaux utilisés dans l'application 'contact'

    private ?string $name = null;
    private ?string $urlFavicon = null;


    public function __construct(Parameter $parameter)
    {
        $valeur = $parameter->getValeur();
        if ($valeur) { // if sommething is in valeur
            $this->name = substr($valeur, 0, strpos($valeur, '|'));
            $this->urlFavicon = substr($valeur, strpos($valeur, '|') + 1);
        }
        // if empty valeur : not is initialize
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
     * Get the value of favicon
     */
    public function getUrlFavicon(): string
    {
        return $this->urlFavicon;
    }

    /**
     * Set the value of urlFavicon
     */
    public function setUrlFavicon(string $urlFavicon): self
    {
        $this->urlFavicon = $urlFavicon;

        return $this;
    }

    /**
     * redefine getValeur to serve string formated
     * @return string|null
     */
    public function getValeur(): mixed
    {
        if (!$this->name && !$this->urlFavicon) return null;
        return ($this->name ?? "").'|'.($this->urlFavicon ?? "");
    }
}