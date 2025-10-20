<?php

namespace App\Infrastructure\Http;

use App\Infrastructure\DI\Container;
use App\Shared\Attributes\FromBody;
use App\Shared\Attributes\FromRoute;
use App\Shared\DTO\BaseDTO;
use App\Shared\Enum\MethodEnum;
use ReflectionMethod;
use ReflectionParameter;

class Router
{
    private static array $routes = [];

    public static function add(string $method, string $path, array $handler, array $middlewares): void
    {
        self::$routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
            'middlewares' => $middlewares
        ];
    }

    public static function __callStatic($name, $arguments): void
    {
        $method = strtoupper($name);
        $allowedMethods = [
            MethodEnum::GET->value,
            MethodEnum::POST->value,
            MethodEnum::PATCH->value,
            MethodEnum::PUT->value,
            MethodEnum::DELETE->value,
        ];

        if (!in_array($method, $allowedMethods)) {
            throw new \BadMethodCallException("Método HTTP '{$method}' não suportado.");
        }

        $path = $arguments[0] ?? '';
        $handler = $arguments[1] ?? [];
        $middlewares = $arguments[2] ?? [];

        self::add($method, $path, $handler, $middlewares);
    }

    public static function dispatch(Request $request): void
    {
        $method = $request->getMethod();
        $uri = $request->getUri();

        // var_dump($uri);

        foreach (self::$routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $pattern = self::convertPathToRegex($route['path']);
            // var_dump($pattern);
            // var_dump(preg_match($pattern, $uri, $matches));

            if (preg_match($pattern, $uri, $matches)) {
                // var_dump($matches);
                $params = self::extractParams($route['path'], $matches);
                $request->setParams($params);

                self::callHandler($route, $request);
                return;
            }
        }

        Response::notFound('Rota não encontrada');
    }

    private static function convertPathToRegex(string $path): string
    {
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $path);
        return '#^' . $pattern . '$#';
    }

    private static function extractParams(string $path, array $matches): array
    {
        $params = [];
        preg_match_all('/\{([^}]+)\}/', $path, $paramNames);

        for ($i = 1; $i < count($matches); $i++) {
            $paramIndex = $i - 1; // Índice do parâmetro
            if (isset($paramNames[1][$paramIndex])) {
                $paramName = $paramNames[1][$paramIndex];
                $params[$paramName] = $matches[$i];
            }
        }

        return $params;
    }

    private static function callHandler(array $route, Request $request): void
    {
        [$class, $method] = $route['handler'];

        $controller = Container::resolve($class);
        $reflection = new ReflectionMethod($controller, $method);
        $parameters = self::resolveParameters($reflection, $request);

        if ($route['middlewares']) {
            foreach ($route['middlewares'] as $middleware) {
                $middlewareInstance = Container::resolve($middleware);
                [$data, $message, $status] = $middlewareInstance->handle($request);
                if (!$data) {
                    Response::$status($message);
                    return;
                }
            }
        }

        $reflection->invokeArgs($controller, $parameters);
    }

    public static function getRoutes(): array
    {
        return self::$routes;
    }

    private static function resolveParameters(ReflectionMethod $method, Request $request): array
    {
        $resolved = [];
        foreach ($method->getParameters() as $param) {
            $type = $param->getType();

            // Se o tipo é Request, passa o request
            if ($type && $type->getName() === Request::class) {
                $resolved[] = $request;
                continue;
            }

            // Verifica atributos do parâmetro
            $attributes = $param->getAttributes();

            $handled = false;
            foreach ($attributes as $attribute) {
                $attributeInstance = $attribute->newInstance();

                // FromBody - cria DTO do corpo da requisição
                if ($attributeInstance instanceof FromBody) {
                    $dtoClass = $type->getName();

                    if (!is_subclass_of($dtoClass, BaseDTO::class)) {
                        Response::error('Tipo de parâmetro inválido');
                    }

                    $dto = new $dtoClass($request->getData());

                    if ($attributeInstance->validate) {
                        $errors = $dto->validate();
                        if (!empty($errors)) {
                            Response::error(implode(', ', $errors));
                        }
                    }

                    $resolved[] = $dto;
                    $handled = true;
                    break;
                }

                // FromRoute - pega parâmetro da rota
                if ($attributeInstance instanceof FromRoute) {
                    $paramName = $attributeInstance->name ?? $param->getName();
                    $value = $request->getParams()[$paramName] ?? null;

                    if ($value === null && !$param->isOptional()) {
                        Response::badRequest("Parâmetro '{$paramName}' é obrigatório");
                    }

                    $resolved[] = $value;
                    $handled = true;
                    break;
                }
            }

            if ($handled) {
                continue;
            }

            // Se não tem atributo, retorna valor padrão ou null
            $resolved[] = $param->isDefaultValueAvailable() ? $param->getDefaultValue() : null;
        }
        return $resolved;
    }
}
