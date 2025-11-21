<?php

namespace App\Entity;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Book",
    description: "Book entity",
    required: [],
    type: "object"
)]
class Book
{
    public function __construct(
        #[OA\Property(property: "id", description: "Unique ID of the book", type: "integer", example: 101, nullable: true)]
        private ?int $id = null,

        #[OA\Property(property: "title", description: "Book title", type: "string", example: "Harry Potter")]
        private ?string $title = null,

        #[OA\Property(property: "author", description: "Book author", type: "string", example: "J.K. Rowling")]
        private ?string $author = null,

        #[OA\Property(property: "isbn", description: "ISBN identifier", type: "string", example: "978-2070612758", nullable: true)]
        private ?string $isbn = null,

        #[OA\Property(property: "summary", description: "Short summary", type: "string", example: "A young wizard discovers his powers...", nullable: true)]
        private ?string $summary = null,

        #[OA\Property(property: "createdAt", description: "Creation timestamp", type: "string", format: "date-time", example: "2025-02-01T12:45:00Z", nullable: true)]
        private ?string $createdAt = null,

        #[OA\Property(property: "updatedAt", description: "Update timestamp", type: "string", format: "date-time", example: "2025-02-11T17:30:00Z", nullable: true)]
        private ?string $updatedAt = null,
    ) {}

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

