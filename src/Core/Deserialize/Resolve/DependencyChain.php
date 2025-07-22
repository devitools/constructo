<?php

declare(strict_types=1);

namespace Constructo\Core\Deserialize\Resolve;

use BackedEnum;
use Constructo\Core\Deserialize\Chain;
use Constructo\Support\Value;
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
