<?php

declare(strict_types=1);

namespace Constructo\Core\Serialize\Resolver;

use Constructo\Core\Serialize\ResolverTyped;
use Constructo\Support\Set;
use Constructo\Support\Value;
use ReflectionParameter;

use function Constructo\Cast\stringify;

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
