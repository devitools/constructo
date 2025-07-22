<?php

declare(strict_types=1);

namespace Constructo\Core\Serialize\Resolver;

use Constructo\Core\Serialize\ResolverTyped;
use Constructo\Exception\AdapterException;
use Constructo\Support\Reflective\Factory\Target;
use Constructo\Support\Set;
use Constructo\Support\Value;
use ReflectionException;
use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionType;
use ReflectionUnionType;

use function array_key_exists;
use function class_exists;
use function count;
use function enum_exists;
use function Constructo\Cast\arrayify;

final class DependencyValue extends ResolverTyped
{
    /**
     * @throws ReflectionException
     */
    public function resolve(ReflectionParameter $parameter, Set $set): Value
    {
        $field = $this->casedField($parameter);
        if (! $set->has($field)) {
            return parent::resolve($parameter, $set);
        }
        return $this->resolveDependencyValue($parameter, $set, $field);
    }

    /**
     * @throws ReflectionException
     */
    protected function resolveReflectionParameterType(?ReflectionType $type, mixed $value): ?Value
    {
        return match (true) {
            $type instanceof ReflectionNamedType => $this->resolveNamedType($type, $value),
            $type instanceof ReflectionUnionType => $this->resolveUnionType($type, $value),
            $type instanceof ReflectionIntersectionType => $this->resolveIntersectionType($type, $value),
            default => null,
        };
    }

    /**
     * @throws ReflectionException
     */
    protected function resolveNamedType(ReflectionNamedType $type, mixed $value): ?Value
    {
        $builtin = $type->isBuiltin();
        $class = $type->getName();
        if (! class_exists($class) || $this->isNotResolvable($class, $builtin)) {
            return null;
        }
        if ($value instanceof $class) {
            return new Value($value);
        }
        return $this->resolveNamedTypeClass($class, $value);
    }

    /**
     * @throws ReflectionException
     */
    private function resolveDependencyValue(ReflectionParameter $parameter, Set $set, string $field): Value
    {
        $type = $parameter->getType();
        $value = $set->get($field);
        $resolved = $this->resolveReflectionParameterType($type, $value);
        return $resolved ?? parent::resolve($parameter, $set);
    }

    /**
     * @param array<ReflectionParameter> $parameters
     */
    private function convertValueToSet(array $parameters, mixed $value): Set
    {
        $input = arrayify($value, [$value]);
        $values = [];
        foreach ($parameters as $index => $parameter) {
            $name = $this->casedField($parameter);
            if (array_key_exists($name, $input)) {
                $values[$name] = $input[$name];
                continue;
            }
            if (array_key_exists($index, $input)) {
                $values[$name] = $input[$index];
            }
        }
        return Set::createFrom($values);
    }

    /**
     * @template T of object
     * @param class-string<T> $class
     * @throws ReflectionException
     */
    private function resolveNamedTypeClass(string $class, mixed $value): ?Value
    {
        $parameters = Target::createFrom($class)->getReflectionParameters();
        if ($value !== null && count($parameters) === 0) {
            return null;
        }
        $set = $this->convertValueToSet($parameters, $value);
        try {
            $content = $this->make($class, $set, $this->path);
            return new Value($content);
        } catch (AdapterException $exception) {
            return $this->notResolved($exception->getUnresolved(), $value);
        }
    }

    private function isNotResolvable(string $class, bool $builtin): bool
    {
        return $builtin || enum_exists($class);
    }
}
