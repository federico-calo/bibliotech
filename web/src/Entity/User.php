<?php

namespace App\Entity;

use App\Enum\UserRole;

class User
{
    public function __construct(
        private ?int $id = null,
        private ?string $login = null,
        private ?string $password = null,
        private ?string $mail = null,
        private ?string $firstname = null,
        private ?string $lastname = null,
        private ?string $token = null,
        private ?string $token_expires_at = null,
        private ?string $role = UserRole::DEFAULT->value
    ) {
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getLogin(): string
    {
        return $this->login;
    }

    /**
     * @return string
     */
    public function getMail(): string
    {
        return $this->mail;
    }

    /**
     * @param  string $mail
     * @return void
     */
    public function setMail(string $mail): void
    {
        if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("L'email fourni est invalide.");
        }
        $this->mail = $mail;
    }

    /**
     * @return string
     */
    public function getRole(): string
    {
        return $this->role;
    }

    /**
     * Modifier le rôle de l'utilisateur.
     *
     * @param UserRole $role
     *
     * @return void
     */
    public function setRole(UserRole $role): void
    {
        $this->role = $role;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param  string $password
     * @return void
     */
    public function setPassword(string $password): void
    {
        if (strlen($password) < 8) {
            throw new \InvalidArgumentException("Le mot de passe doit contenir au moins 8 caractères.");
        }
        $this->password = password_hash($password, PASSWORD_BCRYPT);
    }

    /**
     * @param  string $password
     * @return bool
     */
    public function verifyPassword(string $password): bool
    {
        return password_verify($password, (string) $this->password);
    }
}
