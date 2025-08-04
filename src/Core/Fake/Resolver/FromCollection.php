<?php

declare(strict_types=1);

namespace Constructo\Core\Fake\Resolver;

use Constructo\Core\Fake\Resolver;
use Constructo\Support\Set;
use Constructo\Support\Value;
use Constructo\Testing\MakeExtension;
use Constructo\Testing\ManagedExtension;
use Constructo\Type\Collection;
use ReflectionClass;
use ReflectionException;
use ReflectionParameter;

final class FromCollection extends Resolver
{
    use MakeExtension;
    use ManagedExtension;

    /**
     * @throws ReflectionException
     */
    public function resolve(ReflectionParameter $parameter, Set $presets): ?Value
    {
        $collectionName = $this->detectCollectionName($parameter);
        if ($collectionName) {
            return $this->resolveCollection($collectionName);
        }
        return parent::resolve($parameter, $presets);
    }

    /**
     * @param class-string<Collection> $collectionName
     * @throws ReflectionException
     */
    private function resolveCollection(string $collectionName): Value
    {
        $reflection = new ReflectionClass($collectionName);
        $type = $this->detectCollectionType($reflection);

        return new Value($type === null ? [] : $this->resolveCollectionFake($type));
    }

    /**
     * @param class-string<object> $type
     * @throws ReflectionException
     */
    private function resolveCollectionFake(string $type): array
    {
        $total = $this->generator->numberBetween(1, 5);
        return array_map(fn () => $this->fake($type)->toArray(), range(1, $total));
    }
}
