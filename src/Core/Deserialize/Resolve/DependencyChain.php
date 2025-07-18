<?php

declare(strict_types=1);

namespace Morph\Core\Deserialize\Resolve;

use BackedEnum;
use Morph\Core\Deserialize\Chain;
use Morph\Support\Value;
use ReflectionException;
use ReflectionParameter;

use function is_object;

class DependencyChain extends Chain
{
    /**
     * @throws ReflectionException
     */
    public function resolve(ReflectionParameter $parameter, mixed $value): Value
    {
        if (! is_object($value)) {
            return parent::resolve($parameter, $value);
        }
        if ($value instanceof BackedEnum) {
            return new Value($value->value);
        }
        return new Value((array) $this->demolish($value));
    }
}
