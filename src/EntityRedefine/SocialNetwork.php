<?php

namespace Celtic34fr\ContactCore\EntityRedefine;

use Celtic34fr\ContactCore\Entity\Parameter;
use DateTimeImmutable;

class SocialNetwork extends Parameter
{
    const CLE = "SysSocialNetwork"; // liste de définition des réseaux sociaux utilisés dans l'application 'contact'

    private ?string $name = null;
    private ?string $urlPage = null;
    private ?int $logoID = null;

    public function __construct($parameter= null)
    {
        if ($parameter) {
            if ($parameter instanceof Parameter) {
                $valeur = $parameter->getValeur();
                if ($valeur) { // if sommething is in valeur
                    $first  = strpos($valeur, '|');
                    $this->name = substr($valeur, 0, $first);
                    $this->logoID = (int) substr($valeur, $first + 1);
                    $this->urlPage = null;
                }
                $this->id = $parameter->getId();
                $this->setCle($parameter->getCle());
                $this->setOrd($parameter->getOrd());
                $this->setCreatedAt($parameter->getCreatedAt());
                $this->setUpdatedAt($parameter->getUpdatedAt());
            } elseif (is_array($parameter)) {
                $this->id = $parameter['id'] ?? null;
                $this->cle = $parameter['cle'];
                $this->ord = $parameter['ord'];
                $this->created_at = gettype($parameter['created']) == 'string' ? new DateTimeImmutable($parameter['created']) : $parameter['created'];
                $this->updated_at = gettype($parameter['updated']) == 'string' ? new DateTimeImmutable($parameter['updated']) : $parameter['updated'];
                $this->name = $parameter['name'];
                $this->logoID = (int) $parameter['logoID'];
                $this->urlPage = $parameter['urlPage'];
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
        return ($this->name ?? "").'|'.($this->logoID ?? "");
    }

    public function getAsArray(): array
    {
        return [
            'id' => $this->getId(),
            'cle' => $this->getCle(),
            'ord' => $this->getOrd(),
            'created' => $this->getCreatedAt(),
            'updated' => $this->getUpdatedAt(),
            'name' => $this->getName(),
            'logoID' => $this->getLogoID(),
            'urlPage' => $this->getUrlPage(),
        ];
    }

    public function getParameter(): Parameter
    {
        $parameter = new Parameter();
        $parameter->setId($this->getId());
        $parameter->setCle($this->getCle());
        $parameter->setOrd($this->getOrd());
        $parameter->setValeur($this->getValeur());
        $parameter->setCreatedAt($this->getCreatedAt());
        $parameter->setUpdatedAt($this->getUpdatedAt());
        return $parameter;
    }
}