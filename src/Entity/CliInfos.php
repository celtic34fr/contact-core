<?php

namespace Celtic34fr\ContactCore\Entity;

use Doctrine\ORM\Mapping as ORM;
use Celtic34fr\ContactCore\Repository\CliInfosRepository;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: CliInfosRepository::class)]
class CliInfos
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, length: 255)]
    private ?string $nom = null;        // nom de l'internaute

    #[ORM\Column(type: Types::TEXT, length: 255, nullable: true)]
    private ?string $prenom = null;     // pénom de l'internaute

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $telephone;  // numéro de téléphone

    #[ORM\ManyToOne(inversedBy: 'cliInfos')]
    private ?Clientele $client;         // lien avec la table des information fixes : courriel

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): self
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getFullname(): string
    {
        $fullname = $this->nom;
        if ($this->prenom) {
            $fullname .= ' ' . $this->prenom;
        }
        return $fullname;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;
        return $this;
    }

    public function getClient(): Clientele
    {
        return $this->client;
    }

    public function setClient(?Clientele $client = null): self
    {
        $this->client = $client;
        return $this;
    }
}
