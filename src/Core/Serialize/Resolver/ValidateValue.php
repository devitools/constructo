<?php

declare(strict_types=1);

namespace Morph\Core\Serialize\Resolver;

use Morph\Core\Serialize\ResolverTyped;
use Morph\Support\Set;
use Morph\Support\Value;
use ReflectionParameter;

use function Morph\Cast\stringify;

final class ValidateValue extends ResolverTyped
{
    public function resolve(ReflectionParameter $parameter, Set $set): Value
    {
        $field = $this->casedField($parameter);
        if ($set->hasNot($field)) {
            return $this->notResolvedAsRequired();
        }

        $type = $parameter->getType();
        $value = $set->get($field);
        $resolved = $this->resolveReflectionParameterType($type, $value);
        return $resolved
            ?? $this->notResolvedAsTypeMismatch(
                stringify($this->formatTypeName($type)),
                $this->detectValueType($value),
                $value,
            );
    }
}
