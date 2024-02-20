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
    private ?string $sujet = null;                      // sujet donné par l'internaute

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\Type(CliInfos::class)]
    private ?CliInfos $destinataire = null;             // lien à l'internaute (info non fixe)

    #[ORM\Column(type: Types::ARRAY)]
    private array $context_courriel = [];               // variables pour génération du courriel

    #[ORM\Column(type: Types::TEXT, length: 255, nullable: true)]
    #[Assert\Type('string')]
    private ?string $template_courriel = null;          // modèle de rendu à utiliser

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: false)]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\DateTime]
    private ?DateTimeImmutable $created_at = null;     // date de création

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    #[Assert\DateTime]
    private ?DateTimeImmutable $send_at = null;        // date d'envoi

    #[ORM\Column(type: Types::INTEGER)]
    #[Assert\Type('integer')]
    private ?int $send_times = null;                    // nombre d'envoi total

    #[ORM\Column(type: Types::TEXT, nullable: false)]
    #[Assert\Type('string')]
    #[CustomAssert\CourrielStatusType]
    private ?string $send_status = null;                // statut d'envoi du courriel, cf Enum\StatusCourrielEnums

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private array $pieces_jointes = [];                 // ensble des pièces jointes (table PiecesJointes)

    #[ORM\Column(type: TypeS::TEXT, nullable: false)]
    #[Assert\Type('string')]
    #[CustomAssert\CourrielType]
    private ?string $nature = null;                     // nature, type de courriel, cf Enum\CourrielEnumes


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
