<?php

namespace Celtic34fr\ContactCore\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\TextType;
use Doctrine\DBAL\Types\DateTimeImmutableType;
use Celtic34fr\ContactCore\Repository\SuiviRepository;

#[ORM\Entity(repositoryClass: SuiviRepository::class)]
#[ORM\Table(name:'suivis')]
class Suivi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: DateTimeImmutableType::class)]
    private ?DateTimeImmutable $evt_at = null;

    #[ORM\Column(type: TextType::class, length: 255)]
    private ?string $event = null;

    #[ORM\ManyToOne(inversedBy: 'events')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Clientele $client = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEvtAt(): ?DateTimeImmutable
    {
        return $this->evt_at;
    }

    public function setEvtAt(DateTimeImmutable $evt_at): self
    {
        $this->evt_at = $evt_at;
        return $this;
    }

    public function getEvent(): ?string
    {
        return $this->event;
    }

    public function setEvent(string $event): self
    {
        $this->event = $event;
        return $this;
    }

    public function getClient(): ?Clientele
    {
        return $this->client;
    }

    public function setClient(Clientele $client): self
    {
        $this->client = $client;
        return $this;
    }
}
