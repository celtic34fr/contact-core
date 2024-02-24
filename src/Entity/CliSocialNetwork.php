<?php

namespace Celtic34fr\ContactCore\Entity;

use Celtic34fr\ContactCore\Repository\CliSocialNetworkRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CliSocialNetworkRepository::class)]
#[ORM\Table(name:'cliSocialNetwork')]
#[ORM\HasLifecycleCallbacks]
/**
 * classe CliSocialNetwork : référence des pages de réseaux sociaux d'une relation
 * 
 * - created_at : date de création de la référence à un reseau social
 * - closed_at  : date de clôture de la référence à un reseau social
 * - network    : réseau social cible
 * - url        : lien HTTP d'accès à la page sur le réseau social
 * - client     : lien vers l'netité relation table clientele ManyToOne bidirectional
 */
class CliSocialNetwork
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: false)]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\DateTime]
    /**
     * date de création, champ obligatoire
     * @var DateTimeImmutable
     */
    private DateTimeImmutable $created_at;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[Assert\DateTime]
    /**
     * date de clôture de la fiche, champ facultatif
     * @var DateTimeImmutable|null
     */
    private ?DateTimeImmutable $closed_at = null;

    #[ORM\ManyToOne(targetEntity: Parameter::class)]
    #[ORM\JoinColumn(name: 'network_id', referencedColumnName: 'id', nullable: true)]
    private ?Parameter $network = null;

    #[ORM\Column(type: Types::TEXT, length: 255, nullable: true)]
    #[Assert\Type('string')]
    /**
     * prénom de l'internaute, champ facultatif
     * @var string|null
     */
    private ?string $url = null;

    #[ORM\ManyToOne(targetEntity: Clientele::class, inversedBy: 'networks')]
    #[ORM\JoinColumn(name: 'network_id', referencedColumnName: 'id')]
    private ?Clientele $client = null;


    /**
     * @return integer|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->created_at;
    }

    /**
     * @param DateTimeImmutable $created_at
     * @return self
     */
    public function setCreatedAt(DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;
        return $this;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getClosedAt(): ?DateTimeImmutable
    {
        return $this->closed_at;
    }

    /**
     * @param DateTimeImmutable|null $closed_at
     * @return self
     */
    public function setClosedAt(?DateTimeImmutable $closed_at): self
    {
        $this->closed_at = $closed_at;
        return $this;
    }

    /**
     * @return Parameter|null
     */
    public function getNetwork(): mixed
    {
        return $this->network;
    }

    /**
     * @param Parameter $network
     * @return self
     */
    public function setNetwork(Parameter $network): self
    {
        $this->network = $network;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return self
     */
    public function setUrl(string $url): self
    {
        $this->url = $url;
        return $this;
    }

    /**
     * Get the value of client
     */
    public function getClient(): ?Clientele
    {
        return $this->client;
    }

    /**
     * Set the value of client
     */
    public function setClient(Clientele $client): self
    {
        $this->client = $client;
        return $this;
    }
}