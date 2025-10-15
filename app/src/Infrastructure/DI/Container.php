<?php

namespace App\Infrastructure\DI;

use ReflectionClass;
use ReflectionParameter;

class Container
{
    private static array $bindings = [];
    private static array $instances = [];

    public static function bind(string $abstract, callable|string|null $concrete = null): void
    {
        self::$bindings[$abstract] = $concrete ?? $abstract;
    }

    public static function singleton(string $abstract, callable|string|null $concrete = null): void
    {
        self::bind($abstract, $concrete);
        self::$instances[$abstract] = null;
    }

    public static function resolve(string $abstract): object
    {
        // Se é singleton e já foi instanciado, retorna
        if (isset(self::$instances[$abstract]) && self::$instances[$abstract] !== null) {
            return self::$instances[$abstract];
        }

        $concrete = self::$bindings[$abstract] ?? $abstract;

        if (is_callable($concrete)) {
            $instance = $concrete(new static());
        } else {
            $instance = self::build($concrete);
        }

        // Se é singleton, armazena a instância
        if (array_key_exists($abstract, self::$instances)) {
            self::$instances[$abstract] = $instance;
        }

        return $instance;
    }

    private static function build(string $class): object
    {
        $reflection = new ReflectionClass($class);

        if (!$reflection->isInstantiable()) {
            throw new \Exception("Classe {$class} não pode ser instanciada");
        }

        $constructor = $reflection->getConstructor();

        if (!$constructor) {
            return new $class();
        }

        $parameters = $constructor->getParameters();
        $dependencies = array_map(
            fn(ReflectionParameter $param) => self::resolveParameter($param),
            $parameters
        );

        return $reflection->newInstanceArgs($dependencies);
    }

    private static function resolveParameter(ReflectionParameter $parameter)
    {
        $type = $parameter->getType();

        if (!$type || $type->isBuiltin()) {
            if ($parameter->isDefaultValueAvailable()) {
                return $parameter->getDefaultValue();
            }
            throw new \Exception("Não é possível resolver o parâmetro {$parameter->getName()}");
        }

        return self::resolve($type->getName());
    }
}
