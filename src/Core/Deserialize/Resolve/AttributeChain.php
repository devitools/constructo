<?php

declare(strict_types=1);

namespace Constructo\Core\Deserialize\Resolve;

use Constructo\Core\Deserialize\Chain;
use Constructo\Support\Reflective\Attribute\Managed;
use Constructo\Support\Reflective\Attribute\Pattern;
use Constructo\Support\Reflective\AttributeAdapter;
use Constructo\Support\Reflective\Definition\Type;
use Constructo\Support\Reflective\Definition\TypeExtended;
use Constructo\Support\Value;
use Constructo\Type\Timestamp;
use DateMalformedStringException;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use ReflectionNamedType;
use ReflectionParameter;

use function Constructo\Cast\stringify;

class AttributeChain extends Chain
{
    use AttributeAdapter;

    /**
     * @throws DateMalformedStringException
     */
    public function resolve(ReflectionParameter $parameter, mixed $value): Value
    {
        $type = $this->formatTypeName($parameter->getType());
        if ($type === null) {
            return parent::resolve($parameter, $value);
        }
        return $this->resolveByAttributes($parameter, $value)
            ?? parent::resolve($parameter, $value);
    }

    protected function resolveManaged(Managed $instance, mixed $value): ?Value
    {
        return match ($instance->management) {
            'id' => new Value($value),
            'timestamp' => $this->resolveManagedTimestamp($value),
            default => null,
        };
    }

    private function resolveManagedTimestamp(mixed $value): Value
    {
        return new Value(
            match (true) {
                $value instanceof Timestamp => $value->toString(),
                $value instanceof DateTime,
                $value instanceof DateTimeImmutable => $value->format(DateTimeInterface::ATOM),
                default => $value,
            }
        );
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
            $type->demolish(
                $value,
                /* @phpstan-ignore argument.type, argument.templateType */
                fn (object $instance) => $this->demolish($instance)
            )
        );
    }

    protected function resolvePatternFromNamedType(Pattern $instance, mixed $value, ReflectionNamedType $type): ?Value
    {
        return new Value($value);
    }
}
