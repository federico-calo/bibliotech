<?php

namespace App\Entity;

class BookTag
{

    public function __construct(
        private readonly int $bookId,
        private readonly int $tagId
    ) {
    }

    public function getBookId(): int
    {
        return $this->bookId;
    }

    public function getTagId(): int
    {
        return $this->tagId;
    }
}
