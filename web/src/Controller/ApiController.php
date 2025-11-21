<?php

namespace App\Controller;

use App\Core\AuthManager;
use App\Core\Settings;
use App\Repository\BookRepository;
use App\Services\JsonResponse;
use OpenApi\Attributes as OA;

#[OA\Info(version: '1.0', title: 'Bibliotech API')]
class ApiController
{
    public function __construct(
        private AuthManager $authManager,
        private BookRepository $bookRepository,
        private JsonResponse $jsonResponse,
    ) {
    }

    /**
     * @param $params
     * @return never
     */
    #[OA\Get(
        path: "/api/books/{id}",
        operationId: "apiGet",
        description: "Récupère les informations d’un livre par son ID unique.",
        tags: ["Livre"]
    )]
    #[OA\Parameter(
        name: "id",
        description: "Identifiant du livre",
        in: "path",
        required: true,
        schema: new OA\Schema(type: "integer", example: 12)
    )]
    #[OA\Response(
        response: 200,
        description: "Détails du livre récupérés avec succès.",
        content: new OA\JsonContent(ref: "#/components/schemas/Book")
    )]
    #[OA\Response(response: 404, description: "Livre non trouvé.")]
    public function apiGet($params): never
    {
        $id = $params['id'] ?? null;
        echo $this->jsonResponse->json($this->bookRepository->getBook($id));
        exit;
    }

    /**
     * @param $params
     * @return never
     */
    #[OA\Get(
        path: "/api/books",
        operationId: "apiGetAll",
        description: "Récupère une liste paginée de livres avec filtres facultatifs.",
        tags: ["Livre"]
    )]
    #[OA\Parameter(
        name: "page",
        description: "Numéro de page.",
        in: "query",
        required: false,
        schema: new OA\Schema(type: "integer", example: 1)
    )]
    #[OA\Parameter(
        name: "tag",
        description: "Filtre les livres par tag.",
        in: "query",
        required: false,
        schema: new OA\Schema(type: "string", example: "fantasy")
    )]
    #[OA\Parameter(
        name: "search",
        description: "Recherche texte sur auteur/titre/résumé.",
        in: "query",
        required: false,
        schema: new OA\Schema(type: "string", example: "Harry Potter")
    )]
    #[OA\Response(
        response: 200,
        description: "Liste des livres récupérée avec succès.",
        content: new OA\JsonContent(ref: "#/components/schemas/BooksPaginated")
    )]
    public function apiGetAll($params): never
    {
        $page = isset($params['page']) && is_numeric($params['page']) ? (int)$params['page'] : 1;
        $tagName = $params['tag'] ?? '';
        $search = $params['search'] ?? '';
        $booksData = $this->bookRepository->getBooks($tagName, $search, $page);

        echo $this->jsonResponse->json([
            'current_page' => $page,
            'total_pages'  => $booksData['total_pages'],
            'total_books'  => $booksData['total_books'],
            'books'        => $booksData['books']
        ]);
        exit;
    }

    /**
     * @param $params
     * @return void
     * @throws \DateMalformedStringException
     */
    #[OA\Post(
        path: "/api/books",
        operationId: "apiPost",
        description: "Crée un livre (authentification par API Key requise).",
        tags: ["Livre"]
    )]
    #[OA\Parameter(
        name: "Api_key",
        description: "Clé API permettant d'autoriser la création.",
        in: "header",
        required: true,
        schema: new OA\Schema(type: "string", example: "8f2c4c0a-3e65-47d2-8939-b3c9dcfd1ea7")
    )]
    #[OA\RequestBody(
        description: "Données du livre à créer.",
        required: true,
        content: new OA\JsonContent(ref: "#/components/schemas/BookInput")
    )]
    #[OA\Response(response: 201, description: "Livre créé avec succès.")]
    #[OA\Response(response: 400, description: "Données invalides.")]
    #[OA\Response(response: 401, description: "Clé API invalide.")]
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
        }
        echo $this->jsonResponse->json(['error' => 'Invalid data'], 400);
        exit;
    }

    /**
     * @param $params
     * @return void
     * @throws \Exception
     */
    #[OA\Post(
        path: "/api/token",
        operationId: "apiToken",
        description: "Génère un nouveau token d'API suite à validation d'un précédent.",
        tags: ["Authentification"]
    )]
    #[OA\RequestBody(
        description: "Identifiant utilisateur pour lequel générer un nouveau token.",
        required: true,
        content: new OA\JsonContent(
            required: ["user_id"],
            properties: [
                new OA\Property(property: "user_id", type: "integer", example: 42)
            ],
            type: "object"
        )
    )]
    #[OA\Parameter(
        name: "Api_key",
        description: "Clé API actuelle à valider avant régénération.",
        in: "header",
        required: true,
        schema: new OA\Schema(type: "string")
    )]
    #[OA\Response(
        response: 200,
        description: "Nouveau token généré avec succès.",
        content: new OA\JsonContent(ref: "#/components/schemas/TokenResponse")
    )]
    #[OA\Response(response: 401, description: "Clé API invalide.")]
    #[OA\Response(response: 403, description: "Paramètre manquant.")]
    #[OA\Response(response: 400, description: "Impossible de générer un token.")]
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
