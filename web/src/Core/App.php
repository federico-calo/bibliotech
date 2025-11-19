<?php

namespace App\Core;

use App\Core\Routing\Router;
use App\Core\Routing\RouterManager;
use App\Repository\BookRepository;
use App\Repository\UserRepository;
use App\Services\AuthCookie;
use App\Services\OpenLibraryClient;
use App\Services\RedisHelper;
use GuzzleHttp\Client;

final class App
{
    protected static Settings $settings;

    /**
     * @throws \Exception
     */
    public function __construct(
        private AuthManager $authManager,
        private View $view,
        private RouterManager $routerManager,
        private InputProcessor $inputProcessor,
        private BookRepository $bookRepository,
        private UserRepository $userRepository
    ) {
    }

    /**
     * @throws \Exception
     */
    public static function initialize($settings): self
    {
        new Settings($settings);
        $factory = new AppFactory(
            database: new Database(),
            message: new Message(),
            openLibraryClient: new OpenLibraryClient(new Client()),
            redisHelper: new RedisHelper()
        );
        return new static(
            authManager: $factory->createAuthManager(),
            view: new View(),
            routerManager: new RouterManager(
                new Router(Settings::get('routingPath')),
                new ClassResolver(),
                $factory->createAuthManager()
            ),
            inputProcessor: new InputProcessor(),
            bookRepository: $factory->createBookRepository(),
            userRepository: $factory->createUserRepository()
        );
    }

    /**
     * @throws \Exception
     */
    public function run(): void
    {
        $this->refreshAuth();
        $requestPath = parse_url((string) $_SERVER['REQUEST_URI'])['path'];
        $input = $this->inputProcessor->processServerInput();
        $this->routerManager->handle($requestPath, $input);
    }

    /**
     * @return void
     */
    private function refreshAuth(): void
    {
        if (!empty(AuthCookie::getAuthenticatedUserId()) && !AuthManager::isLoggedIn()) {
            $loggedInUser = $this->userRepository->findById(AuthCookie::getAuthenticatedUserId());
            if ($loggedInUser) {
                $_SESSION['user_id'] = $loggedInUser->getId();
                $_SESSION['logged_in'] = true;
                $_SESSION['role'] = $loggedInUser->getRole();
            }
        }
    }
}
