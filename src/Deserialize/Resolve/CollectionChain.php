<?php

declare(strict_types=1);

namespace Morph\Deserialize\Resolve;

use ReflectionClass;
use ReflectionException;
use ReflectionParameter;
use Morph\Collection\Collection;
use Morph\Support\Value;
use Morph\Deserialize\Chain;

use function Serendipity\Type\Cast\stringify;

class CollectionChain extends Chain
{
    /**
     * @throws ReflectionException
     */
    public function resolve(ReflectionParameter $parameter, mixed $value): Value
    {
        $candidate = $this->detectCollectionName($parameter);
        $className = stringify($candidate);
        if (! $candidate || ! is_subclass_of($className, Collection::class) || ! $value instanceof Collection) {
            return parent::resolve($parameter, $value);
        }
        return $this->resolveCollection($parameter, $className, $value);
    }

    /**
     * @param Collection|class-string<Collection> $className
     * @throws ReflectionException
     */
    private function resolveCollection(
        ReflectionParameter $parameter,
        Collection|string $className,
        Collection $value
    ): Value {
        $reflection = new ReflectionClass($className);
        if (! $reflection->isInstance($value)) {
            return parent::resolve($parameter, $value);
        }
        return $this->resolveCollectionValue($value);
    }

    private function resolveCollectionValue(Collection $value): Value
    {
        return new Value($value->map(fn (object $instance): array => (array) $this->demolish($instance)));
    }
}
