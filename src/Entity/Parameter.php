<?php

namespace Celtic34fr\ContactCore\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Celtic34fr\ContactCore\Repository\ParameterRepository;
use DateTimeImmutable;

#[ORM\Entity(repositoryClass: ParameterRepository::class)]
#[ORM\Table(name: 'parameters')]
#[ORM\HasLifecycleCallbacks]
class Parameter
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, length: 16)]
    private string $cle;

    #[ORM\Column(type: Types::INTEGER)]
    private int $ord = 0;

    #[ORM\Column(type: Types::TEXT, length: 255)]
    private string $valeur;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: false)]
    private DateTimeImmutable $created_at;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: false)]
    private DateTimeImmutable $updated_at;

    public function __construct()
    {
        $this->created_at = new DateTimeImmutable('now');        
    }

    /**
     * Get the value of id
     */ 
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the value of cle
     */ 
    public function getCle(): string
    {
        return $this->cle;
    }

    /**
     * Set the value of cle
     * @return  self
     */ 
    public function setCle(string $cle): self
    {
        $this->cle = $cle;
        return $this;
    }

    /**
     * Get the value of ord
     */ 
    public function getOrd(): int
    {
        return $this->ord;
    }

    /**
     * Set the value of ord
     * @return  self
     */ 
    public function setOrd(int $ord): self
    {
        $this->ord = $ord;
        return $this;
    }

    /**
     * Get the value of valeur
     */ 
    public function getValeur(): string
    {
        return $this->valeur;
    }

    /**
     * Set the value of valeur
     * @return  self
     */ 
    public function setValeur(string $valeur): self
    {
        $this->valeur = $valeur;
        return $this;
    }

    /**
     * Get the value of created_at
     */ 
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->created_at;
    }

    /**
     * Set the value of created_at
     * @return  self
     */ 
    public function setCreatedAt(DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;
        return $this;
    }

    /**
     * Get the value of updated_at
     */
    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updated_at;
    }

    /**
     * Set the value of updated_at
     * @return  self
     */
    public function setUpdatedAt(DateTimeImmutable $updated_at): self
    {
        $this->updated_at = $updated_at;
        return $this;
    }
}
