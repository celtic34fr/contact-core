<?php

namespace Celtic34fr\ContactCore\Entity;

use Celtic34fr\ContactCore\Enum\CourrielEnums;
use Celtic34fr\ContactCore\Enum\StatusCourrielEnums;
use Celtic34fr\ContactCore\Repository\CourrielRepository;
use Celtic34fr\ContactCore\Validator\Constraint as CustomAssert;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CourrielRepository::class)]
#[ORM\Table(name:'courriels')]
#[ORM\HasLifecycleCallbacks]
class Courriel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, length: 255)]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Type('string')]
    /**
     * sujet donné au courriel envoyé à l'internaute, champ obligatoire
     * @var string
     */
    private string $sujet;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\Type(type: CliInfos::class)]
    /**
     * lien à l'internaute (info non fixe), champ facultatif
     * @var CliInfos|null
     */
    private ?CliInfos $destinataire = null;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    #[Assert\Type(type: 'array')]
    /**
     * variables pour génération du courriel, champ facultatif
     * @var array
     */
    private array $context_courriel = [];

    #[ORM\Column(type: Types::TEXT, length: 255, nullable: true)]
    #[Assert\Type('string')]
    /**
     * modèle de rendu (TWIG principalement) à utiliser, champ facultatif
     * @var string|null
     */
    private ?string $template_courriel = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: false)]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\DateTime]
    /**
     * date de création de l'enregitrement, champ obligatoire
     * @var DateTimeImmutable
     */
    private DateTimeImmutable $created_at;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    #[Assert\DateTime]
    /**
     * date d'envoi du courriel à l'internaute, champ facultatif
     * @var DateTimeImmutable|null
     */
    private ?DateTimeImmutable $send_at = null;

    #[ORM\Column(type: Types::INTEGER, nullable: false)]
    #[Assert\Type('integer')]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    /**
     * nombre d'envoi total du courriel, champ obligatoire, valorisé à 0 en base
     * @var integer
     */
    private int $send_times = 0;

    #[ORM\Column(type: Types::TEXT, nullable: false)]
    #[Assert\Type('string')]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[CustomAssert\CourrielStatusType]
    /**
     * statut d'envoi du courriel, cf Enum\StatusCourrielEnums, champ obligatoire
     * @var string|null
     */
    private string $send_status;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    /**
     * ensble des pièces jointes (table PiecesJointes) [tableau], champ facultatif
     * @var array|null
     */
    private ?array $pieces_jointes = null;

    #[ORM\Column(type: Types::TEXT, nullable: false)]
    #[Assert\Type('string')]
    #[CustomAssert\CourrielType]
    /**
     * nature, type de courriel, cf Enum\CourrielEnumes, champ obligatoire
     * @var string
     */
    private string $nature;



    #[ORM\PrePersist]
    public function beforPersist(PrePersistEventArgs $eventArgs)
    {
        $entity = $eventArgs->getObject();
        $this->created_at = new DateTimeImmutable('now');
    }
    

    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSujet(): ?string
    {
        return $this->sujet;
    }

    public function setSujet(string $sujet): self
    {
        $this->sujet = $sujet;
        return $this;
    }

    public function getDestinataire(): ?CliInfos
    {
        return $this->destinataire;
    }

    public function setDestinataire(CliInfos $destinataire): self
    {
        $this->destinataire = $destinataire;
        return $this;
    }

    public function getContextCourriel(): array
    {
        return $this->context_courriel;
    }

    public function setContextCourriel(array $context_courriel): self
    {
        $this->context_courriel = $context_courriel;
        return $this;
    }

    public function getTemplateCourriel(): ?string
    {
        return $this->template_courriel;
    }

    public function setTemplateCourriel(string $template_courriel): self
    {
        $this->template_courriel = $template_courriel;
        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;
        return $this;
    }

    public function getSendAt(): ?DateTimeImmutable
    {
        return $this->send_at;
    }

    public function setSendAt(DateTimeImmutable $send_at): self
    {
        $this->send_at = $send_at;
        return $this;
    }

    public function getSendTimes(): ?int
    {
        return $this->send_times;
    }

    public function setSendTimes(int $send_times): self
    {
        $this->send_times = $send_times;
        return $this;
    }

    public function getSendStatus(): ?string
    {
        return $this->send_status;
    }

    public function setSendStatus(string $send_status): bool|self
    {
        if (StatusCourrielEnums::isValid($send_status)) {
            $this->send_status = $send_status;
            return $this;
        }
        return false;
    }

    public function getPiecesJointes(): array
    {
        return $this->pieces_jointes;
    }

    public function setPiecesJointes(array $pieces_jointes): self
    {
        $this->pieces_jointes = $pieces_jointes;
        return $this;
    }

     public function getNature(): ?string
    {
        return $this->nature;
    }

    public function setNature(string $nature): bool|self
    {
        if (CourrielEnums::isValid($nature)) {
            $this->nature = $nature;
            return $this;
        }
        return false;
    }
}
