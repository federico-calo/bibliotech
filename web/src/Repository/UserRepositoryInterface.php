<?php

namespace App\Repository;

use App\Entity\User;

interface UserRepositoryInterface
{
    public const int PER_PAGE = 10;

    public const string TABLE = 'users';

    public const string ENTITY_CLASS = User::class;

    public const string ENTITY_NAME = 'Utilisateur';

    public const string SEARCH_FIELD = 'login';

    /**
     * @param  int    $page
     * @param  string $search
     * @return array
     */
    public function findAll(int $page, string $search): array;

    /**
     * @param  int $id
     * @return User|null
     */
    public function findById(int $id): ?User;

    /**
     * @param  int $id
     * @return void
     */
    public function delete(int $id): void;

    /**
     * @param  string $search
     * @return int
     */
    public function countAll(string $search = ''): int;

    /**
     * @return false|array
     */
    public function currentUser(): false|array;
}
