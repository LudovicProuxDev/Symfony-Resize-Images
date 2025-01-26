<?php

namespace App\Entity;

use App\Repository\DocumentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DocumentRepository::class)]
class Document
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $originalName = null;

    #[ORM\Column(length: 255)]
    private ?string $originalSize = null;

    #[ORM\Column(length: 255)]
    private ?string $newName = null;

    #[ORM\Column(length: 255)]
    private ?string $newSize = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOriginalName(): ?string
    {
        return $this->originalName;
    }

    public function setOriginalName(string $originalName): static
    {
        $this->originalName = $originalName;

        return $this;
    }

    public function getOriginalSize(): ?string
    {
        return $this->originalSize;
    }

    public function setOriginalSize(string $originalSize): static
    {
        $this->originalSize = $originalSize;

        return $this;
    }

    public function getNewName(): ?string
    {
        return $this->newName;
    }

    public function setNewName(string $newName): static
    {
        $this->newName = $newName;

        return $this;
    }

    public function getNewSize(): ?string
    {
        return $this->newSize;
    }

    public function setNewSize(string $newSize): static
    {
        $this->newSize = $newSize;

        return $this;
    }
}
