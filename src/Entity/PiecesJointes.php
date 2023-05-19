<?php

namespace Celtic34fr\ContactCore\Entity;

use Celtic34fr\ContactCore\PiecesJointesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PiecesJointesRepository::class)]
class PiecesJointes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: false)]
    private string $file_name;

    #[ORM\Column(length: 255, nullable: false)]
    private string $file_mime;

    #[ORM\Column(type: 'blob')]
    private $file_content = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(nullable: true)]
    private ?bool $tempo = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFileName(): string
    {
        return $this->file_name;
    }

    public function setFileName(string $file_name): self
    {
        $this->file_name = $file_name;
        return $this;
    }

    public function getFileMime(): string
    {
        return $this->file_mime;
    }

    public function setFileMime(string $file_mime): self
    {
        $this->file_mime = $file_mime;
        return $this;
    }

    public function getFileContent()
    {
        return $this->file_content;
    }

    public function setFileContent($file_content): self
    {
        $this->file_content = $file_content;
        return $this;
    }

    public function getDateCreated(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setDateCreated(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;
        return $this;
    }

    public function getTempo(): ?bool
    {
        return $this->tempo;
    }

    public function setTempo(bool $tempo): self
    {
        $this->tempo = $tempo;
        return $this;
    }
}
