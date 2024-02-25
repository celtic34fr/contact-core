<?php

namespace Celtic34fr\ContactCore\Entity;

use Celtic34fr\ContactCore\Entity\CliInfos;
use Celtic34fr\ContactCore\Entity\CliSocialNetwork;
use Celtic34fr\ContactCore\Entity\Parameter;
use Celtic34fr\ContactCore\Entity\Suivi;
use Celtic34fr\ContactCore\Enum\CustomerEnums;
use Celtic34fr\ContactCore\Repository\ClienteleRepository;
use Celtic34fr\ContactCore\Validator\Constraint as CustomAssert;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ClienteleRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(name:'clienteles')]
/**
 * classe Clientele : informations de base avec la relation Client/prospect/fournisseur/partenaire 
 * 
 * - created_at : date de création de la fiche relation
 * - updated_at : date de dernière modification d'information / ajout de suivi sur la fiche relation
 * - closed_at  : date de clôture de la fiche relation
 *      TODO : cette denière informations pourra faire l'objet de consultation particulière, historisation/purge de la base
 *      comme de donner la possibilité de restaurer une historisation d'information pour une relation
 * - courriel   : adresse courriel (@mail) de la relation
 * - type       : type de relation Client / Prospect / Fournisseur / Partenaire
 * - category   : catégorie associée au type de relation, lien vers la table Parameter, ManyToOne, structure RelationCategory
 *      TODO : à créer pour le type de relation 'CL' (Client) :
 *      - Particuliers
 *      - Organismes publics
 *      - Entreprises privées
 *      - Syndic
 *      - Collectivités
 * - cliInfos   : lien vers l'ensemble des informations détails de la relation, table CliInfos, OneToMany bidirectionnal
 * - events     : lien vers l'ensemble des événements relatifs à la relation, table Suivi, OneToMany bidirectionnal
 * - networks   : lien vers l'ensemble des pages sur les réseaux sociaux, table CliSocialNetwork, OneToMany bidirectionnal
 * - website    : URL site internet associé à la relation
 * 
 * TODO :
 * - commentaire, annotation sur la relation (condition de contact direct, limite dépôt commande, ...)
 * - secteur d'activité de la relation
 * 
 * => voir l'impact sur l'outils d'extraction pour publipostage, campagne d'information / publicité
 * => voir comment intégrer le filtrage des informations de suivi de relation dans les filtre de l'extraction
 */
class Clientele
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
     * date de dernière mise à jour, champ facultatif
     * @var DateTimeImmutable|null
     */
    private ?DateTimeImmutable $updated_at = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[Assert\DateTime]
    /**
     * date de clôture de la fiche, champ facultatif
     * @var DateTimeImmutable|null
     */
    private ?DateTimeImmutable $closed_at = null;

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

    #[ORM\Column(type: Types::TEXT, length: 255)]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Type('string')]
    #[CustomAssert\CustomerType]
    /**
     * type de l'internaute, Cf. Enum CustomerEnums, champ obligatoire
     * @var string
     */
    private string $type;

    #[ORM\ManyToOne(targetEntity: Parameter::class)]
    #[ORM\JoinColumn(name: 'category_id', referencedColumnName: 'id', nullable: true)]
    /**
     * categorie associée, Cf. RelationCategory
     * @var Parameter|null
     */
    private Parameter|null $category = null;

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

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: CliSocialNetwork::class, orphanRemoval: true)]
    #[ORM\JoinColumn(nullable: true)]
    /**
     * lien avec la table CliSocialNetwork : ensemble des pages sur les réseaux sociaux
     * @var Collection|null
     */
    private ?Collection $networks = null;

    #[ORM\Column(type: Types::TEXT, length: 255, nullable: true)]
    #[Assert\Type('string')]
    #[CustomAssert\CustomerType]
    /**
     * type de l'internaute, Cf. Enum CustomerEnums, champ obligatoire
     * @var string
     */
    private ?string $website = null;


    #[ORM\PrePersist]
    public function beforPersist(PrePersistEventArgs $eventArgs)
    {
        $this->created_at = new DateTimeImmutable('now');
    }

    #[ORM\PreUpdate]
    public function beforeUpdate(PreUpdateEventArgs $eventArgs)
    {
        $entity = $eventArgs->getObject();
        if (empty($entity->getUpdatedAt())) $this->updated_at = new DateTimeImmutable('now');
    }

    

    public function __construct()
    {
        $this->networks = new ArrayCollection();
        $this->events = new ArrayCollection();
        $this->cliInfos = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
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

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?DateTimeImmutable $updated_at): self
    {
        $this->updated_at = $updated_at;
        return $this;
    }

    public function getClosedAt(): ?DateTimeImmutable
    {
        return $this->closed_at;
    }

    public function setClosedAt(?DateTimeImmutable $closed_at): self
    {
        $this->closed_at = $closed_at;
        return $this;
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

    /**
     * Get the value of category
     */
    public function getCategory(): ?Parameter
    {
        return $this->category;
    }

    /**
     * Set the value of category
     */
    public function setCategory(?Parameter $category): self
    {
        $this->category = $category;

        return $this;
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
            //if ($cliInfos->getClient() === $this) {
            //    $cliInfos->setClient(null);
            //}
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
            //if ($event->getClient() === $this) {
            //    $event->setClient(null);
            //}
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

    /**
    * @return Collection<int, CliSocialNetwork>
    */
    public function getNetworks(): Collection
    {
        return $this->networks;
    }

    public function addNetwork(CliSocialNetwork $network): self
    {
        if (!$this->networks->contains($network)) {
            $this->networks->add($network);
            $network->setClient($this);
        }
        return $this;
    }

    public function removeNetwork(CliSocialNetwork $network): self
    {
        $this->networks->removeElement($network);
        return $this;
    }

    /**
     * Get the value of website
     */
    public function getWebsite(): ?string
    {
        return $this->website;
    }

    /**
     * Set the value of website
     */
    public function setWebsite(string $website): self
    {
        $this->website = $website;
        return $this;
    }
}