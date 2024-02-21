<?php

namespace Celtic34fr\ContactCore\Entity;

use Celtic34fr\ContactCore\Enum\UtilitiesPJEnums;
use Celtic34fr\ContactCore\Repository\PieceJointeRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PieceJointeRepository::class)]
#[ORM\Table(name:'pieces_jointes')]
class PieceJointe
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
     * nom du fichier origine, champ obligatoire
     * @var string
     */
    private string $file_name;

    #[ORM\Column(type: Types::TEXT, length: 255, nullable: false)]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Type('string')]
    /**
     * type de fichier, chmap obligatoire
     * @var string
     */
    private string $file_mime;

    #[ORM\Column(type: Types::BLOB)]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Type('string')]
    /**
     * contenu proprement dit du fichier, champ obligatoire
     * @var string
     */
    private string $file_content;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\DateTime]
    /**
     * date de création, champ facultatif
     * @var DateTimeImmutable
     */
    private DateTimeImmutable $created_at;

    #[ORM\Column(type: Types::BOOLEAN, nullable: true)]
    #[Assert\Type('boolean')]
    /**
     * flag indiquant si le fichier est à garder (false) ou non (true), champ obligatoire initialisé à FAUX
     * @var boolean
     */
    private bool $tempo = false;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Type('string')]
    /**
     * utilité de la pièce jointe : logo entreprise, image pour courriel ...., champ facultatif
     * @var string|null
     */
    private ?string $utility = null;

    #[ORM\Column(type: Types::TEXT, length: 255, nullable: false)]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Type('string')]
    /**
     * taille de la pièce jointe format normalisé, champ facultatif
     * @var string
     */
    private string $file_size;


    
    /** 
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /** 
     * @return string
     */
    public function getFileName(): string
    {
        return $this->file_name;
    }

    /**
     * @param string $file_name
     * @return PieceJointe
     */
    public function setFileName(string $file_name): self
    {
        $this->file_name = $file_name;
        return $this;
    }

    /** 
     * @return string
     */
    public function getFileMime(): string
    {
        return $this->file_mime;
    }

    /** 
     * @param string $file_mime
     * @return PieceJointe
     */
    public function setFileMime(string $file_mime): self
    {
        $this->file_mime = $file_mime;
        return $this;
    }

    /** 
     * @return mixed
     */
    public function getFileContent()
    {
        return $this->file_content;
    }

    public function getFileContentBase64(): string
    {
        $content = stream_get_contents($this->file_content);
        return base64_encode($content);
    }

    /** 
     * @param $file_content
     * @return PieceJointe
     */
   public function setFileContent($file_content): self
    {
        $this->file_content = $file_content;
        return $this;
    }

    /** 
     * @return DateTimeImmutable
     */
    public function getDateCreated(): DateTimeImmutable
    {
        return $this->created_at;
    }

    /** 
     * @param DateTimeImmutable $created_at
     * @return PieceJointe
     */
    public function setDateCreated(DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;
        return $this;
    }

    /**
     * @return bool
     */
    public function getTempo(): bool
    {
        return $this->tempo;
    }

    /**
     * @param bool $tempo
     * @return PieceJointe
     */
    public function setTempo(bool $tempo): self
    {
        $this->tempo = $tempo;
        return $this;
    }

    /**
     * @return  string|null
     */ 
    public function getUtility(): ?string
    {
        return $this->utility;
    }

    /**
     * @param string $utility
     * @return  PieceJointe|bool
     */ 
    public function setUtility(string $utility): mixed
    {
        if (UtilitiesPJEnums::isValid($utility)) {
            $this->utility = $utility;
            return $this;
        }
        return false;
    }

    /**
     * Get the value of size
     */ 
    public function getFileSize(): string
    {
        return $this->file_size;
    }

    /**
     * Set the value of size
     *
     * @return  PieceJointe
     */ 
    public function setFileSize(string $fileSize): self
    {
        $this->file_size = $fileSize;
        return $this;
    }
}
