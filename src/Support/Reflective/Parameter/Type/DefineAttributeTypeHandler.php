<?php

declare(strict_types=1);

namespace Constructo\Support\Reflective\Parameter\Type;

use Constructo\Core\Metadata\Schema\Field;
use Constructo\Core\Metadata\Schema\Registry;
use Constructo\Support\Reflective\Attribute\Define;
use Constructo\Support\Reflective\Definition\Type;
use Constructo\Support\Reflective\Definition\TypeExtended;
use Constructo\Support\Reflective\Parameter\Type\Contract\TypeHandler;
use ReflectionAttribute;
use ReflectionParameter;

use function array_shift;
use function assert;

class DefineAttributeTypeHandler extends TypeHandler
{
    public function resolve(ReflectionParameter $parameter, Field $field): void
    {
        $type = $this->resolveDefineAttributeType($parameter, $field->registry);
        if ($type !== null) {
            $field->{$type}();
            return;
        }

        parent::resolve($parameter, $field);
    }

    private function resolveDefineAttributeType(ReflectionParameter $parameter, Registry $specs): ?string
    {
        $type = $this->detecteAttributeType($parameter);
        if ($type === null) {
            return null;
        }
        return $specs->hasSpec($type)
            ? $type
            : null;
    }


    private function detecteAttributeType(ReflectionParameter $parameter): ?string
    {
        $attributes = $parameter->getAttributes(Define::class);
        if (empty($attributes)) {
            return null;
        }

        $attribute = array_shift($attributes);
        assert($attribute instanceof ReflectionAttribute);
        $instance = $attribute->newInstance();

        foreach ($instance->types as $type) {
            $rule = $this->extractRuleFromType($type);
            if ($rule === null) {
                continue;
            }
            return $rule;
        }

        return null;
    }

    private function extractRuleFromType(mixed $type): ?string
    {
        return match (true) {
            $type instanceof Type => $type->name,
            $type instanceof TypeExtended => $type->rule(),
            default => null,
        };
    }
}
