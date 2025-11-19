<?php

namespace App\Repository;

use App\Core\Database;
use App\Core\Message;
use App\Entity\User;
use PDO;

class UserRepository implements UserRepositoryInterface
{
    /**
     * @var PDO
     */
    private readonly \PDO $pdo;

    /**
     * @param Database $Database
     * @param Message  $message
     */
    public function __construct(
        private readonly Database $Database,
        private readonly Message $message
    ) {
        $this->pdo = $Database->getConnection();
    }

    /**
     * @param  int    $page
     * @param  string $search
     * @return array
     */
    public function findAll(int $page, string $search): array
    {
        $offset = ($page - 1) * self::PER_PAGE;
        $query = 'SELECT * FROM ' . self::TABLE;
        if (!empty($search)) {
            $query .= ' WHERE ' . self::SEARCH_FIELD . ' LIKE :search';
        }
        $query .= ' LIMIT :limit OFFSET :offset';
        $stmt = $this->pdo->prepare($query);
        if (!empty($search)) {
            $stmt->bindValue(':search', '%' . $search . '%');
        }
        $stmt->bindValue(':limit', self::PER_PAGE, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, self::ENTITY_CLASS);

        return $stmt->fetchAll();
    }

    /**
     * @param  int $id
     * @return User|null
     */
    public function findById(int $id): ?User
    {
        $query = 'SELECT * FROM ' . self::TABLE . ' WHERE id = :id';
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, self::ENTITY_CLASS);

        return $stmt->fetch();
    }

    /**
     * @param  int $id
     * @return void
     */
    public function delete(int $id): void
    {
        try {
            $this->pdo->beginTransaction();
            $stmt = $this->pdo->prepare('DELETE FROM ' . self::TABLE . ' WHERE id = :id');
            $stmt->execute([':id' => $id]);
            $this->pdo->commit();
            $this->message->setMessage(self::ENTITY_NAME . ' supprimé avec succès !', 'success');
            header("Location: /");
            exit;
        } catch (\PDOException $e) {
            $this->pdo->rollBack();
            $this->message->setMessage($e->getMessage(), 'danger');
            return;
        }
    }

    /**
     * @param  string $search
     * @return int
     */
    public function countAll(string $search = ''): int
    {
        $query = 'SELECT COUNT(*) AS total FROM ' . self::TABLE;
        if (!empty($search)) {
            $query .= ' WHERE login LIKE :search';
        }

        $stmt = $this->pdo->prepare($query);
        if (!empty($search)) {
            $stmt->bindValue(':search', '%' . $search . '%');
        }

        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        return (int) $result['total'];
    }

    /**
     * @return false|array
     */
    public function currentUser(): false|array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM ' . self::TABLE . ' WHERE id = :id');
        $stmt->bindParam(':id', $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $result === false ? [] : $result ;
    }
}
