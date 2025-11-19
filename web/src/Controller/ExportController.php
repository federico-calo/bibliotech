<?php

namespace App\Controller;

use App\Core\AuthManager;
use App\Core\Routing\Router;
use App\Core\View;
use App\Repository\BookRepository;
use App\Services\CsvExporter;

class ExportController
{
    /**
     * @param AuthManager    $authManager
     * @param View           $view
     * @param BookRepository $bookRepository
     * @param CsvExporter    $csvExporter
     */
    public function __construct(
        private AuthManager $authManager,
        private View $view,
        private BookRepository $bookRepository,
        private CsvExporter $csvExporter,
    ) {
    }

    /**
     * @param  $params
     * @return void
     * @throws \Exception
     */
    public function export($params): void
    {
        if (!$this->authManager->isAdmin()) {
            Router::accessDenied();
        }
        $tagName = $params['tag'] ?? '';
        $search = $params['search'] ?? '';
        $booksData = $this->bookRepository->getBooks($tagName, $search);
        $books = $booksData['books'];
        $this->csvExporter->export($books, 'books.csv');
    }
}
