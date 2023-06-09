<?php

namespace Celtic34fr\ContactCore\Entity;

use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Celtic34fr\ContactCore\Enum\UtilitiesPJEnums;
use Celtic34fr\ContactCore\Repository\PieceJointeRepository;

#[ORM\Entity(repositoryClass: PieceJointeRepository::class)]
#[ORM\Table(name:'pieces_jointes')]
class PieceJointe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, length: 255, nullable: false)]
    private string $file_name;                      // nom du fichier origine

    #[ORM\Column(type: Types::TEXT, length: 255, nullable: false)]
    private string $file_mime;                      // type de fichier

    #[ORM\Column(type: Types::BLOB)]
    private $file_content = null;                   // contenu proprement dit du fichier

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $created_at = null; // date de création

    #[ORM\Column(type: Types::BOOLEAN, nullable: true)]
    private ?bool $tempo = null;                    // flag indiquant si le fichier est à garder (false) ou non (true)

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $utility = null;                // utilité de la pièce jointe : logo entreprise, image pour courriel ....

    #[ORM\Column(type: Types::TEXT, length: 255, nullable: true)]
    private ?string $file_size = null;                   // taille de la pièce jointe format normalisé

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
     * @return DateTimeImmutable|null
     */
    public function getDateCreated(): ?DateTimeImmutable
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
     * @return bool|null
     */
    public function getTempo(): ?bool
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
    public function getFileSize(): ?string
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
