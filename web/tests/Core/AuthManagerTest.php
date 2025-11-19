<?php

namespace Tests\Core;

use App\Core\AuthManager;
use App\Core\Database;
use App\Core\Message;
use App\Core\Settings;
use App\Enum\UserRole;
use App\Services\AuthCookie;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AuthManagerTest extends TestCase
{
    /**
     * @var \PDO&MockObject
     */
    private \PDO $pdo;

    /**
     * @var AuthManager
     */
    private AuthManager $authManager;

    /**
     * @return void
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $_SESSION = [];
        Settings::set('hashKey', str_repeat('a', 64));
        $this->pdo = $this->createMock(\PDO::class);
        $database = $this->createMock(Database::class);
        $database->method('getConnection')->willReturn($this->pdo);
        $this->authManager = new AuthManager($database, new Message());
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testRegisterUserPersistsCredentials(): void
    {
        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->method('execute')->willReturn(true);
        $this->pdo->method('prepare')->willReturn($stmt);
        $this->authManager->registerUser('Alice', 'SuperSecret123!', UserRole::ADMIN->value);
        $this->assertEquals(
            ['messageText' => 'Bienvenue dans la Bibliothech !', 'messageType' => 'success'],
            $_SESSION['message']
        );
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testLoginUserWithValidCredentials(): void
    {
        $passwordHash = password_hash('Secret123!', PASSWORD_ARGON2ID);
        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('fetch')->willReturn(
            [
            'id' => 5,
            'password' => $passwordHash,
            'role' => UserRole::ADMIN->value,
            'login' => 'alice'
            ]
        );
        $this->pdo->method('prepare')->willReturn($stmt);
        AuthCookie::clearAuthCookie();
        $result = $this->authManager->loginUser('alice', 'Secret123!');
        $this->assertTrue($result);
        $this->assertEquals(5, $_SESSION['user_id']);
        $this->assertTrue($_SESSION['logged_in']);
        $this->assertEquals(UserRole::ADMIN->value, $_SESSION['role']);
        $this->assertEquals(
            ['messageText' => 'Connecté', 'messageType' => 'success'],
            $_SESSION['message']
        );
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testLoginUserWithInvalidCredentials(): void
    {
        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('fetch')->willReturn(false);

        $this->pdo->method('prepare')->willReturn($stmt);

        $result = $this->authManager->loginUser('alice', 'wrong');

        $this->assertFalse($result);
        $this->assertEquals(
            ['messageText' => 'Echec de la connexion', 'messageType' => 'warning'],
            $_SESSION['message']
        );
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testUpdateUserResetsPasswordWithArgon2Id(): void
    {
        $stmt = $this->createMock(\PDOStatement::class);
        $stmt->method('execute')->willReturn(true);
        $this->pdo->method('prepare')->willReturn($stmt);
        $this->authManager->updateUser(
            [
            'id' => 1,
            'login' => 'admin',
            'pwd' => 'StrongPassword!42',
            'firstname' => 'Admin',
            'lastname' => 'User',
            'role' => UserRole::ADMIN->value,
            'mail' => 'admin@example.test'
            ]
        );
        $this->assertEquals(
            ['messageText' => 'Utilisateur mis à jour avec succès !', 'messageType' => 'success'],
            $_SESSION['message']
        );
    }
}
