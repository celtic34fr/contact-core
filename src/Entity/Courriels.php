<?php

namespace Celtic34fr\ContactCore\Entity;

use Celtic34fr\ContactCore\Enum\CourrielEnums;
use Celtic34fr\ContactCore\Enum\StatusCourrielEnums;
use Celtic34fr\ContactCore\Repository\CourrielsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

#[ORM\Entity(repositoryClass: CourrielsRepository::class)]
class Courriels
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $sujet = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?CliInfos $destinataire = null;

    #[ORM\Column]
    private array $context_courriel = [];

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $template_courriel = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $send_at = null;

    #[ORM\Column]
    private ?int $send_times = null;

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $send_status = null;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private array $pieces_jointes = [];

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $nature = null;

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

    public function setDestinataire(?CliInfos $destinataire): self
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

    public function setTemplateCourriel(?string $template_courriel): self
    {
        $this->template_courriel = $template_courriel;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;
        return $this;
    }

    public function getSendAt(): ?\DateTimeImmutable
    {
        return $this->send_at;
    }

    public function setSendAt(?\DateTimeImmutable $send_at): self
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

    public function setPiecesJointes(?array $pieces_jointes): self
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
