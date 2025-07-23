<?php

declare(strict_types=1);

namespace Constructo\Support\Reflective\Factory\Ruler;

use Constructo\Support\Reflective\Attribute\Define;
use Constructo\Support\Reflective\Attribute\Pattern;
use Constructo\Support\Reflective\Definition\Type;
use Constructo\Support\Reflective\Definition\TypeExtended;
use Constructo\Support\Reflective\Factory\Chain;
use Constructo\Support\Reflective\Ruleset;
use ReflectionAttribute;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionType;
use ReflectionUnionType;

use function Constructo\Cast\boolify;
use function Constructo\Notation\snakify;

class AttributeChain extends Chain
{
    public function resolve(ReflectionParameter $parameter, Ruleset $rules): Ruleset
    {
        $field = $this->dottedField($parameter);
        $type = $parameter->getType();

        $attributes = $parameter->getAttributes();
        foreach ($attributes as $attribute) {
            $this->resolveAttribute($attribute, $type, $field, $rules);
        }

        return parent::resolve($parameter, $rules);
    }

    private function resolveAttribute(
        ReflectionAttribute $attribute,
        ?ReflectionType $type,
        string $field,
        Ruleset $rules
    ): void {
        $instance = $attribute->newInstance();
        match (true) {
            $instance instanceof Pattern => $this->resolveAttributePattern($instance, $type, $field, $rules),
            $instance instanceof Define => $this->resolveAttributeDefine($instance, $field, $rules),
            default => null,
        };
    }

    private function resolveAttributePattern(
        Pattern $instance,
        ?ReflectionType $type,
        string $field,
        Ruleset $rules
    ): ?bool {
        return match (true) {
            $type instanceof ReflectionNamedType && $type->isBuiltin() => match ($type->getName()) {
                'string', 'int', 'float' => $rules->add($field, 'regex', $instance->pattern),
                default => null,
            },
            $type instanceof ReflectionUnionType => $this->resolveAttributePatternUnion(
                $instance,
                $field,
                $rules,
                $type
            ),
            default => null,
        };
    }

    private function resolveAttributePatternUnion(
        Pattern $instance,
        string $field,
        Ruleset $rules,
        ReflectionUnionType $type
    ): bool {
        $done = false;
        foreach ($type->getTypes() as $unionType) {
            $done = $this->resolveAttributePattern($instance, $unionType, $field, $rules);
            if ($done) {
                break;
            }
        }
        return boolify($done);
    }

    private function resolveAttributeDefine(
        Define $instance,
        string $field,
        Ruleset $rules
    ): void {
        foreach ($instance->types as $type) {
            $this->resolveAttributeDefineType($type, $rules, $field);
        }
    }

    private function resolveAttributeDefineType(Type|TypeExtended $type, Ruleset $rules, string $field): void
    {
        if ($type instanceof TypeExtended) {
            return;
        }
        $rules->add($field, snakify($type->name));
    }
}
