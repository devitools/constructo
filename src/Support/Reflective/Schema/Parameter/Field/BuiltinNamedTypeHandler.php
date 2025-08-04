<?php

declare(strict_types=1);

namespace Constructo\Support\Reflective\Schema\Parameter\Field;

use Constructo\Core\Metadata\Schema\Field;
use Constructo\Support\Reflective\Schema\Parameter\Field\Contract\NamedTypeHandler;
use Constructo\Support\Reflective\Schema\Parameter\Field\Contract\NamedTypeResolution;
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
