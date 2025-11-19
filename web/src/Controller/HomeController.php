<?php

namespace App\Controller;

use App\Core\AuthManager;
use App\Core\Settings;
use App\Core\View;
use App\Repository\BookRepository;

class HomeController
{
    /**
     * @param AuthManager    $authManager
     * @param BookRepository $bookRepository
     * @param View           $view
     */
    public function __construct(
        private AuthManager $authManager,
        private BookRepository $bookRepository,
        private View $view,
    ) {
    }

    public const string TEMPLATE_NAME = 'book/books-list';

    /**
     * @param  $params
     * @return void
     * @throws \Exception
     */
    public function index($params): void
    {
        $templatePath = Settings::getTemplatePath(self::TEMPLATE_NAME);
        if (!empty($params['tag'])) {
            $totalBooks = $this->bookRepository->countAllByTagName(
                tagName: $params['tag']
            );
        } else {
            $totalBooks = $this->bookRepository->countAll(
                search: $params['search'] ?? ''
            );
        }
        $pagination = $this->bookRepository->getPaginationQueryStrings(
            $params['page'] ?? 1,
            $totalBooks,
            BookRepository::BOOKS_PER_PAGE,
            $params['search'] ?? '',
            $params['tag'] ?? '',
        );
        $this->view->render(
            $templatePath,
            [
            'isAdmin' => $this->authManager->isAdmin(),
            'isLoggedIn' => $this->authManager->isLoggedIn(),
            'books' => $this->bookRepository->getBooks(
                tagName: $params['tag'] ?? '',
                search: $params['search'] ?? '',
                page: $params['page'] ?? 1,
            )['books'],
            'pagination' => $pagination,
            'userEditUrl' =>  isset($_SESSION['user_id']) ?
                'user/' . $_SESSION['user_id'] . '/edit' : '',
            'queryTag' => $params['tag'] ?? '',
            'querySearch' => $params['search'] ?? ''
            ],
        );
    }
}
