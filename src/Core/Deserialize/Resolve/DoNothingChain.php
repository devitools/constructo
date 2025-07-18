<?php

declare(strict_types=1);

namespace Morph\Core\Deserialize\Resolve;

use Morph\Core\Deserialize\Chain;
use Morph\Support\Value;
use ReflectionParameter;

class DoNothingChain extends Chain
{
    public function resolve(ReflectionParameter $parameter, mixed $value): Value
    {
        return new Value($value);
    }
}
