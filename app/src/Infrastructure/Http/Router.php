<?php

namespace App\Infrastructure\Http;

use App\Application\Middlewares\AuthMiddleware;
use App\Shared\Attributes\FromBody;
use App\Shared\Attributes\FromRoute;
use App\Shared\DTO\BaseDTO;
use App\Shared\Enum\MethodEnum;
use ReflectionMethod;
use ReflectionParameter;

class Router
{
    private static array $routes = [];

    private static function add(string $method, string $path, array $handler, bool $isPrivate): void
    {
        self::$routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
            'isPrivate' => $isPrivate
        ];
    }

    public static function GET(string $path, array $handler, bool $isPrivate = false): void
    {
        self::add(MethodEnum::GET->value, $path, $handler, $isPrivate);
    }

    public static function POST(string $path, array $handler, bool $isPrivate = false): void
    {
        self::add(MethodEnum::POST->value, $path, $handler, $isPrivate);
    }

    public static function PATCH(string $path, array $handler, bool $isPrivate = false): void
    {
        self::add(MethodEnum::PATCH->value, $path, $handler, $isPrivate);
    }

    public static function PUT(string $path, array $handler, bool $isPrivate = false): void
    {
        self::add(MethodEnum::PUT->value, $path, $handler, $isPrivate);
    }

    public static function DELETE(string $path, array $handler, bool $isPrivate = false): void
    {
        self::add(MethodEnum::DELETE->value, $path, $handler, $isPrivate);
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

                self::callHandler($route['handler'], $request);
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

    private static function callHandler(array $handler, Request $request): void
    {
        [$class, $method] = $handler;

        if (!class_exists($class)) {
            throw new \Exception("Controller {$class} não encontrado");
        }

        $controller = new $class();

        if (!method_exists($controller, $method)) {
            throw new \Exception("Método {$method} não encontrado no controller {$class}");
        }

        $route = self::$routes[array_search($handler, array_column(self::$routes, 'handler'))];

        if ($route['isPrivate'] && !AuthMiddleware::handle($request)) {
            Response::unauthorized('Acesso não autorizado');
            return;
        }

        $reflection = new ReflectionMethod($controller, $method);
        $parameters = $reflection->getParameters();

        $args = [];

        foreach ($parameters as $param) {
            $args[] = self::resolveParameter($param, $request);
        }

        call_user_func_array([$controller, $method], $args);

        // $controller->$method($request);
    }

    public static function getRoutes(): array
    {
        return self::$routes;
    }

    private static function resolveParameter(ReflectionParameter $param, Request $request): mixed
    {
        $type = $param->getType();

        // Se o tipo é Request, passa o request
        if ($type && $type->getName() === Request::class) {
            return $request;
        }

        // Verifica atributos do parâmetro
        $attributes = $param->getAttributes();

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
                        Response::badRequest(implode(', ', $errors));
                    }
                }

                return $dto;
            }

            // FromRoute - pega parâmetro da rota
            if ($attributeInstance instanceof FromRoute) {
                $paramName = $attributeInstance->name ?? $param->getName();
                $value = $request->getParams()[$paramName] ?? null;

                if ($value === null && !$param->isOptional()) {
                    Response::badRequest("Parâmetro '{$paramName}' é obrigatório");
                }

                return $value;
            }
        }

        // Se não tem atributo, retorna valor padrão ou null
        return $param->isDefaultValueAvailable() ? $param->getDefaultValue() : null;
    }
}
