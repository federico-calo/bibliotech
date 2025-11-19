<?php

namespace App\Core;

class Database
{
    private readonly \PDO $pdo;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $settings = Settings::getSettings();
        $dbConnectionString = Settings::get('db');
        $mysqlUser = Settings::get('mysqlUser');
        $mysqlPassword = Settings::get('mysqlPassword');
        if (null === $dbConnectionString || null === $mysqlUser || null === $mysqlPassword) {
            throw new \Exception('Les paramètres de connexion à la base de données sont manquants.');
        }

        $this->pdo = new \PDO(
            $dbConnectionString,
            $mysqlUser,
            $mysqlPassword,
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            ]
        );
    }

    /**
     * @return \PDO
     */
    public function getConnection(): \PDO
    {
        return $this->pdo;
    }

    /**
     * @param  string $sql
     * @param  array  $params
     * @return \PDOStatement
     */
    public function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt;
    }
}
