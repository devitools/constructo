<?php

declare(strict_types=1);

namespace Constructo\Core\Reflect\Resolver\Type;

use Constructo\Core\Reflect\Resolver\Type\Contract\NamedTypeHandler;
use Constructo\Core\Reflect\Resolver\Type\Contract\NamedTypeResolution;
use Constructo\Support\Metadata\Schema\Field;
use ReflectionNamedType;

use function is_string;

class BuiltinNamedTypeHandler extends NamedTypeHandler
{
    protected function resolveNamedType(ReflectionNamedType $parameter, Field $field): NamedTypeResolution
    {
        if (! $parameter->isBuiltin()) {
            return NamedTypeResolution::NotResolved;
        }
        $type = $this->resolveBuiltinType($parameter->getName());
        if (is_string($type) && $field->specs->has($type)) {
            $field->{$type}();
        }
        return NamedTypeResolution::Resolved;
    }
}
