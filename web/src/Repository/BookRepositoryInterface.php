<?php

namespace App\Repository;

use App\Entity\Book;

interface BookRepositoryInterface
{
    public function findAll(int $page, int $itemsPerPage, string $search): array;

    public function findById(int $id): ?Book;

    public function findByTagName(string $tagName, int $page, int $itemsPerPage): array;

    public function findTagsByBookId(int $bookId): array;

    public function getBooks(string $tagName = '', string $search = '', int $page = 1): array;

    public function getBook(int $bookId): array;

    public function save(Book $book): void;

    public function insertBook(array $bookData): ?string;

    public function updateBook(array $bookData): void;

    public function deleteBook(array $bookData): void;

    public function addTags(int $bookId, string $tags): void;

    public function getPaginationQueryStrings(int $page, int $totalItems, int $itemsPerPage, string $search, string $tag): array;

}
