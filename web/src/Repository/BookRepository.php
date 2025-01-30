<?php

namespace App\Repository;

use App\Core\Database;
use App\Core\Message;
use App\Entity\Book;
use App\Entity\Tag;
use App\Services\OpenLibraryClient;
use App\Services\RedisHelper;
use PDO;

class BookRepository implements BookRepositoryInterface
{

    public const int BOOKS_PER_PAGE = 10;

    /**
     * @var \PDO
     */
    private readonly PDO $pdo;

    /**
     * Constructeur de BookRepository.
     *
     * @param Database $Database L'instance de la classe Database qui gère la connexion PDO.
     */
    public function __construct(
        private readonly Database $Database,
        private readonly Message $message,
        private OpenLibraryClient $openLibraryClient,
        private readonly RedisHelper $redisHelper,
    ) {
        $this->pdo = $Database->getConnection();
    }
    /**
     * @inheritdoc
     */
    #[\Override]
    public function findAll(int $page, int $itemsPerPage, string $search): array
    {
        $offset = ($page - 1) * $itemsPerPage;
        $query = 'SELECT id, title, author, isbn, summary, created_at, updated_at
          FROM books';
        if (!empty($search)) {
            $query .= ' WHERE title LIKE :search';
        }
        $query .= ' LIMIT :limit OFFSET :offset';
        $stmt = $this->pdo->prepare($query);
        if (!empty($search)) {
            $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
        }
        $stmt->bindValue(':limit', $itemsPerPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, Book::class);
        $books = $stmt->fetchAll();

        return $books;
    }


    /**
     * @inheritdoc
     */
    #[\Override]
    public function findById(int $id): ?Book
    {
        $query = 'SELECT id, title, author, isbn, summary, created_at, 
       updated_at FROM books WHERE id = :id';
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, Book::class);

        return $stmt->fetch();
    }

    /**
     * Récupère les livres ayant un tag spécifique.
     *
     * @param  string $tagName Le nom du tag.
     * @return array Un tableau d'instances de \App\Entity\Book correspondant aux livres ayant le tag.
     */
    #[\Override]
    public function findByTagName(string $tagName, int $page, int $itemsPerPage): array
    {
        $offset = ($page - 1) * $itemsPerPage;
        $query = 'SELECT b.id, b.title, b.author, b.isbn, b.summary, b.created_at, 
       b.updated_at FROM books b
        INNER JOIN book_tags bt ON b.id = bt.book_id
        INNER JOIN tags t ON bt.tag_id = t.id
        WHERE t.name = :tag_name
    ';
        $query .= ' LIMIT :limit OFFSET :offset';
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':tag_name', $tagName);
        $stmt->bindValue(':limit', $itemsPerPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
            $stmt->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, Book::class);

        return $stmt->fetchAll();
    }


    /**
     * @inheritdoc
     */
    #[\Override]
    public function findTagsByBookId(int $bookId): array
    {
        $query = '
            SELECT t.id, t.name 
            FROM tags t
            INNER JOIN book_tags bt ON t.id = bt.tag_id
            WHERE bt.book_id = :book_id
        ';
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':book_id', $bookId, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, Tag::class);

        return $stmt->fetchAll();
    }

    /**
     * @param string $tagName
     * @param string $search
     * @param int    $page
     *
     * @return array
     */
    #[\Override]
    public function getBooks(string $tagName = '', string $search = '', int $page = 1): array
    {
        $itemsPerPage = self::BOOKS_PER_PAGE;
        $cacheKey = 'books:list:' . md5(json_encode([$page, $itemsPerPage, $search, $tagName]));
        $booksList = $this->redisHelper->get($cacheKey);
        if (empty($booksList['books'])) {
            if (!empty($tagName)) {
                $booksEntities = $this->findByTagName($tagName, $page, $itemsPerPage);
                $totalBooks = $this->countAllByTagName($tagName);
            }
            else {
                $booksEntities = $this->findAll($page, $itemsPerPage, $search);
                $totalBooks = $this->countAll($search);
            }
            $totalPages = ceil($totalBooks / $itemsPerPage);
            $books = [];
            $allBooksIds = array_map(fn($book) => $book->getId(), $booksEntities);
            $tagsByBookId = $this->findTagsByBookIds($allBooksIds);
            foreach ($booksEntities as $key => $bookEntity) {
                $bookTags = [];
                $books[$key] = $bookEntity->toArray();
                if (isset($tagsByBookId[$books[$key]['id']])) {
                    foreach ($tagsByBookId[$books[$key]['id']] as $tag) {
                        $bookTags[] = $tag;
                    }
                }
                $books[$key]['tags'] = $bookTags;
            }
            $booksList = [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_books' => $totalBooks,
                'books' => $books
            ];
            $this->redisHelper->set($cacheKey, $booksList);
        }

        return $booksList;
    }

    public function findTagsByBookIds(array $bookIds): array
    {
        if (empty($bookIds)) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($bookIds), '?'));
        $sql = "
        SELECT bt.book_id, t.name 
        FROM book_tags bt
        JOIN tags t ON bt.tag_id = t.id
        WHERE bt.book_id IN ($placeholders)
    ";
        $stmt = $this->pdo->prepare($sql);
        foreach ($bookIds as $index => $bookId) {
            $stmt->bindValue($index + 1, $bookId, PDO::PARAM_INT);
        }
        $stmt->execute();
        $tagsByBookId = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $tagsByBookId[$row['book_id']][] = $row['name'];
        }

        return $tagsByBookId;
    }

    /**
     * @param $bookId
     *
     * @return array
     */
    #[\Override]
    public function getBook($bookId): array
    {
        $cacheKey = 'books:get:' . md5((string) $bookId);
        $book = $this->redisHelper->get($cacheKey);
        if (empty($book)) {
            $book = $this->findById($bookId)->toArray();
            $tags = $this->findTagsByBookId($bookId);
            foreach ($tags as $tag) {
                $book['tags'][] = $tag->getName();
            }
            $book['data'] = $this->openLibraryClient->getBookLink($book['isbn']);
            $this->redisHelper->set($cacheKey, $book);
        }

        return $book;
    }

    /**
     * @param \App\Entity\Book $book
     *
     * @return void
     */
    #[\Override]
    public function save(Book $book): void
    {
        if ($book->getId() === null) {
            $this->insertBook($book);
        }
        else {
            $this->updateBook($book);
        }
        $this->redisHelper->clearCacheFromPattern('books:list:*');
    }

    /**
     * @param array $bookData
     *
     * @return string|null
     */
    #[\Override]
    public function insertBook(array $bookData): ?string
    {
        try {
            $title = $bookData['title'] ?? '';
            $author = $bookData['author'] ?? '';
            $isbn = $bookData['isbn'] ?? '';
            $summary = $bookData['summary'] ?? '';
            $tags = $bookData['tags'] ?? '';
            if (!empty($isbn) && !empty($title)) {
                $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM books WHERE isbn = :isbn");
                $stmt->execute([':isbn' => $isbn]);
                if ($stmt->fetchColumn() > 0) {
                    $this->message->setMessage(
                        'ISBN déjà existant. Veuillez vérifier les informations du livre.',
                        'warning'
                    );
                    return null;
                }
                $sql = "INSERT INTO books (title, author, isbn, summary) VALUES (:title, :author, :isbn, :summary)";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(
                    [
                    ':title' => strip_tags((string) $title),
                    ':author' => strip_tags((string) $author),
                    ':isbn' => $isbn,
                    ':summary' => strip_tags((string) $summary),
                    ]
                );
                $bookId = $this->pdo->lastInsertId();
                $this->addTags($bookId, $tags);
                $this->message->setMessage('Livre ajouté avec succès !', 'success');
            }
            return $bookId ?? null;

        } catch (\PDOException $e) {
            $this->message->setMessage('Erreur : ' . $e->getMessage(), 'danger');
            return null;
        }
    }

    /**
     * @param array $bookData
     *
     * @return void
     */
    #[\Override]
    public function updateBook(array $bookData): void
    {
        try {
            $bookId = $bookData['id'] ?? '';
            $title = $bookData['title'] ?? '';
            $author = $bookData['author'] ?? '';
            $isbn = $bookData['isbn'] ?? '';
            $summary = $bookData['summary'] ?? '';
            $tags = $bookData['tags'] ?? '';

            if (!empty($isbn) && !empty($title)) {
                $stmt = $this->pdo->prepare(
                    "
            UPDATE books 
            SET title = :title, author = :author, isbn = :isbn, summary = :summary
            WHERE id = :book_id
        "
                );
                $stmt->execute(
                    [
                    ':title' => strip_tags((string) $title),
                    ':author' => strip_tags((string) $author),
                    ':isbn' => $isbn,
                    ':summary' => strip_tags((string) $summary),
                    ':book_id' => $bookId,
                    ]
                );
                $stmt = $this->pdo->prepare("DELETE FROM book_tags WHERE book_id = :book_id");
                $stmt->execute([':book_id' => $bookId]);
                $this->addTags($bookId, $tags);
                $this->message->setMessage('Livre mis à jour avec succès !', 'success');
                $this->redisHelper->clearCache('books:get:' . md5((string) $bookId));
            }
            return;

        } catch (\PDOException $e) {
            $this->message->setMessage('Erreur : ' . $e->getMessage(), 'danger');
            return;
        }
    }

    /**
     * @param array $bookData
     *
     * @return void
     */
    #[\Override]
    public function deleteBook(array $bookData): void
    {
        $bookId = $bookData['id'] ?? '';
        try {
            $this->pdo->beginTransaction();
            $stmt = $this->pdo->prepare("DELETE FROM book_tags WHERE book_id = :book_id");
            $stmt->execute([':book_id' => $bookId]);
            $stmt = $this->pdo->prepare("DELETE FROM books WHERE id = :book_id");
            $stmt->execute([':book_id' => $bookId]);
            $this->pdo->commit();
            $this->message->setMessage('Livre supprimé avec succès !', 'success');
            header("Location: /");

            exit;

        } catch (\PDOException $e) {
            $this->pdo->rollBack();
            $this->message->setMessage($e->getMessage(), 'danger');
            return;
        }
    }

    /**
     * @param $bookId
     * @param $tags
     *
     * @return void
     */
    #[\Override]
    public function addTags($bookId, $tags): void
    {
        $tagArray = array_map('trim', preg_split('/[,|]/', (string) $tags));
        foreach ($tagArray as $tagName) {
            if (!empty($tagName)) {
                $stmt = $this->pdo->prepare("SELECT id FROM tags WHERE name = :name");
                $stmt->execute([':name' => $tagName]);
                $tag = $stmt->fetch();
                $tagId = $tag ? $tag['id'] : $this->insertTag($tagName);
                $stmt = $this->pdo->prepare("INSERT INTO book_tags (book_id, tag_id) VALUES (:book_id, :tag_id)");
                $stmt->execute([':book_id' => $bookId, ':tag_id' => $tagId]);
            }
        }
    }

    /**
     * @param string $tagName
     *
     * @return false|string
     */
    private function insertTag(string $tagName): false|string
    {
        $stmt = $this->pdo->prepare("INSERT INTO tags (name) VALUES (:name)");
        $stmt->execute([':name' => $tagName]);

        return $this->pdo->lastInsertId();
    }

    /**
     * @param string $search
     *
     * @return int
     */
    public function countAll(string $search = ''): int
    {
        $query = 'SELECT COUNT(*) AS total FROM books';
        if (!empty($search)) {
            $query .= ' WHERE title LIKE :search';
        }

        $stmt = $this->pdo->prepare($query);
        if (!empty($search)) {
            $stmt->bindValue(':search', '%' . $search . '%');
        }

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int) $result['total'];
    }


    public function countAllByTagName(string $tagName = '', string $search = ''): int
    {
        $query = 'SELECT COUNT(*) AS total 
              FROM books b
              INNER JOIN book_tags bt ON b.id = bt.book_id
              INNER JOIN tags t ON bt.tag_id = t.id
              WHERE t.name = :tag_name';
        if (!empty($search)) {
            $query .= ' AND b.title LIKE :search';
        }
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':tag_name', $tagName);
        if (!empty($search)) {
            $stmt->bindValue(':search', '%' . $search . '%');
        }
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int) $result['total'];
    }


    /**
     * @param int    $page
     * @param int    $totalItems
     * @param int    $itemsPerPage
     * @param string $search
     * @param string $tag
     *
     * @return array
     */
    #[\Override]
    public function getPaginationQueryStrings(
        int $page,
        int $totalItems,
        int $itemsPerPage,
        string $search,
        string $tag
    ): array {
        $nbPages = $totalItems/$itemsPerPage;
        $pagination = [];
        if ($page > 1) {
            $pagination['prev'] = '?page=' . $page-1;
            if (!empty($search)) {
                $pagination['prev'] .= '&search=' . $search;
            }
            if (!empty($tag)) {
                $pagination['prev'] .= '&tag=' . $tag;
            }
        }
        if ($page < $nbPages) {
            $pagination['next'] = '?page=' . $page+1;
            if (!empty($search)) {
                $pagination['next'] .= '&search=' . $search;
            }
            if (!empty($tag)) {
                $pagination['next'] .= '&tag=' . $tag;
            }
        }

        return $pagination;
    }

    public function getMonthlyBooks()
    {
        $this->pdo->setAttribute(
            \PDO::ATTR_ERRMODE,
            \PDO::ERRMODE_EXCEPTION
        );

        $sql = "
            SELECT 
                DATE_FORMAT(created_at, '%m/%Y') AS publicationMonth,
                COUNT(*) AS bookCount
            FROM 
                books
            GROUP BY 
                publicationMonth
            ORDER BY 
                publicationMonth ASC;
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

}