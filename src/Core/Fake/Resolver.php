<?php

declare(strict_types=1);

namespace Constructo\Core\Fake;

use Constructo\Support\Set;
use Constructo\Support\Value;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionType;
use ReflectionUnionType;

use function count;

abstract class Resolver extends Faker
{
    protected ?Resolver $previous = null;

    final public function then(?Resolver $resolver): Resolver
    {
        if ($resolver === null) {
            return $this;
        }
        $resolver->previous($this);
        return $resolver;
    }

    public function resolve(ReflectionParameter $parameter, Set $presets): ?Value
    {
        if (isset($this->previous)) {
            return $this->previous->resolve($parameter, $presets);
        }
        return null;
    }

    final protected function previous(Resolver $previous): void
    {
        $this->previous = $previous;
    }

    protected function detectReflectionType(?ReflectionType $type): ?string
    {
        if ($type instanceof ReflectionNamedType) {
            return $type->getName();
        }
        if ($type instanceof ReflectionUnionType) {
            $reflectionNamedTypes = $type->getTypes();
            $index = $this->generator->numberBetween(0, count($reflectionNamedTypes) - 1);
            return $this->detectReflectionType($reflectionNamedTypes[$index]);
        }
        return null;
    }
}
