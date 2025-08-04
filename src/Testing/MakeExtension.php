<?php

declare(strict_types=1);

namespace Constructo\Testing;

use ReflectionClass;
use ReflectionException;

/**
 * @phpstan-ignore trait.unused
 */
trait MakeExtension
{
    /**
     * @template T of mixed
     * @param class-string<T> $class
     * @param array<string, mixed> $args
     *
     * @return T
     * @throws ReflectionException
     */
    protected function make(string $class, array $args = []): mixed
    {
        return (new ReflectionClass($class))->newInstanceArgs(array_values($args));
    }
}
