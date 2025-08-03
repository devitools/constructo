<?php

declare(strict_types=1);

namespace Constructo\Testing\Faker\Resolver;

use Constructo\Support\Set;
use Constructo\Support\Value;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionType;
use ReflectionUnionType;
use Constructo\Exception\ManagedException;
use Constructo\Support\Reflective\Attribute\Define;
use Constructo\Support\Reflective\Attribute\Managed;
use Constructo\Support\Reflective\Attribute\Pattern;
use Constructo\Support\Reflective\Definition\Type;
use Constructo\Support\Reflective\Definition\TypeExtended;
use Serendipity\Hyperf\Testing\Extension\MakeExtension;
use Constructo\Testing\Extension\ManagedExtension;
use Constructo\Testing\Faker\Resolver;

final class FromTypeAttributes extends Resolver
{
    use MakeExtension;
    use ManagedExtension;

    /**
     * @throws ManagedException
     */
    public function resolve(ReflectionParameter $parameter, Set $presets): ?Value
    {
        $type = $this->detectReflectionType($parameter->getType());
        if ($type === null) {
            return parent::resolve($parameter, $presets);
        }

        return $this->resolveByAttributes($parameter)
            ?? parent::resolve($parameter, $presets);
    }

    /**
     * @throws ManagedException
     */
    private function resolveByAttributes(ReflectionParameter $parameter): ?Value
    {
        $attributes = $parameter->getAttributes();
        if (empty($attributes)) {
            return null;
        }
        foreach ($attributes as $attribute) {
            $instance = $attribute->newInstance();
            $resolved = match (true) {
                $instance instanceof Managed => $this->resolveManaged($instance),
                $instance instanceof Define => $this->resolveDefine($instance),
                $instance instanceof Pattern => $this->resolvePattern($instance, $parameter->getType()),
                default => null,
            };
            if ($resolved !== null) {
                return $resolved;
            }
        }
        return null;
    }

    /**
     * @throws ManagedException
     */
    private function resolveManaged(Managed $instance): ?Value
    {
        return match ($instance->management) {
            'id' => new Value($this->managed()->id()),
            'timestamp' => new Value($this->managed()->now()),
            default => null,
        };
    }

    private function resolveDefine(Define $instance): ?Value
    {
        $types = $instance->types;
        $callback = fn (?Value $carry, Type|TypeExtended $type): ?Value => $carry ?? match (true) {
            $type instanceof Type => $this->resolveByFormat($type->value),
            $type instanceof TypeExtended => $type->fake($this),
        };
        return array_reduce($types, $callback);
    }

    private function resolvePattern(Pattern $instance, ?ReflectionType $type): ?Value
    {
        return match (true) {
            $type instanceof ReflectionNamedType => $this->resolvePatternFromNamedType($instance, $type),
            $type instanceof ReflectionUnionType => $this->resolvePatternFromUnionType($instance, $type),
            default => null,
        };
    }

    private function resolveByFormat(string $type): Value
    {
        return new Value($this->generator->format($type));
    }

    private function resolvePatternFromNamedType(Pattern $instance, ReflectionNamedType $type): Value
    {
        $content = $this->generator->regexify($instance->pattern);
        $content = match ($type->getName()) {
            'int' => (int) $content,
            'float' => (float) $content,
            default => $content,
        };
        return new Value($content);
    }

    private function resolvePatternFromUnionType(Pattern $instance, ReflectionUnionType $unionType): ?Value
    {
        $types = $unionType->getTypes();
        $value = null;
        foreach ($types as $type) {
            $value = $this->resolvePattern($instance, $type);
            if ($value !== null) {
                break;
            }
        }
        return $value;
    }
}
