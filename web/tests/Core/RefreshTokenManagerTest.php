<?php

namespace Tests\Core;

use App\Services\RefreshTokenManager;
use PHPUnit\Framework\TestCase;

class RefreshTokenManagerTest extends TestCase
{
    private \PDO $mockPdo;
    private RefreshTokenManager $tokenManager;

    /**
     * @var string
     */
    private string $secretKey;

    /**
     * @var string
     */
    private string $validToken;

    /**
     * @return void
     * @throws \PHPUnit\Framework\MockObject\Exception
     * @throws \Random\RandomException
     */
    protected function setUp(): void
    {
        $this->mockPdo = $this->createMock(\PDO::class);
        $this->secretKey = bin2hex(random_bytes(32));
        $this->validToken = bin2hex(random_bytes(32));
        $this->tokenManager = new RefreshTokenManager($this->mockPdo, $this->secretKey);
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testCompareTokenWithValidToken(): void
    {
        $userId = 1;
        $hashedToken = \hash_hmac('sha256', $this->validToken, \base64_encode($this->secretKey));
        $mockStatement = $this->createMock(\PDOStatement::class);
        $mockStatement->method('execute')
            ->with([':id' => $userId]);
        $mockStatement->method('fetch')
            ->willReturn(['token' => $hashedToken]);
        $this->mockPdo->method('prepare')
            ->with('SELECT token FROM users WHERE id = :id')
            ->willReturn($mockStatement);
        $tokenManager = new RefreshTokenManager($this->mockPdo, $this->secretKey);
        $result = $tokenManager->compareToken($hashedToken, $userId);
        $this->assertTrue($result, 'Le token doit être valide.');
    }


    /**
     * @return void
     * @throws \PHPUnit\Framework\MockObject\Exception
     * @throws \Random\RandomException
     */
    public function testCompareTokenWithInvalidToken(): void
    {
        $userId = 1;
        $invalidToken = \bin2hex(\random_bytes(32));
        $mockStatement = $this->createMock(\PDOStatement::class);
        $mockStatement->method('execute')
            ->with([':id' => $userId]);
        $mockStatement->method('fetch')
            ->willReturn(['token' => \bin2hex(\random_bytes(32))]);
        $this->mockPdo->method('prepare')
            ->with('SELECT token FROM users WHERE id = :id')
            ->willReturn($mockStatement);
        $result = $this->tokenManager->compareToken($invalidToken, $userId);
        $this->assertFalse($result, 'Le token doit être invalide.');
    }


    /**
     * @return void
     * @throws \DateMalformedStringException
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testValidateTokenWithValidToken(): void
    {
        $userId = 1;
        $hashedToken = \hash_hmac('sha256', $this->validToken, \base64_encode($this->secretKey));
        $futureDate = (new \DateTime('+1 day'))->format('Y-m-d H:i:s');
        $mockStatement = $this->createMock(\PDOStatement::class);
        $mockStatement->method('execute')
            ->with([':id' => $userId]);
        $mockStatement->method('fetch')
            ->willReturn(['token' => $hashedToken, 'token_expires_at' => $futureDate]);
        $this->mockPdo->method('prepare')
            ->with('SELECT token, token_expires_at FROM users WHERE id = :id')
            ->willReturn($mockStatement);
        $result = $this->tokenManager->validateToken($hashedToken, $userId);
        $this->assertTrue($result, 'Le token doit être valide.');
    }


    /**
     * @return void
     * @throws \DateMalformedStringException
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testValidateTokenWithExpiredToken(): void
    {
        $userId = 1;
        $hashedToken = \hash_hmac('sha256', $this->validToken, \base64_encode($this->secretKey));
        $pastDate = (new \DateTime('-1 day'))->format('Y-m-d H:i:s');
        $mockStatement = $this->createMock(\PDOStatement::class);
        $mockStatement->method('execute')
            ->with([':id' => $userId]);
        $mockStatement->method('fetch')
            ->willReturn(['token' => $hashedToken, 'token_expires_at' => $pastDate]);
        $this->mockPdo->method('prepare')
            ->with('SELECT token, token_expires_at FROM users WHERE id = :id')
            ->willReturn($mockStatement);
        $result = $this->tokenManager->validateToken($hashedToken, $userId);
        $this->assertFalse($result, 'Le token doit être invalide car expiré.');
    }

}
