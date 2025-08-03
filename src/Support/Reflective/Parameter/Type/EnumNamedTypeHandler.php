<?php

declare(strict_types=1);

namespace Constructo\Support\Reflective\Parameter\Type;

use Constructo\Core\Metadata\Schema\Field;
use Constructo\Support\Reflective\Parameter\Type\Contract\NamedTypeHandler;
use Constructo\Support\Reflective\Parameter\Type\Contract\NamedTypeResolution;
use BackedEnum;
use ReflectionEnum;
use ReflectionEnumBackedCase;
use ReflectionException;
use ReflectionNamedType;

class EnumNamedTypeHandler extends NamedTypeHandler
{
    /**
     * @throws ReflectionException
     */
    protected function resolveNamedType(ReflectionNamedType $parameter, Field $field): NamedTypeResolution
    {
        $enumClassName = $parameter->getName();
        if (! is_subclass_of($enumClassName, BackedEnum::class)) {
            return NamedTypeResolution::NotResolved;
        }
        return $this->resolveEnumType($enumClassName, $field);
    }

    /**
     * @throws ReflectionException
     */
    private function resolveEnumType(string $enumClassName, Field $field): NamedTypeResolution
    {
        $reflection = new ReflectionEnum($enumClassName);
        $backingType = $reflection->getBackingType();

        if ($backingType instanceof ReflectionNamedType) {
            $cases = $reflection->getCases();
            $this->applyEnumType($field, $backingType, $cases);
            return NamedTypeResolution::Resolved;
        }
        return NamedTypeResolution::NotResolved;
    }

    private function applyEnumType(Field $field, ReflectionNamedType $backingType, array $cases): void
    {
        $type = $this->resolveBuiltinType($backingType->getName());
        $field->{$type}();
        $values = [];
        foreach ($cases as $case) {
            if ($case instanceof ReflectionEnumBackedCase) {
                $values[] = $case->getBackingValue();
            }
        }
        if (empty($values)) {
            return;
        }
        $field->in($values);
    }
}
