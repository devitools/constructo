<?php

declare(strict_types=1);

namespace Constructo\Support\Reflective\Parameter\Type;

use Constructo\Core\Metadata\Schema\Field;
use Constructo\Support\Reflective\Parameter\Type\Contract\NamedTypeHandler;
use Constructo\Support\Reflective\Parameter\Type\Contract\NamedTypeResolution;
use ReflectionNamedType;

use function class_exists;
use function enum_exists;
use function is_string;

class DependencyTypeHandler extends NamedTypeHandler
{
    protected function resolveNamedType(ReflectionNamedType $parameter, Field $field): NamedTypeResolution
    {
        $source = $parameter->getName();
        if (class_exists($source) && ! enum_exists($source)) {
            $this->resolveDynamicType($field, $source);
            return NamedTypeResolution::Resolved;
        }
        return NamedTypeResolution::NotResolved;
    }

    private function resolveDynamicType(Field $field, string $source): void
    {
        $type = $this->specs->type($source);
        if (is_string($type)) {
            $field->{$type}();
            return;
        }
        $field->array();
        $field->setSource($source);
    }
}
