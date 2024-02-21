<?php

namespace Celtic34fr\ContactCore\Entity;

use Celtic34fr\ContactCore\Repository\CliInfosRepository;
use Celtic34fr\ContactCore\Validator\Constraint as CustomAssert;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CliInfosRepository::class)]
class CliInfos
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, length: 255, nullable: false)]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Type('string')]
    /**
     * nom de l'internaute, champ obligatoire
     * @var string
     */
    private string $nom;

    #[ORM\Column(type: Types::TEXT, length: 255, nullable: true)]
    #[Assert\Type('string')]
    /**
     * prénom de l'internaute, champ facultatif
     * @var string|null
     */
    private ?string $prenom = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Type('string')]
    #[CustomAssert\PhoneNumber]
    /**
     * numéro de téléphone, champ facultatif
     * @var string|null
     */
    private ?string $telephone = null;

    #[ORM\ManyToOne(inversedBy: 'cliInfos')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    #[Assert\NotNull()]
    #[Assert\Type(Clientele::class)]
    /**
     * lien avec la table des information fixes : courriel, champ obligatoire
     * @var Clientele
     */
    private Clientele $client;

    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): string
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

    public function setClient(Clientele $client): self
    {
        $this->client = $client;
        return $this;
    }
}
