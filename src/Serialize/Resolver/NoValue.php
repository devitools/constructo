<?php

declare(strict_types=1);

namespace Morph\Serialize\Resolver;

use ReflectionException;
use ReflectionParameter;
use Morph\Support\Set;
use Morph\Support\Value;
use Morph\Serialize\Resolver;

final class NoValue extends Resolver
{
    /**
     * @throws ReflectionException
     */
    public function resolve(ReflectionParameter $parameter, Set $set): Value
    {
        $name = $this->casedField($parameter);
        if ($set->has($name)) {
            return parent::resolve($parameter, $set);
        }
        return $this->resolveNoValue($parameter);
    }

    /**
     * @throws ReflectionException
     */
    public function resolveNoValue(ReflectionParameter $parameter): Value
    {
        if ($parameter->isOptional() || $parameter->isDefaultValueAvailable()) {
            return new Value($parameter->getDefaultValue());
        }
        if ($parameter->allowsNull()) {
            return new Value(null);
        }
        return $this->notResolvedAsRequired();
    }
}
