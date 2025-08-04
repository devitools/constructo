<?php

declare(strict_types=1);

namespace Constructo\Support\Reflective\Schema\Parameter\Field;

use Constructo\Core\Metadata\Schema\Field;
use Constructo\Support\Reflective\Schema\Parameter\Field\Contract\NamedTypeHandler;
use Constructo\Support\Reflective\Schema\Parameter\Field\Contract\NamedTypeResolution;
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
        $type = $this->types->get($source);
        if (is_string($type)) {
            $field->{$type}();
            return;
        }
        $field->array();
        $field->setSource($source);
    }
}
