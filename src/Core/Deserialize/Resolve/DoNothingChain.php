<?php

declare(strict_types=1);

namespace Constructo\Core\Deserialize\Resolve;

use Constructo\Core\Deserialize\Chain;
use Constructo\Support\Value;
use ReflectionParameter;

class DoNothingChain extends Chain
{
    public function resolve(ReflectionParameter $parameter, mixed $value): Value
    {
        return new Value($value);
    }
}
