<?php

namespace App\Entity;

class Tag
{

    public function __construct(
        private ?int $id = null,
        private ?string $name = null
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }

}
