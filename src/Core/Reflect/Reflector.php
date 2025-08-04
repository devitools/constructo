<?php

declare(strict_types=1);

namespace Constructo\Core\Reflect;

use Constructo\Core\Reflect\Introspection\Introspector;
use Constructo\Core\Reflect\Resolver\ManagedResolver;
use Constructo\Core\Reflect\Resolver\RequirementResolver;
use Constructo\Core\Reflect\Resolver\TypeResolver;
use Constructo\Factory\SchemaFactory;
use Constructo\Support\Cache;
use Constructo\Support\Metadata\Schema;
use Constructo\Support\Metadata\Schema\Field;
use Constructo\Support\Metadata\Schema\Registry\Types;
use Constructo\Support\Reflective\Factory\Target;
use Constructo\Support\Reflective\Notation;
use ReflectionException;
use ReflectionParameter;

use function array_pop;
use function assert;
use function Constructo\Notation\format;
use function implode;
use function in_array;
use function is_array;
use function sprintf;

class Reflector
{
    private array $sources = [];

    public function __construct(
        private readonly SchemaFactory $factory,
        private readonly Types $types,
        private readonly Cache $cache,
        private readonly Introspector $introspector,
        private readonly Notation $notation = Notation::SNAKE,
        private readonly string $conector = '.',
        private readonly string $expansor = '*',
    ) {
    }

    /**
     * @template T of object
     * @param class-string<T> $source
     * @throws ReflectionException
     */
    public function reflect(string $source): Schema
    {
        $this->cache->reset();
        $this->sources = [];

        $parameters = $this->extractParameters($source);
        $schema = $this->factory->make();
        $this->introspect($parameters, $schema);

        return $schema;
    }

    /**
     * @throws ReflectionException
     */
    protected function introspect(array $parameters, Schema $schema, ?Field $parent = null, array $path = []): void
    {
        $chain = (new TypeResolver($this->types))
            ->then(new RequirementResolver($parent))
            ->then(new ManagedResolver());

        foreach ($parameters as $parameter) {
            assert($parameter instanceof ReflectionParameter);
            $nestedPath = [
                ...$path,
                format($parameter->getName(), $this->notation),
            ];
            $name = implode($this->conector, $nestedPath);

            $field = $schema->add($name);
            $chain->resolve($parameter, $field, $nestedPath);

            $source = $field->getSource();
            if ($source === null || $this->wouldCauseCircularReference($source)) {
                continue;
            }

            $this->introspectSource($source, $schema, $field, $nestedPath);
        }
    }

    /**
     * @throws ReflectionException
     */
    private function introspectSource(string $source, Schema $schema, Field $parent, array $path): void
    {
        $result = $this->introspector->analyze($source);
        $introspection = $result->introspectable();
        if ($introspection !== null) {
            $source = $introspection;
            $path = [...$path, $this->expansor];
        }

        $nestedParameters = $this->extractParameters($source);
        if (empty($nestedParameters)) {
            return;
        }

        $this->sources[] = $source;
        $this->introspect($nestedParameters, $schema, $parent, $path);
        array_pop($this->sources);
    }

    private function wouldCauseCircularReference(string $source): bool
    {
        return in_array($source, $this->sources, true);
    }

    /**
     * @throws ReflectionException
     */
    private function extractParameters(string $source): array
    {
        $key = sprintf("parameters:%s", $source);
        $parameters = $this->cache->get($key);
        if (is_array($parameters)) {
            return $parameters;
        }
        $parameters = Target::createFrom($source)
            ->getReflectionParameters();
        return $this->cache->set($key, $parameters);
    }
}
