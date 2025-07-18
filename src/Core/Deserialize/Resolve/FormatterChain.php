<?php

declare(strict_types=1);

namespace Morph\Core\Deserialize\Resolve;

use Morph\Core\Deserialize\Chain;
use Morph\Support\Value;
use ReflectionParameter;

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
