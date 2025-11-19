<?php

namespace App\Core;

use App\Repository\BookRepository;
use App\Repository\UserRepository;
use App\Services\OpenLibraryClient;
use App\Services\RedisHelper;

class AppFactory
{
    /**
     * @param Database          $database
     * @param Message           $message
     * @param OpenLibraryClient $openLibraryClient
     * @param RedisHelper       $redisHelper
     */
    public function __construct(
        private readonly Database $database,
        private readonly Message $message,
        private readonly OpenLibraryClient $openLibraryClient,
        private readonly RedisHelper $redisHelper,
    ) {
    }

    /**
     * @return BookRepository
     */
    public function createBookRepository(): BookRepository
    {
        return new BookRepository(
            $this->database,
            $this->message,
            $this->openLibraryClient,
            $this->redisHelper
        );
    }

    /**
     * @return AuthManager
     */
    public function createAuthManager(): AuthManager
    {
        return new AuthManager($this->database, $this->message);
    }

    /**
     * @return UserRepository
     */
    public function createUserRepository(): UserRepository
    {
        return new UserRepository($this->database, $this->message);
    }
}
