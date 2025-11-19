<?php

namespace App\Services;

class RefreshTokenManager implements TokenManagerInterface
{
    /**
     * @param \PDO        $pdo
     * @param string|null $secretKey
     */
    public function __construct(private \PDO $pdo, private ?string $secretKey = null)
    {
    }

    /**
     * @throws \Exception
     */
    public static function generateToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    /**
     * @throws \Exception
     */
    public function assignToken(int $userId): string
    {
        $token = static::generateToken();
        $hashedToken = hash_hmac('sha256', $token, base64_encode((string) $this->secretKey));
        $expiresAt = (new \DateTime('+30 days'))->format('Y-m-d H:i:s');
        $stmt = $this->pdo->prepare(
            'UPDATE users SET token = :token, token_expires_at = :expires_at WHERE id = :id'
        );
        $stmt->execute(
            [
            ':token' => $hashedToken,
            ':expires_at' => $expiresAt,
            ':id' => $userId
            ]
        );

        return $token;
    }

    /**
     * @throws \DateMalformedStringException
     */
    public function validateToken(string $token, int $userId): bool
    {
        $stmt = $this->pdo->prepare('SELECT token, token_expires_at FROM users WHERE id = :id');
        $stmt->execute([':id' => $userId]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (
            !$user || !hash_equals($user['token'], $token)
            || new \DateTime($user['token_expires_at']) < new \DateTime()
        ) {
            return false;
        }

        return true;
    }

    /**
     * @param  string $token
     * @param  int    $userId
     * @return bool
     */
    public function compareToken(string $token, int $userId): bool
    {
        $stmt = $this->pdo->prepare('SELECT token FROM users WHERE id = :id');
        $stmt->execute([':id' => $userId]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$user || !hash_equals($user['token'], $token)) {
            return false;
        }

        return true;
    }
}
