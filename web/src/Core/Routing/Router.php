<?php

namespace App\Core\Routing;

use App\Core\View;

class Router
{
    private array $routes;

    /**
     * @param string $routingPath
     */
    public function __construct(string $routingPath)
    {
        $this->routes = json_decode(file_get_contents($routingPath), true);
    }

    /**
     * @param  string $path
     * @param  $params
     * @return Route|null
     */
    public function match(string $path, $params): ?Route
    {
        $matchingRoute = $this->routes[$path] ?? null;
        if ($matchingRoute === null) {
            foreach ($this->routes as $routePath => $route) {
                if ($matchParams = $this->matchRoute($routePath, $path)) {
                    $params = array_merge($params, $matchParams);
                    if (!empty($params)) {
                        $matchingRoute = $route;
                    }
                }
            }
        }
        if (empty($matchingRoute)) {
            return null;
        }

        return new Route(
            $matchingRoute['controller'],
            $matchingRoute['action'],
            $matchingRoute['dependencies'] ?? [],
            $params ?? []
        );
    }

    /**
     * @param  string $routeUri
     * @param  string $requestUri
     * @return array|null
     */
    protected function matchRoute(string $routeUri, string $requestUri): ?array
    {
        $routeSegments = explode('/', trim($routeUri, '/'));
        $requestSegments = explode('/', trim($requestUri, '/'));
        if (count($routeSegments) !== count($requestSegments)) {
            return null;
        }
        $params = [];
        foreach ($routeSegments as $index => $routeSegment) {
            if (str_starts_with($routeSegment, '{') && str_ends_with($routeSegment, '}')) {
                $params[trim($routeSegment, '{}')] = $requestSegments[$index];
            } elseif ($routeSegment !== $requestSegments[$index]) {
                return null;
            }
        }

        return $params;
    }

    /**
     * @return void
     * @throws \Exception
     */
    public static function notFound(): void
    {
        http_response_code(404);
        View::render('errors/404');
        exit;
    }

    /**
     * @throws \Exception
     */
    public static function accessDenied(): void
    {
        http_response_code(403);
        View::render('errors/403');
        exit;
    }

    /**
     * @throws \Exception
     */
    public static function serverError(): void
    {
        http_response_code(500);
        View::render('errors/500');
        exit;
    }
}
