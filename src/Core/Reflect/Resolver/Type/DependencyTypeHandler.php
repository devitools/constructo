<?php

declare(strict_types=1);

namespace Constructo\Core\Reflect\Resolver\Type;

use Constructo\Core\Reflect\Resolver\Type\Contract\NamedTypeHandler;
use Constructo\Core\Reflect\Resolver\Type\Contract\NamedTypeResolution;
use Constructo\Support\Metadata\Schema\Field;
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

    /**
     * @param class-string<object> $source
     */
    private function resolveDynamicType(Field $field, string $source): void
    {
        if ($this->types === null) {
            $field->array();
            $field->setSource($source);
            return;
        }

        $type = $this->types->get($source);
        if (is_string($type)) {
            $field->{$type}();
            return;
        }
        $field->array();
        $field->setSource($source);
    }
}
