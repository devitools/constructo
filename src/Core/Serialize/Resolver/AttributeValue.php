<?php

declare(strict_types=1);

namespace Constructo\Core\Serialize\Resolver;

use Constructo\Core\Serialize\ResolverTyped;
use Constructo\Support\Reflective\Attribute\Managed;
use Constructo\Support\Reflective\Attribute\Pattern;
use Constructo\Support\Reflective\AttributeAdapter;
use Constructo\Support\Reflective\Definition\Type;
use Constructo\Support\Reflective\Definition\TypeExtended;
use Constructo\Support\Set;
use Constructo\Support\Value;
use Constructo\Type\Timestamp;
use DateMalformedStringException;
use ReflectionNamedType;
use ReflectionParameter;

use function Constructo\Cast\stringify;

/**
 * @SuppressWarnings(ExcessiveClassLength)
 */
final class AttributeValue extends ResolverTyped
{
    use AttributeAdapter;

    /**
     * @throws DateMalformedStringException
     */
    public function resolve(ReflectionParameter $parameter, Set $set): Value
    {
        $type = $this->formatTypeName($parameter->getType());
        if ($type === null) {
            return parent::resolve($parameter, $set);
        }
        $field = $this->casedField($parameter);
        $value = $set->get($field);
        return $this->resolveByAttributes($parameter, $value)
            ?? parent::resolve($parameter, $set);
    }

    /**
     * @throws DateMalformedStringException
     */
    protected function resolveManaged(Managed $instance, mixed $value): ?Value
    {
        return match ($instance->management) {
            'id' => new Value($value),
            'timestamp' => new Value(new Timestamp(stringify($value))),
            default => null,
        };
    }

    protected function resolvePatternFromNamedType(Pattern $instance, mixed $value, ReflectionNamedType $type): ?Value
    {
        $value = stringify($value);
        if (preg_match($instance->pattern, $value) !== 1) {
            return null;
        }
        $name = $type->getName();
        $content = match ($name) {
            'int' => (int) $value,
            'float' => (float) $value,
            default => $value,
        };
        return new Value($content);
    }

    protected function resolveDefineType(Type $type, mixed $value): Value
    {
        $content = match ($type) {
            Type::EMOJI => stringify($value),
            default => $value,
        };
        return new Value($content);
    }

    protected function resolveDefineTypeExtended(TypeExtended $type, mixed $value): Value
    {
        return new Value(
            $type->build(
                $value,
                /* @phpstan-ignore argument.type, argument.templateType */
                fn (string $class, Set $set) => $this->build($class, $set, $this->path)
            )
        );
    }
}
