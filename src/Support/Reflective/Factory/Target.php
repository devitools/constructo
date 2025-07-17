<?php

declare(strict_types=1);

namespace Morph\Support\Reflective\Factory;

use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionParameter;

/**
 * @template T of object
 */
final readonly class Target
{
    private function __construct(
        /**
         * @var ReflectionClass<T> $reflection
         */
        private ReflectionClass $reflection,
        /**
         * @var array<ReflectionParameter> $reflectionParameters
         */
        private array $reflectionParameters = [],
    ) {
    }

    /**
     * @template U of object
     * @param class-string<U> $class
     * @return Target<U>
     * @throws ReflectionException
     */
    public static function createFrom(string $class): Target
    {
        $reflection = new ReflectionClass($class);
        $constructor = $reflection->getConstructor();
        return new Target($reflection, self::extractReflectionParameters($constructor));
    }

    /**
     * @return ReflectionClass<T>
     */
    public function getReflectionClass(): ReflectionClass
    {
        return $this->reflection;
    }

    /**
     * @return array<ReflectionParameter>
     */
    public function getReflectionParameters(): array
    {
        return $this->reflectionParameters;
    }

    /**
     * @return array<ReflectionParameter>
     */
    private static function extractReflectionParameters(?ReflectionMethod $constructor): array
    {
        if ($constructor === null) {
            return [];
        }
        return $constructor->getParameters();
    }
}
