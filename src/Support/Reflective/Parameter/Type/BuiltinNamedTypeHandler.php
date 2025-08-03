<?php

declare(strict_types=1);

namespace Constructo\Support\Reflective\Parameter\Type;

use Constructo\Core\Metadata\Schema\Field;
use Constructo\Support\Reflective\Parameter\Type\Contract\NamedTypeHandler;
use Constructo\Support\Reflective\Parameter\Type\Contract\NamedTypeResolution;
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
