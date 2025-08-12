<?php

declare(strict_types=1);

namespace Constructo\Core\Fake\Resolver;

use Constructo\Core\Fake\Resolver;
use Constructo\Support\Set;
use Constructo\Support\Value;
use ReflectionParameter;

final class FromDefaultValue extends Resolver
{
    public function resolve(ReflectionParameter $parameter, Set $presets): ?Value
    {
        if ($parameter->isDefaultValueAvailable()) {
            return new Value($parameter->getDefaultValue());
        }
        if ($parameter->allowsNull()) {
            return new Value(null);
        }
        return parent::resolve($parameter, $presets);
    }
}
