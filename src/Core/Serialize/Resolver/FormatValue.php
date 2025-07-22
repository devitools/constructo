<?php

declare(strict_types=1);

namespace Constructo\Core\Serialize\Resolver;

use Constructo\Core\Serialize\ResolverTyped;
use Constructo\Support\Set;
use Constructo\Support\Value;
use ReflectionParameter;

use function Constructo\Cast\stringify;

final class FormatValue extends ResolverTyped
{
    public function resolve(ReflectionParameter $parameter, Set $set): Value
    {
        $type = $parameter->getType();
        if ($type === null) {
            return parent::resolve($parameter, $set);
        }

        $expected = stringify($this->formatTypeName($type));
        $formatter = $this->selectFormatter($expected);
        if ($formatter === null) {
            return parent::resolve($parameter, $set);
        }

        $field = $this->casedField($parameter);
        $value = $set->get($field);

        $formatted = $formatter($value, $expected);
        $resolved = $this->resolveReflectionParameterType($type, $formatted);
        if ($resolved) {
            return $resolved;
        }
        return parent::resolve($parameter, $set->with($field, $formatted));
    }
}
