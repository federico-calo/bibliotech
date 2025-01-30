<?php

namespace App\Core;

use App\Repository\BookRepository;
use App\Repository\UserRepository;
use App\Services\OpenLibraryClient;
use App\Services\RedisHelper;

class AppFactory
{

    public function __construct(
        private readonly Database          $database,
        private readonly Message           $message,
        private readonly OpenLibraryClient $openLibraryClient,
        private readonly RedisHelper       $redisHelper,
    ) {
    }

    public function createBookRepository(): BookRepository
    {
        return new BookRepository(
            $this->database,
            $this->message,
            $this->openLibraryClient,
            $this->redisHelper
        );
    }

    public function createAuthManager(): AuthManager
    {
        return new AuthManager($this->database, $this->message);
    }

    public function createUserRepository(): UserRepository
    {
        return new UserRepository($this->database, $this->message);
    }

}
