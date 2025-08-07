<?php

declare(strict_types=1);

namespace Constructo\Core\Deserialize\Resolve;

use Constructo\Contract\Collectable;
use Constructo\Core\Deserialize\Chain;
use Constructo\Support\Value;
use ReflectionClass;
use ReflectionException;
use ReflectionParameter;

use function Constructo\Cast\stringify;
use function is_subclass_of;

class CollectionChain extends Chain
{
    /**
     * @throws ReflectionException
     */
    public function resolve(ReflectionParameter $parameter, mixed $value): Value
    {
        $candidate = $this->detectCollectionName($parameter);
        if ($candidate === null || ! $value instanceof Collectable) {
            return parent::resolve($parameter, $value);
        }
        return $this->resolveCollection($parameter, $candidate, $value);
    }

    /**
     * @param class-string<Collectable> $className
     * @throws ReflectionException
     */
    private function resolveCollection(
        ReflectionParameter $parameter,
        string $className,
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
