<?php

declare(strict_types = 1);

namespace Core;

use Core\Logger;
use Psr\Container\ContainerInterface;
use Core\Exceptions\Container\NotFoundException;
use Core\Exceptions\Container\ContainerException;

class Container implements ContainerInterface
{
    private array $entries = [];

    public function get(string $id)
    {
        if ($this->has($id)) {
            $entry = $this->entries[$id];

            if (is_callable($entry)) {
                return $entry($this);
            }

            $id = $entry;
        }

        return $this->resolve($id);
    }

    public function has(string $id): bool
    {
        return isset($this->entries[$id]);
    }

    public function set(string $id, callable|string $concrete): void
    {
        $this->entries[$id] = $concrete;
    }

    public function resolve(string $id)
    {
        try {
            $reflectionClass = new \ReflectionClass($id);
        } catch(\ReflectionException $e) {
            Logger::error("Container: " . $e->getMessage());
            throw new NotFoundException($e->getMessage(), $e->getCode(), $e);
        }

        if (! $reflectionClass->isInstantiable()) {
            Logger::error("Container: the class is not instantiable");

            throw new ContainerException('Class "' . $id . '" is not instantiable');
        }

        $constructor = $reflectionClass->getConstructor();

        if (! $constructor) {
            return new $id;
        }

        $parameters = $constructor->getParameters();

        if (! $parameters) {
            return new $id;
        }

        $dependencies = array_map(
            function (\ReflectionParameter $param) use ($id) {
                $name = $param->getName();
                $type = $param->getType();

                if (! $type) {
                    Logger::error("Container: Failed to resolve class because some params are missing a type hint");

                    throw new ContainerException(
                        'Failed to resolve class "' . $id . '" because param "' . $name . '" is missing a type hint'
                    );
                }

                if ($type instanceof \ReflectionUnionType) {
                    Logger::error("Container: Failed to resolve class because some params are missing a type hint");

                    throw new ContainerException(
                        'Failed to resolve class "' . $id . '" because of union type for param "' . $name . '"'
                    );
                }

                if ($type instanceof \ReflectionNamedType && ! $type->isBuiltin()) {
                    return $this->get($type->getName());
                }

                Logger::error("Container: Failed to resolve class because some params are missing a type hint");

                throw new ContainerException(
                    'Failed to resolve class "' . $id . '" because invalid param "' . $name . '"'
                );
            },
            $parameters
        );

        return $reflectionClass->newInstanceArgs($dependencies);
    }
}