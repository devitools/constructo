<?php

declare(strict_types=1);

namespace Constructo\Testing;

use InvalidArgumentException;
use ReflectionClass;
use ReflectionException;
use ReflectionParameter;

/**
 * @phpstan-ignore trait.unused
 */
trait MakeExtension
{
    /**
     * @template T of object
     * @param class-string<T> $class
     * @param array<string, mixed> $args
     *
     * @return T
     * @throws ReflectionException
     */
    protected function make(string $class, array $args = []): object
    {
        $reflection = new ReflectionClass($class);
        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            return $reflection->newInstance();
        }

        $orderedArgs = [];
        $position = 0;
        foreach ($constructor->getParameters() as $param) {
            $orderedArgs[] = $this->resolveParameter($param, $args, $class, $position);
            $position++;
        }

        return $reflection->newInstanceArgs($orderedArgs);
    }

    /**
     * @throws ReflectionException
     */
    private function resolveParameter(ReflectionParameter $param, array $args, string $class, int $position): mixed
    {
        $name = $param->getName();

        return match (true) {
            array_key_exists($name, $args) => $args[$name],
            array_key_exists($position, $args) => $args[$position],
            $param->isDefaultValueAvailable() => $param->getDefaultValue(),
            $param->allowsNull() => null,
            default => throw new InvalidArgumentException(
                sprintf('Missing required parameter: $%s for class %s', $name, $class)
            ),
        };
    }
}
