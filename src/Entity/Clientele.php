<?php

namespace Celtic34fr\ContactCore\Entity;

use Celtic34fr\ContactCore\Enum\CustomerEnums;
use Celtic34fr\ContactCore\Repository\ClienteleRepository;
use Celtic34fr\ContactCore\Validator\Constraint as CustomAssert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ClienteleRepository::class)]
#[ORM\Table(name:'clienteles')]
class Clientele
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, length: 255)]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Type('string')]
    #[Assert\Email(message: "L'adresse courriel {{ value }} est invalide")]
    /**
     * afresse courriel de l'internaute, champ obligatoire
     * @var string
     */
    private string $courriel;

    #[ORM\Column(type: Types::TEXT, length: 255, nullable: true)]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Type('string')]
    #[CustomAssert\CustomerType]
    /**
     * type de l'internaute, Cf. Enum CustomerEnums, champ obligatoire
     * @var string
     */
    private string $type;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: CliInfos::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    #[Assert\NotNull()]
    /**
     * informations sur l'internaute (nom, prénom, tél.) pouvant être multiple, au moins un occurance obligatoire
     * @var Collection
     */
    private Collection $cliInfos;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Suivi::class, orphanRemoval: true)]
    #[ORM\JoinColumn(nullable: true)]
    /**
     * lien avec la table Suivi : évènement sur l'entity internaute (client/prospect), champ facultatif
     * @var Collection|null
     */
    private ?Collection $events = null;


    
    public function __construct()
    {
        $this->events = new ArrayCollection();
        $this->cliInfos = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCourriel(): string
    {
        return $this->courriel;
    }

    public function setCourriel(string $courriel): self
    {
        $this->courriel = $courriel;
        return $this;
    }

    public function getType(): string
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
        $allCliInfos = $this->getCliInfos();

        if ($this->cliInfos->removeElement($cliInfos) && sizeof($allCliInfos) > 1) {
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

    /**
     * Search Contact Identity on criteria
     * @param array $criteria
     * @return boolean
     */
    public function isCliInfo(array $criteria):bool
    {
        $cliInfos = $this->getCliInfos();
        $name = [];
        foreach ($criteria as $key => $value) {
            switch($key) {
                case 'fullname':
                    $names = explode(" ", $value);
                    break;
                case 'firstname':
                    $name[1] = $value;
                    break;
                case 'lastname':
                    $name[0] = $value;
                    break;
            }
        }
        $found = false;

        if (!array_key_exists(0, $name) || empty($name[0])) return false;
        $onlyNom = !array_key_exists(1, $name) || empty($name[1]);

        foreach ($cliInfos as $cliInfo) {
            /** @var CliInfo $cliInfo */
            switch($onlyNom) {
                case true:
                    if ($name[0] == $cliInfo->getNom()) {
                        $found = true;
                    }
                    break;
                case false:
                    if ($name == [$cliInfo->getNom(), $cliInfo->getPrenom()]) {
                        $found = true;
                    }
                    break;
            }
            if ($found) break;
        }
        return $found;
    }
}