<?php

namespace App\Entity;

class Book
{

    public function __construct(
        private ?int $id = null,
        private ?string $title = null,
        private ?string $author = null,
        private ?string $isbn = null,
        private ?string $summary = null,
        private ?string $createdAt = null,
        private ?string $updatedAt = null
    ) {
    }

    public function __set(string $name, $value): void
    {
        $property = lcfirst(str_replace('_', '', ucwords($name, '_')));
        if (property_exists($this, $property)) {
            $this->$property = $value;
        }
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function getIsbn(): string
    {
        return $this->isbn;
    }

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
