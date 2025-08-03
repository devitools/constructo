<?php

declare(strict_types=1);

namespace Constructo\Support\Reflective\Parameter\Type\Contract;

use Constructo\Core\Metadata\Schema\Field;
use ReflectionNamedType;
use ReflectionParameter;

abstract class NamedTypeHandler extends TypeHandler
{
    protected const array BUILTIN_TYPE_MAPPING = [
        'int' => 'integer',
        'float' => 'numeric',
        'double' => 'numeric',
        'bool' => 'boolean',
        'iterable' => 'array',
        'object' => 'array',
    ];

    public function resolve(ReflectionParameter $parameter, Field $field): void
    {
        $resolution = $this->handleNamedType($parameter, $field);
        if ($resolution === NamedTypeResolution::Resolved) {
            return;
        }
        parent::resolve($parameter, $field);
    }

    abstract protected function resolveNamedType(ReflectionNamedType $parameter, Field $field): NamedTypeResolution;

    private function handleNamedType(ReflectionParameter $parameter, Field $field): NamedTypeResolution
    {
        $type = $parameter->getType();
        if ($type instanceof ReflectionNamedType) {
            return $this->resolveNamedType($type, $field);
        }
        return NamedTypeResolution::NotResolved;
    }

    protected function resolveBuiltinType(string $type): ?string
    {
        if (isset(self::BUILTIN_TYPE_MAPPING[$type])) {
            return self::BUILTIN_TYPE_MAPPING[$type];
        }

        $regular = [
            'string',
            'array',
        ];
        if (in_array($type, $regular, true)) {
            return $type;
        }

        return null;
    }
}
