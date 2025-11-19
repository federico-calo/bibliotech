<?php

namespace App\Entity;

class Tag
{
    /**
     * @param int|null    $id
     * @param string|null $name
     */
    public function __construct(
        private ?int $id = null,
        private ?string $name = null
    ) {
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
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
