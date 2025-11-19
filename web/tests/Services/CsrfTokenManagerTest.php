<?php

namespace Tests\Services;

use App\Services\CsrfTokenManager;
use PHPUnit\Framework\TestCase;

class CsrfTokenManagerTest extends TestCase
{
    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $_SESSION = [];
        $_SERVER['REQUEST_METHOD'] = 'POST';
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testGenerateTokenStoresValueInSession(): void
    {
        $token = CsrfTokenManager::generateToken();
        $this->assertNotEmpty($token);
        $this->assertSame($token, $_SESSION['csrfToken']['token']);
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testGetTokenReturnsExistingToken(): void
    {
        $generated = CsrfTokenManager::generateToken();
        $retrieved = CsrfTokenManager::getToken();
        $this->assertSame($generated, $retrieved);
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testValidateTokenSuccess(): void
    {
        $token = CsrfTokenManager::generateToken();
        $this->assertTrue(CsrfTokenManager::validateToken($token));
        $this->assertNotSame($token, $_SESSION['csrfToken']['token'], 'Token should rotate after successful validation');
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testValidateTokenFailsWhenExpired(): void
    {
        $token = CsrfTokenManager::generateToken();
        $_SESSION['csrfToken']['expires'] = time() - 10;
        $this->assertFalse(CsrfTokenManager::validateToken($token));
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testValidateTokenAllowsSafeMethods(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $this->assertTrue(CsrfTokenManager::validateToken(''));
    }
}
