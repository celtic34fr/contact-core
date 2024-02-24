<?php

namespace Celtic34fr\ContactCore\EntityRedefine;

use Celtic34fr\ContactCore\Entity\Parameter;

class SocialNetwork extends Parameter
{
    const CLE = "socialNetwork";

    private string $name;
    private string $urlFavicon;


    public function __construct(Parameter $parameter)
    {
        $valeur = $parameter->getValeur();
        $this->name = substr($valeur, 0, strpos($valeur, '|'));
        $this->urlFavicon = substr($valeur, strpos($valeur, '|') + 1);
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
}