<?php

namespace App\Core\Routing;

class Route
{
    /**
     * @param string $controller
     * @param string $action
     * @param array  $dependencies
     * @param array  $params
     */
    public function __construct(
        private string $controller,
        private string $action,
        private array $dependencies = [],
        private array $params = []
    ) {
    }

    /**
     * @return string
     */
    public function getController(): string
    {
        return $this->controller;
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @return array
     */
    public function getDependencies(): array
    {
        return $this->dependencies;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }
}
