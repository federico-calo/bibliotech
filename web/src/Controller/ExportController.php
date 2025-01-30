<?php

namespace App\Controller;

use App\Core\AuthManager;
use App\Core\Routing\Router;
use App\Core\View;
use App\Repository\BookRepository;
use App\Services\CsvExporter;

class ExportController
{

    public function __construct(
        private AuthManager $authManager,
        private View $view,
        private BookRepository $bookRepository,
        private CsvExporter $csvExporter,
    ) {
    }

    public function export($params)
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