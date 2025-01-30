<?php

namespace App\Controller;

use App\Core\AuthManager;
use App\Core\Settings;
use App\Core\View;
use App\Repository\BookRepository;
use App\Services\CsrfTokenManager;

class BookController
{

    public function __construct(
        private AuthManager    $authManager,
        private BookRepository $bookRepository,
        private View $view,
        private ?CsrfTokenManager $csrfTokenManager
    ) {
    }

    const string VIEW_TEMPLATE_NAME = 'book/book';

    const string EDIT_TEMPLATE_NAME = 'book/book-edit';

    const string DELETE_TEMPLATE_NAME = 'book/book-delete';

    /**
     * @throws \Exception
     */
    public function view($params): void
    {
        $templatePath = Settings::getTemplatePath(self::VIEW_TEMPLATE_NAME);
        if (!file_exists($templatePath)) {
            throw new \Exception("Template non trouvé : " . $templatePath);
        }
        $id = $params['id'] ?? null;
        $book = !empty($id) ? $this->bookRepository->getBook($id) : [];

        $this->view->render(
            $templatePath,
            [
            'isAdmin' => $this->authManager->isAdmin(),
            'isLoggedIn' => $this->authManager->isLoggedIn(),
            'book' => $book
            ]
        );
    }

    /**
     * @throws \Random\RandomException
     * @throws \Exception
     */
    public function edit($params, $method): void
    {
        $templatePath = Settings::getTemplatePath(self::EDIT_TEMPLATE_NAME);
        if (!file_exists($templatePath)) {
            throw new \Exception("Template non trouvé : " . $templatePath);
        }
        $id = $params['id'] ?? null;
        if ($params !== null && $method == 'POST') {
            $token = $params['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
            if (!empty($id) && $this->csrfTokenManager->validateToken($token)) {
                $this->bookRepository->updateBook($params);
            }
            else {
                $this->bookRepository->insertBook($params);
            }
            header("Location: /");
            exit;
        }
        $book = !empty($id) ? $this->bookRepository->getBook($id) : [];
        $title = isset($book['title']) ? 'Modifier le livre ' . $book['title'] : 'Publier le livre';

        View::render(
            $templatePath,
            [
                'isAdmin' => $this->authManager->isAdmin(),
                'isLoggedIn' => $this->authManager->isLoggedIn(),
                'book' => !empty($id) ? $this->bookRepository->getBook($id) : [],
                'title' => $title,
                'csrfToken' => $this->csrfTokenManager->getToken()
            ]
        );
    }

    /**
     * @throws \Exception
     */
    public function delete($params, $method): void
    {
        $templatePath = Settings::getTemplatePath(self::DELETE_TEMPLATE_NAME);
        if (!file_exists($templatePath)) {
            throw new \Exception("Template non trouvé : " . $templatePath);
        }
        $id = $params['id'] ?? null;
        if ($params !== null && $method == 'POST') {
            if (!empty($id)) {
                $this->bookRepository->deleteBook($params);
            }
            header("Location: /");
            exit;
        }
        View::render(
            $templatePath,
            [
                'isAdmin' => $this->authManager->isAdmin(),
                'isLoggedIn' => $this->authManager->isLoggedIn(),
                'book' => !empty($id) ? $this->bookRepository->getBook($id) : [],
                'csrfToken' => $this->csrfTokenManager->getToken()
            ]
        );
    }

}