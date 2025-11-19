<?php

namespace App\Entity;

class BookTag
{
    /**
     * @param int $bookId
     * @param int $tagId
     */
    public function __construct(
        private readonly int $bookId,
        private readonly int $tagId
    ) {
    }

    /**
     * @return int
     */
    public function getBookId(): int
    {
        return $this->bookId;
    }

    /**
     * @return int
     */
    public function getTagId(): int
    {
        return $this->tagId;
    }
}
