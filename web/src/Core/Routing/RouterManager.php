<?php

namespace App\Core\Routing;

use App\Core\AuthManager;
use App\Core\ClassResolver;

class RouterManager
{
    /**
     * @param Router        $router
     * @param ClassResolver $classResolver
     * @param AuthManager   $authManager
     */
    public function __construct(
        private Router $router,
        private ClassResolver $classResolver,
        private AuthManager $authManager
    ) {
    }

    /**
     * @param  string $path
     * @param  array  $input
     * @return void
     * @throws \ReflectionException
     */
    public function handle(string $path, array $input): void
    {
        $route = $this->router->match($path, $input) ?? $this->router->notFound();
        $controller = $this->classResolver->resolve($route, [$this->authManager]);
        $controller->{$route->getAction()}($route->getParams(), $_SERVER['REQUEST_METHOD']);
    }
}
