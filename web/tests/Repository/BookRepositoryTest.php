<?php

namespace Tests\Repository;

use App\Core\Database;
use App\Core\Message;
use App\Repository\BookRepository;
use App\Services\OpenLibraryClient;
use App\Services\RedisHelper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class BookRepositoryTest extends TestCase
{
    private Database&MockObject $database;
    private Message&MockObject $message;
    private OpenLibraryClient&MockObject $openLibraryClient;
    private RedisHelper&MockObject $redisHelper;
    private \PDO&MockObject $pdo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->database = $this->createMock(Database::class);
        $this->message = $this->createMock(Message::class);
        $this->openLibraryClient = $this->createMock(OpenLibraryClient::class);
        $this->redisHelper = $this->getMockBuilder(RedisHelper::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['get', 'set', 'clearCache', 'clearCacheFromPattern'])
            ->getMock();
        $this->pdo = $this->createMock(\PDO::class);
        $this->database->method('getConnection')->willReturn($this->pdo);
    }

    public function testGetBookReturnsCachedValue(): void
    {
        $bookId = 42;
        $cached = ['id' => $bookId, 'title' => 'Cached'];
        $this->redisHelper->expects(self::once())->method('get')->willReturn($cached);
        $repository = new BookRepository($this->database, $this->message, $this->openLibraryClient, $this->redisHelper);

        $result = $repository->getBook($bookId);

        $this->assertSame($cached, $result);
    }

    public function testGetBookReturnsEmptyArrayWhenNotFound(): void
    {
        $bookId = 404;
        $this->redisHelper->method('get')->willReturn(null);
        $repository = $this->getMockBuilder(BookRepository::class)
            ->setConstructorArgs([$this->database, $this->message, $this->openLibraryClient, $this->redisHelper])
            ->onlyMethods(['findById', 'findTagsByBookId'])
            ->getMock();
        $repository->method('findById')->willReturn(null);
        $repository->expects(self::never())->method('findTagsByBookId');

        $this->assertSame([], $repository->getBook($bookId));
    }
}
