<?php

namespace App\Core\Routing;

class Route
{

    public function __construct(
        private string $controller,
        private string $action,
        private array $dependencies = [],
        private array $params = []
    ) {
    }

    public function getController(): string
    {
        return $this->controller;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getDependencies(): array
    {
        return $this->dependencies;
    }

    public function getParams(): array
    {
        return $this->params;
    }

}
