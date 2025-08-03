<?php

declare(strict_types=1);

namespace Constructo\Testing\Faker\Resolver;

use Constructo\Support\Set;
use Constructo\Support\Value;
use ReflectionException;
use ReflectionParameter;
use Constructo\Testing\Faker\Resolver;

final class FromDefaultValue extends Resolver
{
    /**
     * @throws ReflectionException
     */
    public function resolve(ReflectionParameter $parameter, Set $presets): ?Value
    {
        if ($parameter->isOptional() || $parameter->isDefaultValueAvailable()) {
            return new Value($parameter->getDefaultValue());
        }
        if ($parameter->allowsNull()) {
            return new Value(null);
        }
        return parent::resolve($parameter, $presets);
    }
}
