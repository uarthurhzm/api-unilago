<?php

namespace App\Infrastructure\Http;

use App\Shared\Attributes\Route;
use ReflectionClass;
use ReflectionMethod;

class RouteDiscovery
{
    private static function registerFromController(string $controllerClass)
    {
        $reflection = new ReflectionClass($controllerClass);

        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            $attributes = $method->getAttributes(Route::class, \ReflectionAttribute::IS_INSTANCEOF);

            foreach ($attributes as $attribute) {
                $route = $attribute->newInstance();

                Router::add(
                    $route->method,
                    $route->path,
                    [$controllerClass, $method->getName()],
                    $route->middlewares
                );
            }
        }
    }

    public static function registerAll(array $controllers): void
    {
        foreach ($controllers as $controller) {
            self::registerFromController($controller);
        }
    }
}
