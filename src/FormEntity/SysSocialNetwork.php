<?php

namespace Celtic34fr\ContactCore\FormEntity;

class SysSocialNetwork
{
    private string $name;
    private ?string $urlPage = null;
    private ?string $logoID = null;
    private ?int $logoSN = null;

    

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
    public function getLogoID(): ?string
    {
        return $this->logoID;
    }

    /**
     * Set the value of logoID
     */
    public function setLogoID(?string $logoID): self
    {
        $this->logoID = $logoID;

        return $this;
    }

    /**
     * Get the value of logoSN
     */
    public function getLogoSN(): ?int
    {
        return $this->logoSN;
    }

    /**
     * Set the value of logoSN
     */
    public function setLogoSN(?int $logoSN): self
    {
        $this->logoSN = $logoSN;

        return $this;
    }

    /**
     * redefine getValeur to serve string formated
     * @return string|null
     */
    public function getValeur(): mixed
    {
        if (!$this->name && !$this->logoID) return null;
        return ($this->name ?? "").'|'.($this->logoID ?? "");
    }
}