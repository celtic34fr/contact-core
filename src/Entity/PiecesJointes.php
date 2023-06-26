<?php

namespace Celtic34fr\ContactCore\Entity;

use Celtic34fr\ContactCore\Enum\UtilitiesPJEnums;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Celtic34fr\ContactCore\PiecesJointesRepository;

#[ORM\Entity(repositoryClass: PiecesJointesRepository::class)]
class PiecesJointes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: false)]
    private string $file_name;                      // nom du fichier origine

    #[ORM\Column(length: 255, nullable: false)]
    private string $file_mime;                      // type de fichier

    #[ORM\Column(type: 'blob')]
    private $file_content = null;                   // contenu proprement dit du fichier

    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $created_at = null; // date de création

    #[ORM\Column(nullable: true)]
    private ?bool $tempo = null;                    // flag indiquant si le fichier est à garder (false) ou non (true)

    #[ORM\Column(nullable: true)]
    private ?string $utility = null;                // utilité de la pièce jointe : logo entreprise, image pour courriel ....

    #[ORM\Column(length: 255)]
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
     * @return PiecesJointes
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
     * @return PiecesJointes
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
     * @return PiecesJointes
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
     * @return PiecesJointes
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
     * @return PiecesJointes
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
     * @return  PiecesJointes|bool
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
     * @return  PiecesJointes
     */ 
    public function setFileSize(string $fileSize): self
    {
        $this->file_size = $fileSize;
        return $this;
    }
}
