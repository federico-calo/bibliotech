<?php

namespace App\Controller;

use App\Core\AuthManager;
use App\Core\Settings;
use App\Repository\BookRepository;
use App\Services\JsonResponse;

class ApiController
{
    /**
     * @param AuthManager    $authManager
     * @param BookRepository $bookRepository
     * @param JsonResponse   $jsonResponse
     */
    public function __construct(
        private AuthManager $authManager,
        private BookRepository $bookRepository,
        private JsonResponse $jsonResponse,
    ) {
    }

    /**
     * @param  $params
     * @return never
     */
    public function apiGet($params): never
    {
        $id = $params['id'] ?? null;
        echo $this->jsonResponse->json($this->bookRepository->getBook($id));
        exit;
    }

    /**
     * @param  $params
     * @return never
     */
    public function apiGetAll($params): never
    {
        $page = isset($params['page']) && is_numeric($params['page']) ? (int)$params['page'] : 1;
        $tagName = $params['tag'] ?? '';
        $search = $params['search'] ?? '';
        $booksData = $this->bookRepository->getBooks($tagName, $search, $page);
        $books = $booksData['books'];
        $totalBooks = $booksData['total_books'];
        $totalPages = $booksData['total_pages'];
        echo $this->jsonResponse->json(
            [
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_books' => $totalBooks,
            'books' => $books
            ]
        );
        exit;
    }

    /**
     * @param  $params
     * @return void
     * @throws \DateMalformedStringException
     */
    public function apiPost($params): void
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $manager = $this->authManager->getRefreshTokenManager(Settings::get('hashKey'));
        $token = $params['headers']["Api_key"] ?? null;
        $validate = $manager->validateToken($token, $input['user_id']);
        if (!$validate) {
            echo $this->jsonResponse->json(['error' => 'Unauthorized: Invalid API Key'], 401);
            exit;
        }
        if ($this->bookRepository->insertBook($input)) {
            echo $this->jsonResponse->json(['message' => 'Book created'], 201);
            exit;
        } else {
            echo $this->jsonResponse->json(['error' => 'Invalid data'], 400);
            exit;
        }
    }

    /**
     * @param  $params
     * @return void
     * @throws \Random\RandomException
     */
    public function apiToken($params)
    {
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input['user_id'])) {
            echo $this->jsonResponse->json(
                ['error' => 'Forbidden: Missing user_id parameter'],
                403
            );
            exit;
        }
        $manager = $this->authManager->getRefreshTokenManager(Settings::get('hashKey'));
        $currentToken = $params['headers']["Api_key"] ?? null;
        $isValidOldToken = $manager->compareToken($currentToken, $input['user_id']);
        if (!$isValidOldToken) {
            echo $this->jsonResponse->json(['error' => 'Unauthorized: Invalid API Key'], 401);
            exit;
        }
        $newToken = $manager->assignToken($input['user_id']);
        if (!empty($newToken)) {
            echo $this->jsonResponse->json(
                [
                'message' => 'Token generated successfully',
                'token' => $newToken,
                ]
            );
            exit;
        }
        echo $this->jsonResponse->json(
            ['error' => 'Unable to generate token'],
            400
        );
        exit;
    }
}
