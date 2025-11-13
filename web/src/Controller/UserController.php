<?php

namespace App\Controller;

use App\Core\AuthManager;
use App\Core\Routing\Router;
use App\Core\Settings;
use App\Core\View;
use App\Enum\UserRole;
use App\Repository\UserRepository;
use App\Services\CsrfTokenManager;

class UserController
{

    const string REGISTER_TEMPLATE_NAME = 'user/user-register';

    const string LOGIN_TEMPLATE_NAME = 'user/user-login';

    const string EDIT_TEMPLATE_NAME = 'user/user-edit';

    const string API_TEMPLATE_NAME = 'user/user-api';

    public function __construct(
        private AuthManager    $authManager,
        private UserRepository $userRepository,
        private View $view,
        private CsrfTokenManager $csrfTokenManager,
    ) {
    }

    /**
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
     * @throws \Random\RandomException
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
     * @throws \Random\RandomException
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


    public function logout(): void
    {
        $this->authManager->logout();
        \header("Location: /");
    }

}