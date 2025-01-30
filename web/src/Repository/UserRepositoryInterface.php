<?php

namespace App\Repository;

use App\Entity\User;

interface UserRepositoryInterface
{

    public const int PER_PAGE = 10;

    public const string TABLE = 'users';

    public const ENTITY_CLASS = User::class;

    public const string ENTITY_NAME = 'Utilisateur';

    public const string SEARCH_FIELD = 'login';

    public function findAll(int $page, string $search): array;

    public function findById(int $id): ?User;

    public function delete(int $id): void;

    public function countAll(string $search = ''): int;

    public function currentUser(): false|array;

}
