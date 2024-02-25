<?php

namespace Celtic34fr\ContactCore\Entity;

use Celtic34fr\ContactCore\Repository\CliInfosRepository;
use Celtic34fr\ContactCore\Validator\Constraint as CustomAssert;
use DateTime;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CliInfosRepository::class)]
/**
 * classe CliInfos : table de tail d'informations pour une relation 
 * 
 * - nom        : nom de la relation
 * - prenom     : prénom de la relation
 * - birthdate  : date de naissance de la personne, champ facultatif
 * - telephone  : numéro de télephone de la relation
 *      TODO : question de la gestion de plusieurs téléphones associés au même couple nom/prénom dont FAX
 * - client     : lien vers les informations de base de la relation, table Clientele, ManyToOne
 * 
 * TODO :
 * - fonction et/ou service de la relation décrite ici (pour fournisseur et/ou partenaire)
 * - alerte ?? => calendrier à tenir pour avoir des alertes rappel action à faire en rapport avec la relation
 *      -> gestion de planning d'activité / action en parallèle directement ou par module complémentaire
 */
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

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[Assert\DateTime]
    /**
     * date de naissance de l'internaute, champ facultatif
     * @var DateTimeImmutable|null
     */
    private ?DateTimeImmutable $birthdate = null;

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

    /**
     * Get the value of birthdate
     * @return DateTimeImmutable|null
     */
    public function getBirthdate(): ?DateTimeImmutable
    {
        return $this->birthdate;
    }

    /**
     * Set the value of birthdate
     * @param DateTimeImmutable $birthdate
     * @return self
     */
    public function setBirthdate(DateTimeImmutable $birthdate): self
    {
        $this->birthdate = $birthdate;
        return $this;
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
