<?php

namespace Celtic34fr\ContactCore\FormEntity;

class SysSocialNetwork
{
    private string $name;

    private string $urlFavicon;
    

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
     * Get the value of urlFavicon
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
     * get the valeur from name & urlFavicon to serve string formated
     * @return string|null
     */
    public function getValeur(): mixed
    {
        if (!$this->name && !$this->urlFavicon) return null;
        return ($this->name ?? "").'|'.($this->urlFavicon ?? "");
    }
}