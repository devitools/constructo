<?php

declare(strict_types=1);

namespace Constructo\Core\Reflect;

use Constructo\Core\Reflect\Resolver\Field\ManagedChain;
use Constructo\Core\Reflect\Resolver\Field\RequirementChain;
use Constructo\Core\Reflect\Resolver\Field\TypeChain;
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
        protected readonly SchemaFactory $factory,
        protected readonly Types $types,
        protected readonly Cache $cache,
        protected readonly Notation $notation = Notation::SNAKE,
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
        $chain = (new TypeChain($this->types))
            ->then(new RequirementChain($parent))
            ->then(new ManagedChain());

        foreach ($parameters as $parameter) {
            assert($parameter instanceof ReflectionParameter);
            $nestedPath = [
                ...$path,
                format($parameter->getName(), $this->notation),
            ];
            $name = implode('.', $nestedPath);

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
