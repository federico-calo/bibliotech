<?php
namespace App\Core;

use App\Enum\UserRole;
use App\Services\AuthCookie;
use App\Services\RefreshTokenManager;

class AuthManager
{
    private readonly \PDO $pdo;

    public function __construct(Database $database, private readonly Message $message)
    {
        $this->pdo = $database->getConnection();
    }

    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    public static function isAdmin(): bool
    {
        return isset($_SESSION['role']) && $_SESSION['role'] === UserRole::ADMIN->value;
    }

    public static function logout(): void
    {
        session_unset();
        session_destroy();
        AuthCookie::clearAuthCookie();
        Message::setMessage('Déconnecté !', 'success');
    }

    /**
     * @throws \Exception
     */
    public function registerUser(string $login, string $password, string $role = UserRole::DEFAULT->value): void
    {
        $login = trim($login);
        if ($login === '' || $password === '') {
            throw new \Exception('Le login et le mot de passe ne peuvent pas être vides.');
        }
        $hashedPassword = password_hash($password, PASSWORD_ARGON2ID);
        $stmt = $this->pdo->prepare("INSERT INTO users (login, password, role) VALUES (:login, :password, :role)");
        $stmt->bindParam(':login', $login);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':role', $role);
        if (!$stmt->execute()) {
            throw new \Exception('Impossible d\'enregistrer l\'utilisateur.');
        }
        $this->message->setMessage('Bienvenue dans la Bibliothech !', 'success');
    }

    /**
     * @throws \Exception
     */
    public function loginUser(string $login, string $password): bool
    {
        $stmt = $this->pdo->prepare("SELECT id, password, role, login FROM users WHERE login = :login");
        $stmt->bindParam(':login', $login);
        $stmt->execute();
        $user = $stmt->fetch();
        if ($user && password_verify($password, (string) $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['logged_in'] = true;
            $_SESSION['role'] = $user['role'];
            $this->message->setMessage('Connecté', 'success');
            AuthCookie::setAuthCookie($user['id']);

            return true;
        }
        $this->message->setMessage('Echec de la connexion', 'warning');
        return false;
    }

    /**
     * @throws \Exception
     */
    public function updateUser($userData): void
    {
        if (empty($userData['login']) || empty($userData['pwd'])) {
            throw new \Exception('Le login, le mot de passe et le mail ne peuvent pas être vides.');
        }

        try {
            $userId = $userData['id'] ?? null;
            $firstname = strip_tags((string) ($userData['firstname'] ?? '')) ?: null;
            $lastname = strip_tags((string) ($userData['lastname'] ?? '')) ?: null;
            $role = strip_tags((string) ($userData['role'] ?? '')) ?: null;
            $mail = strip_tags((string) ($userData['mail'] ?? '')) ?: '';
            $login = trim((string) ($userData['login'] ?? ''));
            $password = password_hash((string) $userData['pwd'], PASSWORD_ARGON2ID);
            if ($userId !== null) {
                $stmt = $this->pdo->prepare(
                    "
                UPDATE users 
                SET 
                    login = :login, 
                    firstname = :firstname, 
                    lastname = :lastname, 
                    role = :role, 
                    mail = :mail, 
                    password = :password 
                WHERE id = :id
            "
                );

                $stmt->execute(
                    [
                    ':firstname' => $firstname,
                    ':lastname' => $lastname,
                    ':role' => $role,
                    ':mail' => $mail,
                    ':login' => $login,
                    ':password' => $password,
                    ':id' => $userId,
                    ]
                );

                $this->message->setMessage('Utilisateur mis à jour avec succès !', 'success');
            } else {
                throw new \Exception('ID utilisateur manquant.');
            }
        } catch (\PDOException $e) {
            $this->message->setMessage('Erreur : ' . $e->getMessage(), 'danger');
        }
    }

    public function getRefreshTokenManager($secretKey): RefreshTokenManager
    {
        return new RefreshTokenManager($this->pdo, $secretKey);
    }

}
