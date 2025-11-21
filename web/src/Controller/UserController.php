<?php

namespace App\Controller;

use App\Core\AuthManager;
use App\Core\Routing\Router;
use App\Core\Settings;
use App\Core\View;
use App\Enum\UserRole;
use App\Repository\UserRepository;
use App\Services\CsrfTokenManager;
use App\Services\Openapi;

class UserController
{
    public const string REGISTER_TEMPLATE_NAME = 'user/user-register';

    public const string LOGIN_TEMPLATE_NAME = 'user/user-login';

    public const string EDIT_TEMPLATE_NAME = 'user/user-edit';

    public const string API_TEMPLATE_NAME = 'user/user-api';

    public const string API_DOC_TEMPLATE_NAME = 'user/user-api-documentation';

    public const string OPENAPI_CACHE_PATH = '/../../openapi.cache.json';

    /**
     * @param AuthManager $authManager
     * @param UserRepository $userRepository
     * @param View $view
     * @param CsrfTokenManager $csrfTokenManager
     * @param Openapi $openapi
     */
    public function __construct(
        private AuthManager $authManager,
        private UserRepository $userRepository,
        private View $view,
        private CsrfTokenManager $csrfTokenManager,
        private Openapi $openapi,
    ) {
    }

    /**
     * @param $params
     * @return void
     * @throws \Exception
     */
    public function login($params): void
    {
        $templatePath = Settings::getTemplatePath(self::LOGIN_TEMPLATE_NAME);
        if (!file_exists($templatePath)) {
            throw new \Exception("Template non trouvé : " . $templatePath);
        }
        if (isset($params['login']) && isset($params['pwd'])) {
            $token = $params['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
            if (!$this->csrfTokenManager->validateToken((string) $token)) {
                Router::accessDenied();
            }
            $this->authManager->loginUser($params['login'], $params['pwd']);
            header("Location: /");
            exit;
        }
        $this->view->render(
            $templatePath,
            ['csrfToken' => $this->csrfTokenManager->getToken()]
        );
    }

    /**
     * @param $params
     * @return void
     * @throws \Exception
     */
    public function register($params): void
    {
        $templatePath = Settings::getTemplatePath(self::REGISTER_TEMPLATE_NAME);
        if (!file_exists($templatePath)) {
            throw new \Exception("Template non trouvé : " . $templatePath);
        }
        if (isset($params['login']) && isset($params['pwd'])) {
            $token = $params['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
            if (!$this->csrfTokenManager->validateToken((string) $token)) {
                Router::accessDenied();
            }
            $this->authManager->registerUser($params['login'], $params['pwd']);
            header("Location: /");
            exit;
        }

        $this->view->render(
            $templatePath,
            [
            'csrfToken' => $this->csrfTokenManager->getToken()
            ]
        );
    }

    /**
     * @param $params
     * @param $method
     * @return void
     * @throws \Exception
     */
    public function edit($params, $method): void
    {
        $templatePath = Settings::getTemplatePath(self::EDIT_TEMPLATE_NAME);
        if (!\file_exists($templatePath)) {
            throw new \Exception("Template non trouvé : " . $templatePath);
        }
        $currentUser = $this->userRepository->currentUser();
        if (empty($currentUser) || ($currentUser['role'] != UserRole::ADMIN->value && $currentUser['id'] != $params['id'])) {
            Router::accessDenied();
        }
        $id = $params['id'] ?? null;
        if ($params !== null && $method == 'POST') {
            $token = $params['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
            if (!empty($id) && $this->csrfTokenManager->validateToken($token)) {
                $this->authManager->updateUser($params);
                \header("Location: /");
                exit;
            }
            \header("Location: /");
            exit;
        }

        View::render(
            $templatePath,
            [
                'isAdmin' => $this->authManager->isAdmin(),
                'isLoggedIn' => $this->authManager->isLoggedIn(),
                'user' => $currentUser,
                'csrfToken' => $this->csrfTokenManager->getToken(),
                'userEditUrl' =>  '/user/' . $id . '/edit',
                'userApiUrl' =>  '/user/' . $id . '/api',
                'activeTab' => 'edit'
            ]
        );
    }

    /**
     * @param $params
     * @param $method
     * @return void
     * @throws \Exception
     */
    public function api($params, $method): void
    {
        $templatePath = Settings::getTemplatePath(self::API_TEMPLATE_NAME);
        if (!\file_exists($templatePath)) {
            throw new \Exception("Template non trouvé : " . $templatePath);
        }
        $currentUser = $this->userRepository->currentUser();
        if (empty($currentUser) || ($currentUser['role'] != UserRole::ADMIN->value && $currentUser['id'] != $params['id'])) {
            Router::accessDenied();
        }

        $id = $params['id'] ?? null;
        if ($params !== null && $method == 'POST') {
            $token = $params['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
            if (!empty($id)) {
                if (!$this->csrfTokenManager->validateToken((string) $token)) {
                    Router::accessDenied();
                }
                try {
                    $manager = $this->authManager->getRefreshTokenManager(Settings::get('hashKey'));
                    $manager->assignToken($currentUser['id']);
                } catch (\Exception $e) {
                    echo 'Erreur : ' . $e->getMessage();
                }
            }
        }

        View::render(
            $templatePath,
            [
            'isAdmin' => $this->authManager->isAdmin(),
            'isLoggedIn' => $this->authManager->isLoggedIn(),
            'user' => $currentUser,
            'csrfToken' => $this->csrfTokenManager->getToken(),
            'userEditUrl' =>  '/user/' . $id . '/edit',
            'userApiUrl' =>  '/user/' . $id . '/api',
            'activeTab' => 'api'
            ]
        );
    }

    /**
     * @param $params
     * @param $method
     * @throws \Exception
     */
    public function openapi($params, $method): void
    {
        $templatePath = Settings::getTemplatePath(self::API_DOC_TEMPLATE_NAME);
        if (!\file_exists($templatePath)) {
            throw new \Exception("Template non trouvé : " . $templatePath);
        }
        $currentUser = $this->userRepository->currentUser();
        if (empty($currentUser) || ($currentUser['role'] != UserRole::ADMIN->value && $currentUser['id'] != $params['id'])) {
            Router::accessDenied();
        }

        $id = $params['id'] ?? null;
        if ($params !== null && $method == 'POST') {
            $token = $params['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
            if (!empty($id)) {
                if (!$this->csrfTokenManager->validateToken((string) $token)) {
                    Router::accessDenied();
                }
                try {
                    $manager = $this->authManager->getRefreshTokenManager(Settings::get('hashKey'));
                    $manager->assignToken($currentUser['id']);
                } catch (\Exception $e) {
                    echo 'Erreur : ' . $e->getMessage();
                }
            }
        }
        $cacheFile = __DIR__ . self::OPENAPI_CACHE_PATH;
        if (file_exists($cacheFile)) {
            $openApiJson = file_get_contents($cacheFile);
        }
        else {
            $openApiJson = $this->openapi->generate([__DIR__ . '../Entity', __DIR__]);
            file_put_contents($cacheFile, $openApiJson);
        }
        header('Content-Type: application/json');
        echo $openApiJson;
    }

    /**
     * @param $params
     * @param $method
     * @throws \Exception
     */
    public function swagger($params, $method): void
    {
        $templatePath = Settings::getTemplatePath(self::API_DOC_TEMPLATE_NAME);
        if (!\file_exists($templatePath)) {
            throw new \Exception("Template non trouvé : " . $templatePath);
        }
        $currentUser = $this->userRepository->currentUser();
        if (empty($currentUser) || ($currentUser['role'] != UserRole::ADMIN->value && $currentUser['id'] != $params['id'])) {
            Router::accessDenied();
        }
        $id = $params['id'] ?? null;
        if ($params !== null && $method == 'POST') {
            $token = $params['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
            if (!empty($id)) {
                if (!$this->csrfTokenManager->validateToken((string) $token)) {
                    Router::accessDenied();
                }
                try {
                    $manager = $this->authManager->getRefreshTokenManager(Settings::get('hashKey'));
                    $manager->assignToken($currentUser['id']);
                } catch (\Exception $e) {
                    echo 'Erreur : ' . $e->getMessage();
                }
            }
        }
        $cacheFile = __DIR__ . self::OPENAPI_CACHE_PATH;
        //if (!file_exists($cacheFile)) {
            $openApiJson = $this->openapi->generate([__DIR__ . '/../Entity', __DIR__ . '/../Services', __DIR__]);
            file_put_contents($cacheFile, $openApiJson);
        //}
        View::render(
            $templatePath,
            [
                'isAdmin' => $this->authManager->isAdmin(),
                'isLoggedIn' => $this->authManager->isLoggedIn(),
                'user' => $currentUser,
                'csrfToken' => $this->csrfTokenManager->getToken(),
                'userEditUrl' =>  '/user/' . $id . '/edit',
                'userApiUrl' =>  '/user/' . $id . '/api',
                'activeTab' => 'api'
            ]
        );
    }


    /**
     * @return void
     */
    public function logout(): void
    {
        $this->authManager->logout();
        \header("Location: /");
    }
}
