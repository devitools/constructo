<?php

declare(strict_types=1);

namespace Morph\Core\Deserialize\Resolve;

use Morph\Contract\Collectable;
use Morph\Core\Deserialize\Chain;
use Morph\Support\Value;
use ReflectionClass;
use ReflectionException;
use ReflectionParameter;

use function Morph\Cast\stringify;

class CollectionChain extends Chain
{
    /**
     * @throws ReflectionException
     */
    public function resolve(ReflectionParameter $parameter, mixed $value): Value
    {
        $candidate = $this->detectCollectionName($parameter);
        $className = stringify($candidate);
        if (! $candidate || ! $value instanceof Collectable) {
            return parent::resolve($parameter, $value);
        }
        return $this->resolveCollection($parameter, $className, $value);
    }

    /**
     * @param Collectable|class-string<Collectable> $className
     * @throws ReflectionException
     */
    private function resolveCollection(
        ReflectionParameter $parameter,
        Collectable|string $className,
        Collectable $value
    ): Value {
        $reflection = new ReflectionClass($className);
        if (! $reflection->isInstance($value)) {
            return parent::resolve($parameter, $value);
        }
        return $this->resolveCollectionValue($value);
    }

    private function resolveCollectionValue(Collectable $value): Value
    {
        return new Value($value->map(fn (object $instance): array => (array) $this->demolish($instance)));
    }
}
