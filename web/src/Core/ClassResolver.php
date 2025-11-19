<?php

namespace App\Core;

use App\Core\Routing\Route;

class ClassResolver
{
    /**
     * @throws \ReflectionException
     */
    public function resolve(Route $route, array $services = []): object
    {
        $services = \array_merge($services, $this->resolveDependencies($route->getDependencies()));
        $controllerFullClass = "App\\Controller\\" . $route->getController();
        return $this->resolveController($controllerFullClass, $services);
    }

    /**
     * @throws \ReflectionException
     */
    public function resolveDependencies(array $dependencies): array
    {
        $resolved = [];

        foreach ($dependencies as $dependencyClass) {
            $resolved[] = $this->resolveClass($dependencyClass);
        }

        return $resolved;
    }

    /**
     * @throws \ReflectionException
     * @throws \Exception
     */
    protected function resolveClass(string $className): object
    {
        try {
            $reflectionClass = new \ReflectionClass($className);
        } catch (\ReflectionException) {
            throw new \Exception("Impossible de résoudre la classe : {$className}");
        }
        $constructor = $reflectionClass->getConstructor();
        if (!$constructor) {
            return new $className();
        }
        $parameters = $constructor->getParameters();
        $dependencies = [];
        foreach ($parameters as $parameter) {
            $type = $parameter->getType();

            if ($type instanceof \ReflectionNamedType && !$type->isBuiltin()) {
                $dependencyClassName = $type->getName();
                $dependencies[] = $this->resolveClass($dependencyClassName);
            }
        }

        return $reflectionClass->newInstanceArgs($dependencies);
    }


    /**
     * @throws \ReflectionException
     */
    public function resolveController(string $controllerClass, array $services): object
    {
        $reflectionClass = new \ReflectionClass($controllerClass);
        $constructor = $reflectionClass->getConstructor();
        if (!$constructor) {
            return new $controllerClass();
        }
        $parameters = $constructor->getParameters();
        $resolvedDependencies = [];
        foreach ($parameters as $parameter) {
            $type = $parameter->getType();
            if ($type instanceof \ReflectionNamedType && !$type->isBuiltin()) {
                $dependencyClass = $type->getName();
                foreach ($services as $service) {
                    if ($service instanceof $dependencyClass) {
                        $resolvedDependencies[] = $service;
                        continue 2;
                    }
                }
                if (\class_exists($dependencyClass)) {
                    $resolvedDependencies[] = $this->resolveClass($dependencyClass);
                }
            } else {
                throw new \Exception("Impossible de résoudre la dépendance pour {$parameter->getName()}");
            }
        }

        return $reflectionClass->newInstanceArgs($resolvedDependencies);
    }
}
