<?php

namespace App\Entity;

class Book
{
    /**
     * @param int|null    $id
     * @param string|null $title
     * @param string|null $author
     * @param string|null $isbn
     * @param string|null $summary
     * @param string|null $createdAt
     * @param string|null $updatedAt
     */
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

    /**
     * @param  string $name
     * @param  $value
     * @return void
     */
    public function __set(string $name, $value): void
    {
        $property = lcfirst(str_replace('_', '', ucwords($name, '_')));
        if (property_exists($this, $property)) {
            $this->$property = $value;
        }
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getAuthor(): string
    {
        return $this->author;
    }

    /**
     * @return string
     */
    public function getIsbn(): string
    {
        return $this->isbn;
    }

    /**
     * @return string|null
     */
    public function getSummary(): ?string
    {
        return $this->summary;
    }

    /**
     * @return string|null
     */
    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    /**
     * @return string|null
     */
    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
