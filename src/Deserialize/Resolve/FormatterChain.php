<?php

declare(strict_types=1);

namespace Morph\Deserialize\Resolve;

use ReflectionParameter;
use Morph\Support\Value;
use Morph\Deserialize\Chain;

class FormatterChain extends Chain
{
    public function resolve(ReflectionParameter $parameter, mixed $value): Value
    {
        $type = $this->detectValueType($value);
        $formatter = $this->selectFormatter($type);
        if ($formatter === null) {
            return parent::resolve($parameter, $value);
        }
        return new Value($formatter($value));
    }
}
