<?php

declare(strict_types=1);

namespace Morph\Serialize\Resolver;

use ReflectionClass;
use ReflectionException;
use ReflectionParameter;
use Morph\Contract\Collectable;
use Morph\Support\Set;
use Morph\Support\Value;
use Morph\Serialize\Resolver;

use function Serendipity\Type\Cast\arrayify;

class CollectionValue extends Resolver
{
    /**
     * @throws ReflectionException
     */
    public function resolve(ReflectionParameter $parameter, Set $set): Value
    {
        $collectionName = $this->detectCollectionName($parameter);
        if ($collectionName) {
            $field = $this->casedField($parameter);
            $value = $set->get($field);
            return $this->resolveCollection($collectionName, $value);
        }
        return parent::resolve($parameter, $set);
    }

    /**
     * @template T of object
     * @param class-string<T> $collectionName
     * @throws ReflectionException
     */
    private function resolveCollection(string $collectionName, mixed $value): Value
    {
        $reflection = new ReflectionClass($collectionName);
        $type = $this->detectCollectionType($reflection);

        /** @var Collectable $collection */
        $collection = $reflection->newInstance();
        if ($type === null || ! is_array($value)) {
            return new Value($collection);
        }
        foreach ($value as $datum) {
            $datum = arrayify($datum);
            $set = Set::createFrom($datum);
            $instance = $this->build($type, $set);
            $collection->push($instance);
        }
        return new Value($collection);
    }
}
