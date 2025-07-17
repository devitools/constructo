<?php

declare(strict_types=1);

namespace Morph\Deserialize\Resolve;

use ReflectionParameter;
use Morph\Support\Value;
use Morph\Deserialize\Chain;

class DoNothingChain extends Chain
{
    public function resolve(ReflectionParameter $parameter, mixed $value): Value
    {
        return new Value($value);
    }
}
