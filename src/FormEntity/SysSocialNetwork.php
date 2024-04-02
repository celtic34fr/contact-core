<?php

namespace Celtic34fr\ContactCore\FormEntity;

class SysSocialNetwork
{
    private string $name;
    private ?string $urlPage = null;
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
     * Get the value of urlPage
     */
    public function getUrlPage(): ?string
    {
        return $this->urlPage;
    }

    /**
     * Set the value of urlPage
     */
    public function setUrlPage(?string $urlPage): self
    {
        $this->urlPage = $urlPage;

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
     */
    public function setLogoID(?int $logoID): self
    {
        $this->logoID = $logoID;

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