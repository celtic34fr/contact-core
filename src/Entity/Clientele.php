<?php

namespace Celtic34fr\ContactCore\Entity;

use Celtic34fr\ContactCore\Enum\CustomerEnums;
use Celtic34fr\ContactCore\Repository\ClienteleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClienteleRepository::class)]
#[ORM\Table(name:'clienteles')]
class Clientele
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $courriel = null;

    #[ORM\Column(type: 'text', length: 255, nullable: true)]
    private ?string $type = null;   // Cf. Enum^CustomerEnums

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: CliInfos::class)]
    private Collection $cliInfos;   // information nom, prénom, tél. pouvant être multiple

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Suivi::class, orphanRemoval: true)]
    private Collection $events;     // lien avec la table Suivi : évènement sur l'entity internaute (client/prospect)

    public function __construct()
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

    public function getType(): ?string
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

    public function addCliInfos(CliInfos $cliInfos): self
    {
        if (!$this->cliInfos->contains($cliInfos)) {
            $this->cliInfos->add($cliInfos);
            $cliInfos->setClient($this);
        }
        return $this;
    }

    public function removeCliInfos(CliInfos $cliInfos): self
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
    * @return Collection<int, Suivi>
    */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Suivi $event): self
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
            $event->setClient($this);
        }
        return $this;
    }

    public function removeEvent(Suivi $event): self
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