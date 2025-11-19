<?php

namespace App\Repository;

use App\Entity\Book;

interface BookRepositoryInterface
{
    /**
     * @param  int    $page
     * @param  int    $itemsPerPage
     * @param  string $search
     * @return array
     */
    public function findAll(int $page, int $itemsPerPage, string $search): array;

    /**
     * @param  int $id
     * @return Book|null
     */
    public function findById(int $id): ?Book;

    /**
     * @param  string $tagName
     * @param  int    $page
     * @param  int    $itemsPerPage
     * @return array
     */
    public function findByTagName(string $tagName, int $page, int $itemsPerPage): array;

    /**
     * @param  int $bookId
     * @return array
     */
    public function findTagsByBookId(int $bookId): array;

    /**
     * @param  string $tagName
     * @param  string $search
     * @param  int    $page
     * @return array
     */
    public function getBooks(string $tagName = '', string $search = '', int $page = 1): array;

    /**
     * @param  int $bookId
     * @return array
     */
    public function getBook(int $bookId): array;

    /**
     * @param  Book $book
     * @return void
     */
    public function save(Book $book): void;

    /**
     * @param  array $bookData
     * @return string|null
     */
    public function insertBook(array $bookData): ?string;

    /**
     * @param  array $bookData
     * @return void
     */
    public function updateBook(array $bookData): void;

    /**
     * @param  array $bookData
     * @return void
     */
    public function deleteBook(array $bookData): void;

    /**
     * @param  int    $bookId
     * @param  string $tags
     * @return void
     */
    public function addTags(int $bookId, string $tags): void;

    /**
     * @param  int    $page
     * @param  int    $totalItems
     * @param  int    $itemsPerPage
     * @param  string $search
     * @param  string $tag
     * @return array
     */
    public function getPaginationQueryStrings(int $page, int $totalItems, int $itemsPerPage, string $search, string $tag): array;
}
