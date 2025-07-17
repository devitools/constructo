<?php

declare(strict_types=1);

namespace Morph\Serialize;

use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionType;
use ReflectionUnionType;
use Morph\Support\Value;

abstract class ResolverTyped extends Resolver
{
    protected function resolveReflectionParameterType(?ReflectionType $type, mixed $value): ?Value
    {
        return match (true) {
            $type instanceof ReflectionNamedType => $this->resolveNamedType($type, $value),
            $type instanceof ReflectionUnionType => $this->resolveUnionType($type, $value),
            $type instanceof ReflectionIntersectionType => $this->resolveIntersectionType($type, $value),
            default => new Value($value),
        };
    }

    protected function resolveNamedType(ReflectionNamedType $type, mixed $value): ?Value
    {
        if ($value === null && $type->allowsNull()) {
            return new Value($value);
        }
        $builtin = $type->isBuiltin();
        $actual = $type->getName();
        return ($builtin)
            ? $this->resolveNamedTypeBuiltin($actual, $value)
            : $this->resolveNamedTypeInstanceOf($actual, $value);
    }

    protected function resolveNamedTypeBuiltin(string $expected, mixed $value): ?Value
    {
        if ($expected === 'mixed') {
            return new Value($value);
        }
        $actual = $this->detectValueType($value);
        return ($expected === $actual)
            ? new Value($value)
            : null;
    }

    protected function resolveNamedTypeInstanceOf(string $actual, mixed $value): ?Value
    {
        return ($value instanceof $actual)
            ? new Value($value)
            : null;
    }

    protected function resolveUnionType(ReflectionUnionType $type, mixed $value): ?Value
    {
        $types = $type->getTypes();
        foreach ($types as $nested) {
            $resolved = $this->resolveReflectionParameterType($nested, $value);
            if ($resolved !== null) {
                return $resolved;
            }
        }
        return null;
    }

    protected function resolveIntersectionType(ReflectionIntersectionType $type, mixed $value): ?Value
    {
        $types = $type->getTypes();
        foreach ($types as $nested) {
            if ($this->resolveReflectionParameterType($nested, $value) === null) {
                return null;
            }
        }
        return new Value($value);
    }
}
