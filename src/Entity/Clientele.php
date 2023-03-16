<?php

namespace Celtic34fr\ContactCore\Entity;

use Celtic34fr\ContactCore\DBAL\Types\CustomerEnumType;
use Celtic34fr\ContactCore\Enum\CustomerEnums;
use Celtic34fr\ContactCore\Repository\ClienteleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

#[ORM\Entity(repositoryClass: ClienteleRepository::class)]
class Clientele
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $courriel = null;

    #[ORM\Column(type: 'text', length: 255, nullable: true)]
    private ?string $type = null;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: CrmCliInfos::class)]
    private Collection $cliInfos;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: CrmSuivi::class, orphanRemoval: true)]
    private Collection $events;

    #[Pure] public function __construct()
    {
        $this->events = new ArrayCollection();
        $this->cliInfos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCourriel(): ?string
    {
        return $this->courriel;
    }

    public function setCourriel(string $courriel): self
    {
        $this->courriel = $courriel;
        return $this;
    }

    #[Pure] public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): bool|self
    {
        if (CustomerEnums::isValid($type)) {
            $this->type = $type;
            return $this;
        }
        return false;
    }

    public function getCliInfos(): ArrayCollection|Collection
    {
        return $this->cliInfos;
    }

    public function addCliInfos(CrmCliInfos $cliInfos): self
    {
        if (!$this->cliInfos->contains($cliInfos)) {
            $this->cliInfos->add($cliInfos);
            $cliInfos->setClient($this);
        }
        return $this;
    }

    public function removeCliInfos(CrmCliInfos $cliInfos): self
    {
        if ($this->cliInfos->removeElement($cliInfos)) {
            // set the owning side to null (unless already changed)
            if ($cliInfos->getClient() === $this) {
                $cliInfos->setClient(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, CrmSuivi>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(CrmSuivi $event): self
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
            $event->setClient($this);
        }
        return $this;
    }

    public function removeEvent(CrmSuivi $event): self
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getClient() === $this) {
                $event->setClient(null);
            }
        }
        return $this;
    }
}